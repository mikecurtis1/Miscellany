<?php 

// PHP settings
set_time_limit(7200);
error_reporting(E_ALL);

// create instance
require_once(dirname(__FILE__).'/../ConfigPrivate.php');
$cfg_private = new ConfigPrivate();
require_once('WorldCatFetch.php');
$worldcat_fetch = new WorldCatFetch($cfg_private->wskey,'ISBN');

// procedure
$number_file = 'numbers.txt';
echo 'START:'.date("H:i:s")."\n";
if(is_file($number_file)){
	$numbers = file($number_file);
	#$numbers = array_slice($numbers, 0, 10);
	$worldcat_fetch->fetchWorldCatRecords($numbers);
} else {
	echo "Cannot find number file: {$number_file}.\n";
}
echo 'END:'.date("H:i:s")."\n";

?>
