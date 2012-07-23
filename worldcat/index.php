<?php 
// configure and create objects
error_reporting(E_ALL);
require_once('config.php');
require_once(dirname(__FILE__).'/../classes/Input.php');
require_once(dirname(__FILE__).'/../classes/Pagination.php');
require_once(dirname(__FILE__).'/../classes/SruClient.php');
$i = new Input($_GET);
$p = new Pagination($cfg['maximumRecords']);
//TODO: pass just the config array
$sru = new SruClient($cfg['baseurl'],$cfg['maximumRecords'],$cfg['recordPacking'],$cfg['recordSchema']);
// perform search and set pagination values
if($i->getValue('query') !== ''){
  //TODO: clear up interaction between Input, config, and client object
  $query = $i->getValue('query');
  $sort = $i->getValue('sort');
  if($sort !== ''){
    $sort = $cfg['sortKeys'][$i->getValue('sort')];
  }
  $xml = $sru->search($query,$i->getValue('start'),$sort);
  $p->setValues($sru->response['numberOfRecords'],$i->getValue('start'));
  $p->setURLs($_GET);
}
// display
include_once('template.html');
?>
