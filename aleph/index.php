<?php 
// configure and create objects
error_reporting(E_ALL);
require_once('config.php');
require_once(dirname(__FILE__).'/../classes/Input.php');
require_once(dirname(__FILE__).'/../classes/Pagination.php');
require_once(dirname(__FILE__).'/../classes/AlephXserverClient.php');
$i = new Input($_GET);
$p = new Pagination($cfg['limit']);
$aleph = new AlephXserverClient($cfg,$_GET);
// set pagination, then perform search
$p->setValues(intval($aleph->no_entries),$i->getValue('start'));
$p->setURLs($_GET);
$xml = $aleph->search($p->start,$p->end);
// display
include_once('template.html');
?>
