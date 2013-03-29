<?php 
// set PHP environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
// includes
require_once(dirname(__FILE__).'/helpers.php');
require_once(dirname(__FILE__).'/Pagination.php');
require_once(dirname(__FILE__).'/AlephXserverClient.php');
// config
$host = 'ups.sunyconnect.suny.edu';
$port = '4360';
$base = 'UPS01PUB';
// create client objects and set arguments
$p = new Pagination(12);
/*if ( isset($_GET['query']) && $_GET['query'] === '' ) {
  $temp_g = $_GET;
	$temp_g['query'] = 'WCL=GCHGM';
	$aleph = new AlephXserverClient($host,$port,$base,$temp_g);
} else {*/
	$aleph = new AlephXserverClient($host,$port,$base,$_GET,'WCL=GCHGM');
#}
// run
unset($_GET['sort']); //NOTE: after $_GET has been passed to Aleph object it isn't need and should be removed
$start = get('start');
$xml = $aleph->search($start,$start+11);
$p->setValues(intval($aleph->no_entries),$start);
$p->setURLs($_GET);
// display
if ( isset($_GET['view']) && $_GET['view'] === 'detail' ) {
	include_once('template_detail.html');
} else {
	include_once('template.html');
}
?>
