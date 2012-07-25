<?php 
// set PHP environment
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../classes/Config.php');
require_once(dirname(__FILE__).'/../classes/Input.php');
require_once(dirname(__FILE__).'/../classes/Pagination.php');
require_once(dirname(__FILE__).'/../classes/AlephXserverClient.php');
$cfg_path = dirname(__FILE__).'/vyq_aleph.yaml';
// create config instance and set client arguments from config
$cfg = new Config($cfg_path);
$host = $cfg->getValue('host');
$port = $cfg->getValue('port');
$base = $cfg->getValue('base');
// create client objects and set client arguments from input
$i = new Input($_GET);
$p = new Pagination($cfg->getValue('limit'));
$aleph = new AlephXserverClient($host,$port,$base,$_GET);
// set search arguments
$query = $i->getValue('query');
$start = $i->getValue('start');
$sort = $i->getSortKey($cfg->getValue('sortKeys'));
// preform query
$p->setValues(intval($aleph->no_entries),$start);
$p->setURLs($_GET);
$xml = $aleph->search($p->start,$p->end);
// display
include_once('template.html');
?>
