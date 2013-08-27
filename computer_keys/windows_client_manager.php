<?php 
// debug error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
// includes
require_once(dirname(__FILE__).'/../../secure/php/classes/DbQuery.php');
require_once(dirname(__FILE__).'/../../secure/php/classes/DbConnection.php');
require_once(dirname(__FILE__).'/../../secure/php/classes/HttpRequest.php');
// init variables
$db_name = 'computer_keys';
$g_computer = HttpRequest::getValue('computer');
$g_key      = HttpRequest::getValue('key');
$g_debug    = HttpRequest::getValue('debug');
$end_unix = '';
$end_time = '';
$seconds_remaining = '';
// create query statement
$sql = '
SELECT * 
FROM computers 
LEFT JOIN key_schedule 
ON key_schedule.computer = computers.id 
WHERE computers.name = \''.$g_computer.'\' 
AND key_schedule.active = \'y\' 
AND key_schedule.key = \''.$g_key.'\' 
AND key_schedule.begin <= '.time().' 
AND key_schedule.end >= '.time().' 
';
// run
if ( $dbc = DbConnection::create('localhost','root','',$db_name) ) {
	if ( $query = DbQuery::query($dbc->getLink(),$dbc->getDbName(),$sql) ) {
		if ( $query->getCount() > 0 ) {
			$end_unix = $query->getResults()[0]['end'];
			$end_time = date("Y-m-d H:m:s", $end_unix);
			$seconds_remaining = $end_unix - time();
			//TODO: check this delimited string format for consistency
			echo 'UNLOCK|UNIX_END='.$end_unix.'&END_TIME='.$end_time.'&SECONDS_REMAINING='.$seconds_remaining;
		} else {
			echo 'LOCK|MESSAGE=No active key';
		}
	} else {
		echo 'LOCK|MESSAGE=Query failed';
	}
} else {
	echo 'LOCK|MESSAGE=Could NOT connect';
}
?>
<?php if ( $g_debug === 'debug' ) { ?>
<pre>
----------------------------------------
  SQL STATEMENT
----------------------------------------
<?php echo trim($sql)."\n"; ?>
----------------------------------------
  TIME
----------------------------------------
<?php 
echo 'END UNIX: '.$end_unix."\n";
echo 'END TIME: '.$end_time."\n";
echo 'REMAINING: '.$seconds_remaining."\n";
?>
----------------------------------------
  QUERY OBJECT
----------------------------------------
<?php echo var_dump($query); ?>
</pre>
<?php } ?>
