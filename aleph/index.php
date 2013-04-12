<?php 
// set PHP environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
// config
$host = Get::param('host'); // ups.sunyconnect.suny.edu
$port = Get::param('port'); // 4360
$base = Get::param('base'); // UPS01PUB
$query = Get::param('query');
$start = Get::param('start');
$max = Get::param('max');
if ( !is_numeric($max) ) {
  $max = 10;
}
$callback = Get::param('callback');
// create client object
$aleph = new AlephXserverClient($host,$port,$base,$_GET);
// run search and get a set of records
$xml = $aleph->search($query,NULL,$start,$start+($max-1));
$x = new SimpleXml($xml,NULL,NULL,NULL,FALSE);
$recs = $x->getXpathXmlDocs('//present/record/metadata');
// get hit count
$arr = array();
$arr['circ_status'] = $aleph->circStatus('000094099');
$arr['hits'] = number_format(intval($aleph->getNoEntries()));
// process records
foreach ( $recs as $n => $rec ) {
	$x = new SimpleXml($rec,NULL,NULL,NULL,FALSE);
	$arr['recs'][$n]['language'] = $x->getXpathText('//oai_marc/varfield[@id="041"]');
	$arr['recs'][$n]['authors'][] = $x->getXpathText('//oai_marc/varfield[@id="100"]');
	$arr['recs'][$n]['title'] = $x->getXpathText('//oai_marc/varfield[@id="245"]');
	$arr['recs'][$n]['description'] = $x->getXpathText('//oai_marc/varfield[@id="520"]/subfield[@label="a"]');
	$arr['recs'][$n]['items'][$n]['identifiers'][] = 'ISBN'.chr(31).$x->getXpathText('//oai_marc/varfield[@id="020"]/subfield[@label="a"]');
	$arr['recs'][$n]['items'][$i]['publication_date'] = $x->getXpathText('//oai_marc/varfield[@id="260"]/subfield[@label="c"]');
	$arr['recs'][$n]['items'][$i]['publisher'] = $x->getXpathText('//oai_marc/varfield[@id="260"]/subfield[@label="b"]');
	$arr['recs'][$n]['items'][$i]['physical_description'] = $x->getXpathText('//oai_marc/varfield[@id="300"]');
	$arr['recs'][$n]['items'][$i]['media_type'] = ''; //TODO: hunt down data in the marc record
	$arr['recs'][$n]['items'][$i]['edition'] = $x->getXpathText('//oai_marc/varfield[@id="250"]');
}
// display
$json = json_encode($arr);
header('Content-Type: application/json; charset=utf-8'); 
if ( $callback !== '' ) {
	echo $callback.'('.$json.');';
} else {
	echo $json;
}
?>
