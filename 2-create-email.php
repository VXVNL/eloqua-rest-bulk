<?php
require_once("../../config.php");
require_once("../../libs/eloqua-php-request-master/eloquaRequest.php");
$elq = new EloquaRequest($site,$username,$password,"https://secure.eloqua.com/API/REST/1.0");

require_once("../libs/eloqua-php-request-master/models/assets/email.php");

$email = new Email();
$email->encodingId = "3"; //UTF-8
$email->name = "Test";

$result = $elq->post("/assets/email",$email);
print_r($result);
?>