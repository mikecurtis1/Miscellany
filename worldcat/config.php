<?php 

// config
$cfg = array();

// get OCLC wskey
require_once(dirname(__FILE__).'/../ConfigPrivate.php');
$cfg_private = new ConfigPrivate();
$wskey = $cfg_private->wskey;
$baseurl = 'http://www.worldcat.org/webservices/catalog/search/sru?servicelevel=full&frbrGrouping=on&wskey=' . $wskey .'&';

// baseurl
$cfg['baseurl'] = $baseurl;
// SRU
$cfg['maximumRecords'] = '10';
$cfg['recordPacking'] = 'xml';
$cfg['recordSchema'] = 'marcxml';
$cfg['sortKeys'] = array('author'=>'Author','title'=>'Title');

/*
// ex. http://opencontent.indexdata.com/wikipedia?operation=searchRetrieve&version=1.1&query=dc.title%3D%22love%22&startRecord=1&maximumRecords=10
// baseurl
$cfg['baseurl'] = 'http://opencontent.indexdata.com/wikipedia?';
// SRU
$cfg['maximumRecords'] = '10';
$cfg['recordPacking'] = 'xml';
$cfg['recordSchema'] = 'dc';
*/

?>
