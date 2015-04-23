<?php
require_once("../../config.php");
require_once("../../libs/eloqua-php-request-master/eloquaRequest.php");

if (preg_match("/^([0-9a-zA-Z\.\@]+)$/i",$_REQUEST['search']))
  $search = $_REQUEST['search']; //simple security
?>
<form method="get" action="<?=$_SERVER['REQUEST_URI']?>">
  Search:
  <input type="text" name="search" value="<?=$search?>" />
  <input type="submit" value="Submit" />
</form>
<ul>
<?php
if (!empty($search)) {	
  $elq = new EloquaRequest($site,$username,$password,"https://secure.eloqua.com/API/REST/1.0");
  $result = $elq->get("/data/contacts?search=*".urlencode($search)."*&count=100&page=1&depth=minimal");

  foreach ($result->elements as $key => $contact) {
    echo "<li>".$contact->name."</li>";
  }
}
?>
</ul>
