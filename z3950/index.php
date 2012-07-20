<?php 
// configure and create objects
error_reporting(E_ALL);
require_once('config.php');
require_once(dirname(__FILE__).'/../classes/Input.php');
require_once(dirname(__FILE__).'/../classes/Pagination.php');
require_once(dirname(__FILE__).'/../classes/Z3950Client.php');
$i = new Input($_GET);
$p = new Pagination($cfg['limit']);
$z = new Z3950Client($cfg);
// if a search value is present, get z search results and set pagination
if($i->getValue('query') !== ''){
  $rpn = $i->getValue('query');
  $hits = $z->zSearch($rpn);
  $p->setValues($hits,$i->getValue('start'));
  $p->setURLs($_GET);
  $recs = $z->getRecords($p->start,$p->quantity);
}
// display
include_once('template.html');
?>
