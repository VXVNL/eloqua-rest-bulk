<?php
require_once("../../config.php");
require_once("../../libs/eloqua-php-request-master/eloquaRequest.php");
$elq = new EloquaRequest($site,$username,$password,"https://secure.eloqua.com/API/REST/1.0");

$form = $elq->get("/assets/form/948?depth=complete");
$form->htmlName = "newHTMLName-".time();
$elq->put("/assets/form/948",$form);
?>