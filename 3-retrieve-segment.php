<?php
require_once("../../config.php");
require_once("../../libs/eloqua-php-request-master/eloquaRequest.php");
$elq = new EloquaRequest($site,$username,$password,"https://secure.eloqua.com/API/REST/1.0");

$search = "test*";
$search_result = $elq->get("/assets/contact/segments?depth=minimal&count=100&page=1&search=".urlencode($search));

//Let's assume our first result is the segment we need (for the sake of this example)
$segment_id = (int) $search_result->elements[0]->id;
$segment = $elq->get("/assets/contact/segment/".$segment_id."?depth=complete");
print_r($segment);
?>