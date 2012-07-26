<?php 
// set PHP environment
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../classes/Config.php');
require_once(dirname(__FILE__).'/../classes/Input.php');
require_once(dirname(__FILE__).'/../classes/Pagination.php');
require_once(dirname(__FILE__).'/../classes/SruClient.php');
require_once(dirname(__FILE__).'/../classes/MarcXml2Php.php');
require_once(dirname(__FILE__).'/../private.php');
$cfg_path = dirname(__FILE__).'/worldcat_sru.yaml';
// create config instance and set client arguments
$cfg = new Config($cfg_path);
$baseurl = 'http://www.worldcat.org/webservices/catalog/search/sru?servicelevel=full&frbrGrouping=on&wskey=' . $private['wskey'] .'&';
$max = $cfg->getValue('maximumRecords');
$packing = $cfg->getValue('recordPacking');
$schema = $cfg->getValue('recordSchema');
// create client objects
$i = new Input($_GET);
$p = new Pagination($cfg->getValue('limit')); // or maximumRecords
$sru = new SruClient($baseurl,$max,$packing,$schema);
$marc = new MarcXml2Php(NULL);
// set search arguments
$query = $i->getValue('query');
$start = $i->getValue('start');
$sort = $i->getSortKey($cfg->getValue('sortKeys'));
// preform query
if($query !== ''){
  $xml = $sru->search($query,$start,$sort);
  $hits = $sru->response['numberOfRecords'];
  $p->setValues($hits,$start);
  $p->setURLs($_GET);
  $results = $marc->parse($xml);
}
// display
include_once('template.html');
?>
