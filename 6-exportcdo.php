<?php
require_once("../../config.php");
require_once("../../libs/eloqua-php-request-master/eloquaRequest.php");
$elq = new EloquaRequest($site,$username,$password,"https://secure.eloqua.com/API/Bulk/2.0");

$id = 310;
$limit = 100;
//retrieve CDO fields

$metadata = $elq->get("customObjects/".intval($id)."/fields?page=1&depth=minimal");
foreach ($metadata->items as $f) {
	$fields[$f->internalName] = $f->statement;
}

//build definition
$definition = array("name" => "Custom Object export",
                    "secondsToRetainData" => 3600,
								    "fields" => $fields);
										
//create export definition
$export = $elq->post("/customObjects/".$id."/exports",$definition);

//start sync
$sync_start = $elq->post("/syncs",array("syncedInstanceUri" => $export->uri));

//retrieve status once, and keep checking every 30 secs
$sync = $elq->get($sync_start->uri);
while ($sync->status == "pending" || $sync->status == "active") {
	$i++;
	sleep(10);
	$sync = $elq->get($sync_start->uri);
	if ($i == 60) die("Timeout");
}

//sync done; create csv file handle...
$tmp = tempnam(sys_get_temp_dir(),"bulkex");
$handle = fopen($tmp,"w");

//...and download the data
$data = $elq->get($sync->syncedInstanceUri."/data?offset=0&limit=".$limit);

while (!empty($data->items)) { //keep downloading while we get data
	foreach ($data->items as $obj) {
		$arr = (array) $obj;
		if ($headers_set != TRUE) { //build CSV header
			fputcsv($handle,array_keys($arr));
			$headers_set = TRUE;
		}
		fputcsv($handle,array_values($arr));
	}
	
	$j++; if ($j == 1000) break; //timeout
	$data = $elq->get($sync->syncedInstanceUri."/data?offset=".($j * $limit)."&limit=".$limit); //keep downloading data
}

fclose($handle);
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header("Content-Disposition: attachment; filename=".$definition['name'].".csv");
readfile($tmp);
?>