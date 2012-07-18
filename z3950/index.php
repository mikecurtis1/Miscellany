<?php 
// configure and create objects
require_once('config.php');
require_once('Input.php');
require_once('zClient.php');
require_once('Pagination.php');
$z = new zClient($cfg);
$i = new Input($_GET);
$p = new Pagination($cfg['limit']);
// if a search value is present, get z search results and set pagination
if($i->search !== ''){
  $rpn = $i->search;
  $hits = $z->zSearch($rpn);
  $p->setValues($hits,$i->start);
  $p->setURLs($_GET);
  $recs = $z->getRecords($p->start,$p->quantity);
}
// display
include_once('template.html');
?>
