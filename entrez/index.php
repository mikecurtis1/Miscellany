<?php 
// debug error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
// create Entrez instance
require_once('Entrez.php');
$e = new Entrez('pubmed');
// set values from $_GET
$term = '';
if ( isset($_GET['term']) ) {
	$term = $_GET['term'];
}
$start = NULL;
if ( isset($_GET['start']) ) {
	$start = intval($_GET['start']);
}
$end = NULL;
if ( isset($_GET['end']) ) {
	$end = intval($_GET['end']);
}
// run Entrez
$hits = $e->search($term);
$terms = $e->getTranslationStack();
$fetch_url = $e->fetch($start,$end);
#print_r($e->getResults());
// display HTML template
include_once('template.html');
?>
