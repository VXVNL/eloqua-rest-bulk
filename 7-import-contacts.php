<?php
require_once("../../config.php");
require_once("../../libs/eloqua-php-request-master/eloquaRequest.php");
$elq = new EloquaRequest($site,$username,$password,"https://secure.eloqua.com/API/Bulk/2.0");

$fieldset = $elq->get("/contacts/fields?q='name=Email*Address'");
$field = $fieldset->items[0]; //let's assume this is the Email Address field we need

$shared_list_set = $elq->get("/contacts/lists?q='name=Import'");
$shared_list = $shared_list_set->items[0]; //let's assume this is the Shared List we need

//build definition
$definition = array("isSyncTriggeredOnImport" => "true",
                    "name" => "Email Address Import ".date("Y-m-d H:i"),
                    "updateRule" => "always",
                    "identifierFieldName" => $field->internalName,
                    "secondsToRetainData" => "3600",
                    "fields" => array($field->internalName => $field->statement),
                    "syncActions" => array("destination" => $shared_list->statement,
                                           "action" => "add"));
                    
//create import definition
$import = $elq->post("/contacts/imports",$definition);

/* The Eloqua PHP library doesn't support sending csv out-of-the-box (unless you adapt it), even though the API does accept this content type.
Therefore we convert the CSV data to an array below. */

$csv = array_map("str_getcsv",file("import.csv"));
foreach ($csv as $n => $line) {
  if ($n == 0) {
    $headers = $line;
    continue;
  }
  
  foreach ($line as $key => $value) {
    $arr[$headers[$key]] = $value;
  }
  
  $data[] = $arr;
}

$import = $elq->post($import->uri."/data",$data);
?>