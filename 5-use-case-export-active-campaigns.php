<?php
require_once("../../config.php");
require_once("../../libs/eloqua-php-request-master/eloquaRequest.php");
$elq = new EloquaRequest($site,$username,$password,"https://secure.eloqua.com/API/REST/1.0");

$tmp = tempnam(sys_get_temp_dir(),"camps");
$handle = fopen($tmp,"w");
fputcsv($handle,array("Campaign ID","Campaign Name","Created date")); //build our CSV headers

$count = 500;
$data = $elq->get('/assets/campaigns?depth=minimal&search=*&count='.$count.'&page=1'); //first call
$i = 1;

while (count($data->elements) > 0) { //keep calling API until results are exhausted
  $i++;
  
  foreach ($data->elements as $cp) { //walk through all campaigns in result set
    if ($cp->currentStatus == "Active")
      fputcsv($handle,array($cp->id,$cp->name,date("Y-m-d H:i:s",$cp->createdAt))); //write line in csv
  }
  $data = $elq->get('/assets/campaigns?depth=minimal&search=*&count='.$count.'&page='.$i); //next call
}

fclose($handle);
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header("Content-Disposition: attachment; filename=".date("Ymd_His")."_ActiveCampaigns.csv");
readfile($tmp); //return CSV
?>