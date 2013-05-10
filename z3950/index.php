<?php 
/** 
 * index.php?oclc_symbol=VYQ&view=z3950&host=ups.sunyconnect.suny.edu&port=6363&database=UPS01PUB&username=&password=&syntax=opac&search_type=rpn&rec_type=xml%3B+charset%3Dmarc-8%2Cutf-8&rpn=%40attr+1%3D4+health%20%3E%3E%20search.idssearch.idsproject.org/curtismi/index.php?oclc_symbol=VYQ&view=z3950&host=ups.sunyconnect.suny.edu&port=6363&database=UPS01PUB&username=&password=&syntax=opac&search_type=rpn&rec_type=xml;+charset=marc-8,utf-8&rpn=@attr+1=1007+000087884
 */
// PHP environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8');
require_once(dirname(__FILE__).'/../classes/Request.php');
require_once(dirname(__FILE__).'/../classes/Z3950Client.php');
require_once(dirname(__FILE__).'/../classes/SimpleXml.php');
require_once(dirname(__FILE__).'/../classes/Record.php');
require_once(dirname(__FILE__).'/../classes/Item.php');
require_once(dirname(__FILE__).'/../classes/Isbn.php');
// config
$callback = Request::value('callback');
$host = Request::value('host');
$port = Request::value('port');
$database = Request::value('database');
$username = Request::value('username');
$password = Request::value('password');
$syntax = Request::value('syntax');
$search_type = Request::value('search_type');
$rec_type = Request::value('rec_type');
$rpn = Request::value('rpn');
$delimiter = '::'; //chr(31)
// create client object
$z = new Z3950Client($host,$port,$database,$username,$password,$syntax,$search_type,$rec_type);
// run search and get a record set
$hits = $z->zSearch($rpn);
$max = 10;
$end = 1;
$start = 1;
if ( $hits < $max ) {
  $end = $hits;
} else {
	$end = $max;
}
$recs = $z->getRecords($start,$end);
// process records
foreach ( $recs as $n => $rec ) {
	#$x = new SimpleXml($rec,NULL,NULL,NULL,FALSE);
	$r = new Record();
	$i = new Item();
	$x = new SimpleXml($rec,NULL,NULL,'http://www.loc.gov/MARC21/slim',FALSE);
	$ns = array('slim'=>'http://www.loc.gov/MARC21/slim');
	$x->regNameSpaces($ns);
	$holdings = $x->getXpathXmlDocs('//holding');
	$r->setMetadataSource('Aleph');
	$r->setMetadataSourceId($x->getXpathText('//slim:record/slim:controlfield[@tag="001"]'));
	$r->setMetadataSourceUri('z3950//:'.$host.':'.$port.'/'.$database.'?query=('.urlencode($rpn).')'); // http://www.gils.net/z-url.txt

	/*
	$marc_020 = $x->getXpathXmlDocs('//oai_marc/varfield[@id="020"]/subfield[@label="a"]');
	$isbn = Isbn::all(implode(',',$marc_020));
	print_r($isbn);
	*/
	$marc_001 = $x->getXpathText('//slim:record/slim:controlfield[@tag="001"]');
	$marc_020 = $x->getXpathText('//slim:record/slim:datafield[@tag="020"]/subfield[@label="a"]');
	$isbn = Isbn::first($marc_020);
	$r = new Record();
	$r->setTitle($x->getXpathText('//slim:record/slim:datafield[@tag="245"]'));
	$r->setSeries($x->getXpathText('//slim:record/slim:datafield[@tag="490"]'));
	$r->setAuthor($x->getXpathText('//slim:record/slim:datafield[@tag="100"]/subfield[@label="a"]'));
	$r->setEdition($x->getXpathText('//slim:record/slim:datafield[@tag="250"]'));
	$r->setLanguage($x->getXpathText('//slim:record/slim:datafield[@tag="041"]'));
	$description = '';
	if ( $description === '' ) {
		$description = $x->getXpathText('//slim:record/slim:datafield[@tag="520"]/subfield[@label="a"]');
	}
	if ( $description === '' ) {
		$description = $x->getXpathText('//slim:record/slim:datafield[@tag="505"]/subfield[@label="a"]');
	}
	$r->setDescription($description);
	foreach ( $holdings as $i => $holding ) {
		$h = new SimpleXml($holding,NULL,NULL,NULL,FALSE);
		$i = new Item();
		$temp['localLocation'] = $h->getXpathText('localLocation');
		$temp['shelvingLocation'] = $h->getXpathText('shelvingLocation');
		$temp['callNumber'] = $h->getXpathText('callNumber');
		$temp['copyNumber'] = $h->getXpathText('copyNumber');
		$temp['enumAndChron'] = $h->getXpathText('enumAndChron');
		$temp['publicNote'] = $h->getXpathText('publicNote');
		$temp['availableNow'] = $h->getXpathAttr('circulations/circulation/availableNow','value');
		$temp['availabiltyDate'] = $h->getXpathText('circulations/circulation/availabiltyDate');
		$temp['availableThru'] = $h->getXpathText('circulations/circulation/availableThru');
		$temp['itemId'] = $h->getXpathText('circulations/circulation/itemId');
		$a['publicNote'] = $temp['publicNote'];
		$a['availableNow'] = $temp['availableNow'];
		$a['availabiltyDate'] = $temp['availabiltyDate'];
		$a['availableThru'] = $temp['availableThru'];
		$i->setMediaType(''); //TODO: build a media type
		$i->setAccess(http_build_query($a, '', '&'));
		$i->setCopyright('');
		$i->setLocation(buildLocation($temp));
		$i->setUri('');
		$i->setMimeType('');
		$i->setPublicationDate($x->getXpathText('//slim:record/slim:datafield[@tag="260"]/slim:subfield[@code="c"]'));
		$i->setPublisher($x->getXpathText('//slim:record/slim:datafield[@tag="260"]/slim:subfield[@code="b"]'));
		$i->setPhysicalDescription($x->getXpathText('//slim:record/slim:datafield[@tag="300"]'));
		$i->setIdentifier('SYS',$delimiter,$marc_001);
		$i->setIdentifier('ENUMANDCHRON',$delimiter,$temp['enumAndChron']);
		$i->setIdentifier('ITEMID',$delimiter,$temp['itemId']);
		$i->setIdentifier('COPY',$delimiter,$temp['copyNumber']);
		$i->setIdentifier('ISBN',$delimiter,$isbn);
		$r->setItem($i);
	}
	$record_objects[] = $r;
}
// produce and display JSON
//TODO: this part is a mess! I should probably have a Records object with a toJson() method
function recordObjects2Array($obj) {
	if ( is_object($obj) ) {
		$obj = (array) $obj;
	}
	if ( is_array($obj) ) {
		$new = array();
		foreach($obj as $key => $val) {
			$new[$key] = recordObjects2Array($val);
		}
	}
	else { 
		$new = $obj;
	}
	return $new;
}
$arr = array();
$arr['query'] = $rpn;
$arr['start'] = $start;
$arr['max'] = $max;
$arr['hits'] = $hits;
$arr['records'] = recordObjects2Array($record_objects);
$json = str_replace('\\u0000', '', json_encode($arr));
header('Content-Type: application/json; charset=utf-8'); 
if ( $callback !== '' ) {
	echo $callback.'('.$json.');';
} else {
	echo $json;
}
// helpers
function buildLocation($arr=array(),$delimiter='; '){
	$location = '';
	if ( isset($arr['localLocation']) && $arr['localLocation'] !== '' ) {
		$location.= $arr['localLocation'].$delimiter;
	}
	if ( isset($arr['shelvingLocation']) && $arr['shelvingLocation'] !== '' ) {
		$location.= $arr['shelvingLocation'].$delimiter;
	}
	if ( isset($arr['callNumber']) && $arr['callNumber'] !== '' ) {
		$location.= $arr['callNumber'];
	}
	
	return $location;
}
?>
