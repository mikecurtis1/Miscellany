<?php 
/** 
 * http://search.idsproject.org/curtismi/index.php?oclc_symbol=VYQ&view=z3950&host=ups.sunyconnect.suny.edu&port=6363&database=UPS01PUB&username=&password=&syntax=opac&search_type=rpn&rec_type=xml%3B+charset%3Dmarc-8%2Cutf-8&rpn=%40attr+1%3D4+health%20%3E%3E%20search.idssearch.idsproject.org/curtismi/index.php?oclc_symbol=VYQ&view=z3950&host=ups.sunyconnect.suny.edu&port=6363&database=UPS01PUB&username=&password=&syntax=opac&search_type=rpn&rec_type=xml;+charset=marc-8,utf-8&rpn=@attr+1=1007+000087884
 */
// PHP environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
// config
$callback = Get::param('callback');
$oclc_symbol = Get::param('oclc_symbol');
$host = Get::param('host');
$port = Get::param('port');
$database = Get::param('database');
$username = Get::param('username');
$password = Get::param('password');
$syntax = Get::param('syntax');
$search_type = Get::param('search_type');
$rec_type = Get::param('rec_type');
$rpn = Get::param('rpn');
// create client object
$z = new Z3950Client($host,$port,$database,$username,$password,$syntax,$search_type,$rec_type);
// run search and get a record set
$hits = $z->zSearch($rpn);
$limit = 10;
$end = 1;
if ( $hits < $limit ) {
  $end = $hits;
} else {
	$end = $limit;
}
$recs = $z->getRecords(1,$end);
// set hits
$arr = array();
$arr['hits'] = number_format($hits);
// process records
foreach ( $recs as $n => $rec ) {
	$x = new SimpleXml($rec,NULL,NULL,'http://www.loc.gov/MARC21/slim',FALSE);
	$ns = array('slim'=>'http://www.loc.gov/MARC21/slim');
	$x->regNameSpaces($ns);
	$holdings = $x->getXpathXmlDocs('//holding');
	$arr['recs'][$n]['metadata_source'] = 'Aleph';
	$arr['recs'][$n]['metadata_source_id'] = $x->getXpathText('//slim:record/slim:controlfield[@tag="001"]');
	$arr['recs'][$n]['metadata_source_uri'] = 'z3950//:'.$host.':'.$port.'/'.$database.'?query=('.urlencode($rpn).')'; // http://www.gils.net/z-url.txt
	$arr['recs'][$n]['title'] = $x->getXpathText('//slim:record/slim:datafield[@tag="245"]');
	$arr['recs'][$n]['authors'][] = $x->getXpathText('//slim:record/slim:datafield[@tag="100"]/slim:subfield[@code="a"]'); //TODO: gather up author data from all associated fields
	$arr['recs'][$n]['language'] = $x->getXpathText('//slim:record/slim:datafield[@tag="041"]');
	$arr['recs'][$n]['series'] = $x->getXpathText('//slim:record/slim:datafield[@tag="490"]');
	$arr['recs'][$n]['description'] = $x->getXpathText('//slim:record/slim:datafield[@tag="520"]/slim:subfield[@code="a"]');
	if ( $arr['recs'][$n]['description'] === '' ) {
		$arr['recs'][$n]['description'] = $x->getXpathText('//slim:record/slim:datafield[@tag="505"]');
	}
	$arr['recs'][$n]['topics'][] = $x->getXpathText('//slim:record/slim:datafield[@tag="650"]');
	foreach ( $holdings as $i => $holding ) {
		$h = new SimpleXml($holding,NULL,NULL,NULL,FALSE);
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
		if ( $temp['copyNumber'] !== '' ) {
			$arr['recs'][$n]['items'][$i]['identifiers'][] = 'COPY'.chr(31).$temp['copyNumber'];
		}
		if ( $temp['itemId'] !== '' ) {
			$arr['recs'][$n]['items'][$i]['identifiers'][] = 'ITEMID'.chr(31).$temp['itemId'];
		}
		if ( $temp['enumAndChron'] !== '' ) {
			$arr['recs'][$n]['items'][$i]['identifiers'][] = 'ENUMANDCHRON'.chr(31).$temp['enumAndChron'];
		}
		$a['publicNote'] = $temp['publicNote'];
		$a['availableNow'] = $temp['availableNow'];
		$a['availabiltyDate'] = $temp['availabiltyDate'];
		$a['availableThru'] = $temp['availableThru'];
		$arr['recs'][$n]['items'][$i]['access'] = http_build_query($a, '', '&');
		$arr['recs'][$n]['items'][$i]['copyright'] = '';
		$arr['recs'][$n]['items'][$i]['location'] = buildLocation($temp);
		$arr['recs'][$n]['items'][$i]['uri'] = '';
		$arr['recs'][$n]['items'][$i]['mime_type'] = '';
		$arr['recs'][$n]['items'][$i]['identifiers'][] = 'ISBN'.chr(31).$x->getXpathText('//slim:record/slim:datafield[@tag="020"]/subfield[@label="a"]');
		$arr['recs'][$n]['items'][$i]['publication_date'] = $x->getXpathText('//slim:record/slim:datafield[@tag="260"]/slim:subfield[@code="c"]');
		$arr['recs'][$n]['items'][$i]['publisher'] = $x->getXpathText('//slim:record/slim:datafield[@tag="260"]/slim:subfield[@code="b"]');
		$arr['recs'][$n]['items'][$i]['physical_description'] = $x->getXpathText('//slim:record/slim:datafield[@tag="300"]');
		$arr['recs'][$n]['items'][$i]['media_type'] = ''; //TODO: hunt down data in the marc record
		$arr['recs'][$n]['items'][$i]['edition'] = $x->getXpathText('//slim:record/slim:datafield[@tag="250"]');
	}
}
// display
$json = json_encode($arr);
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
function buildMediaType(){}
function buildAuthors(){}
?>
