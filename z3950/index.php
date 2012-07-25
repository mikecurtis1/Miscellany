<?php 
// set PHP environment
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../classes/Config.php');
require_once(dirname(__FILE__).'/../classes/Input.php');
require_once(dirname(__FILE__).'/../classes/Pagination.php');
require_once(dirname(__FILE__).'/../classes/Z3950Client.php');
$cfg_path = dirname(__FILE__).'/aleph_z3950.yaml';
// create config instance and set client arguments
$cfg = new Config($cfg_path);
$host = $cfg->getValue('host');
$port = $cfg->getValue('port');
$database = $cfg->getValue('database');
$username = $cfg->getValue('username');
$password = $cfg->getValue('password');
$syntax = $cfg->getValue('syntax');
$search_type = $cfg->getValue('search_type');
$rec_type = $cfg->getValue('rec_type');
// create client objects
$i = new Input($_GET);
$p = new Pagination($cfg->getValue('limit'));
$z = new Z3950Client($host,$port,$database,$username,$password,$syntax,$search_type,$rec_type);
// set search arguments
$query = $i->getValue('query');
$start = $i->getValue('start');
$sort = $i->getSortKey($cfg->getValue('sortKeys'));
// if a search value is present, get z search results and set pagination
if($query !== ''){
  $hits = $z->zSearch($query);
  $p->setValues($hits,$start);
  $p->setURLs($_GET);
  $recs = $z->getRecords($p->start,$p->quantity);
}
// display
include_once('template.html');
?>
