<?php 
/* 
 * index.php?host=ups.sunyconnect.suny.edu&port=4360&base=UPS01PUB&query=&start=1&max=&zoom=&css=
 * index.php?query=&start=1&max=&zoom=&css=
 */
// set PHP environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8'); 
require_once(dirname(__FILE__).'/../classes/Get.php');
require_once(dirname(__FILE__).'/../classes/AlephXserverClient.php');
require_once(dirname(__FILE__).'/../classes/SimpleXml.php');
require_once(dirname(__FILE__).'/../classes/Isbn.php');
require_once(dirname(__FILE__).'/../classes/Record.php');
require_once(dirname(__FILE__).'/../classes/Item.php');
// config
$content = '';
$snippet = '';
$host = 'ups.sunyconnect.suny.edu';
$port = '4360';
$base = 'UPS01PUB';
$query = '';
$start = 1;
$max = 10;
$callback = 'callback';
#$host = Get::value('host'); // ups.sunyconnect.suny.edu
#$port = Get::value('port'); // 4360
#$base = Get::value('base'); // UPS01PUB
$query = Get::value('query');
$start = Get::value('start');
$max = Get::value('max');
$callback = Get::value('callback');
if ( !is_numeric($start) ) {
  $start = 1;
}
if ( !is_numeric($max) ) {
  $max = 10;
}
$hits = 0;
$opac_url = 'http://'.$host.':'.$port.'/F?func=find-c&amp;local_base='.$base.'&amp;ccl_term='.urlencode($query);
$delimiter = '::'; //chr(31)
// create objects, run search, and get a set of records
$aleph = new AlephXserverClient($host,$port,$base,$_GET);
$xml = $aleph->search($query,NULL,$start,$start+($max-1));
$hits = number_format(intval($aleph->getNoEntries()));
$x = new SimpleXml($xml,NULL,NULL,NULL,FALSE);
$recs = $x->getXpathXmlDocs('//present/record/metadata');
// process records
$records = array();
foreach ( $recs as $n => $rec ) {
  $x = new SimpleXml($rec,NULL,NULL,NULL,FALSE);
	/*
	$marc_020 = $x->getXpathXmlDocs('//oai_marc/varfield[@id="020"]/subfield[@label="a"]');
	$isbn = Isbn::all(implode(',',$marc_020));
	print_r($isbn);
	*/
	$marc_001 = $x->getXpathText('//oai_marc/fixfield[@id="001"]');
	$marc_020 = $x->getXpathText('//oai_marc/varfield[@id="020"]/subfield[@label="a"]');
	$isbn = Isbn::first($marc_020);
	$r = new Record();
	$i = new Item();
	$r->setTitle($x->getXpathText('//oai_marc/varfield[@id="245"]'));
	$r->setAuthor($x->getXpathText('//oai_marc/varfield[@id="100"]'));
	$r->setEdition($x->getXpathText('//oai_marc/varfield[@id="250"]'));
	$r->setLanguage($x->getXpathText('//oai_marc/varfield[@id="041"]'));
	$description = '';
	if ( $description === '' ) {
		$description = $x->getXpathText('//oai_marc/varfield[@id="520"]/subfield[@label="a"]');
	}
	if ( $description === '' ) {
		$description = $x->getXpathText('//oai_marc/varfield[@id="505"]/subfield[@label="a"]');
	}
	$r->setDescription($description);
	$i->setPublicationDate($x->getXpathText('//oai_marc/varfield[@id="260"]/subfield[@label="c"]'));
	$i->setPublisher($x->getXpathText('//oai_marc/varfield[@id="260"]/subfield[@label="b"]'));
	$i->setPhysicalDescription($x->getXpathText('//oai_marc/varfield[@id="300"]'));
	$i->setIdentifier('SYS'.$delimiter.$marc_001);
	$i->setIdentifier('ISBN'.$delimiter.$isbn);
	$r->setItem((array) $i);
	$records[] = (array) $r;
}
$arr = array();
$arr['query'] = $query;
$arr['start'] = $start;
$arr['max'] = $max;
$arr['hits'] = $hits;
$arr['records'] = $records;
// display
#$json = json_encode($arr);
$json = str_replace('\\u0000', '', json_encode($arr));
header('Content-Type: application/json; charset=utf-8'); 
if ( $callback !== '' ) {
	echo $callback.'('.$json.');';
} else {
	echo $json;
}
?>
