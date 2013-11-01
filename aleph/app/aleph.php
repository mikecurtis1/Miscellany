<?php 
// set PHP environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8'); 
// load required files
require_once(dirname(__FILE__).'/../../php/classes/AlephXserverClient.php');
require_once(dirname(__FILE__).'/../../php/classes/SimpleXml.php');
require_once(dirname(__FILE__).'/../../php/classes/HttpRequest.php');
require_once(dirname(__FILE__).'/../../php/functions/truncate_str.php');
// run config from .ini file
$app = HttpRequest::getValue('app');
$ini_file = dirname(__FILE__).'/../ini/'.$app.'.ini';
$ini = parse_ini_file($ini_file);
$html_title = $ini['html_title'];
$app_heading = $ini['app_heading'];
$title_truncation = (int) $ini['title_truncation'];
$description_truncation = (int) $ini['description_truncation'];
$template = $ini['template'];
$css_main_href = $ini['css_main_href'];
$host = $ini['host'];
$port = $ini['port'];
$base = $ini['base'];
$max = $ini['max'];
$ccl_suffix = $ini['ccl_suffix'];
// set HTTP request params
$query = HttpRequest::getValue('query');
$start = HttpRequest::getValue('start');
$sort = HttpRequest::getValue('sort');
$no_entries = HttpRequest::getValue('no_entries');
$set_number = HttpRequest::getValue('set_number');
$session_id = HttpRequest::getValue('session_id');
// define search set start and end values
if ( $start === '' ) {
	$start = $ini['start'];
}
$end = $start+$max-1;
if ( $no_entries !== '' && $end > $no_entries ) {
	$end = $no_entries;
}
// create objects, run search, and get a set of records
$aleph = new AlephXserverClient($host,$port,$base,$ccl_suffix);
$xml = $aleph->search($query,$start,$end,$sort,$no_entries,$set_number,$session_id);
$no_entries = intval($aleph->getNoEntries());
$set_number = $aleph->getSetNumber();
$session_id = $aleph->getSessionId();
$x = new SimpleXml($xml,NULL,NULL,NULL,FALSE);
$recs = $x->getXpathXmlDocs('//present/record/metadata');
// error handling
//NOTE: example error codes
// 'Error 6100 Not defined in file xml_present.'
// 'Error reading set 000987'
$errors = array();
foreach ( $aleph->getExceptions() as $exception ) {
	$errors[] = $exception;
}
$error =  $x->getXpathText('//present/error'); // error check for a set number time out
if ( $set_number !== '' && strpos($error,'Error') !== FALSE ) {
	$url = 'http://library.upstate.edu/aleph/index.php?app='.$_GET['app'].'&start=1&query='.$_GET['query'];
	$errors[] = 'An error occured for the set number, please resend the <a href="'.$url.'">search</a>';
}
// process records
$records = array();
foreach ( $recs as $n => $rec ) {
	$x = new SimpleXml($rec,NULL,NULL,NULL,FALSE);
	$marc_001 = $x->getXpathText('//oai_marc/fixfield[@id="001"]');
	$marc_020 = $x->getXpathText('//oai_marc/varfield[@id="020"]/subfield[@label="a"]');
	$title = $x->getXpathText('//oai_marc/varfield[@id="245"]');
	$author = $x->getXpathText('//oai_marc/varfield[@id="100"]');
	$pubdate = $x->getXpathText('//oai_marc/varfield[@id="260"]/subfield[@label="c"]');
	$physical_description = $x->getXpathText('//oai_marc/varfield[@id="300"]');
	$description = '';
	if ( $description === '' ) {
		$description = $x->getXpathText('//oai_marc/varfield[@id="520"]/subfield[@label="a"]');
	}
	if ( $description === '' ) {
		$description = $x->getXpathText('//oai_marc/varfield[@id="505"]/subfield[@label="a"]');
	}
	$records[$n]['id'] = $marc_001;
	$records[$n]['isbn'] = $marc_020;
	$records[$n]['cover_src'] = 'http://library.upstate.edu/aleph/cover.php?isbn='.urlencode($marc_020).'&amp;app='.$app;
	$records[$n]['title'] = truncate_str($title,$title_truncation,TRUE);
	$records[$n]['author'] = $author;
	$records[$n]['physical_description'] = $physical_description;
	$records[$n]['description'] = truncate_str($description,$description_truncation,TRUE);
	$records[$n]['pubdate'] = $pubdate;
}
// create results array
$arr = array();
$arr['query'] = $query;
$arr['start'] = $start;
$arr['no_entries'] = $no_entries;
$arr['records'] = $records;
$arr['errors'] = $errors;
// define pagination values and URLs
$g = $_GET;
unset($g['sort']);
$start = $g['start'];
$g['no_entries'] = $no_entries;
$g['set_number'] = $set_number;
$g['session_id'] = $session_id;
$previous = $start-$max;
$next = $start+$max;
if ( $previous < 1 ) {
	$previous = 1;
}
if ( $next > $no_entries ) {
	$next = $start;
}
$g['start'] = $previous;
$previous_url = 'index.php?'.http_build_query($g,'','&');
$g['start'] = $next;
$next_url = 'index.php?'.http_build_query($g,'','&');
// template
require_once(dirname(__FILE__).'/../templates/'.$template);
?>
