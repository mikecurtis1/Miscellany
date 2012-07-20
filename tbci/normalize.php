<pre>
<?php 

// PHP settings
set_time_limit(300);
error_reporting(E_ALL);

// create objects
require_once(dirname(__FILE__).'/../ConfigPrivate.php');
$cfg_private = new ConfigPrivate();
require_once('MySql.php');
require_once('IdsMySql.php');
$idsmysql = new IdsMySql($cfg_private->sql_host,$cfg_private->sql_user,$cfg_private->sql_passwd,'illiad');

// procedure
echo 'START:'.date("H:i:s")."\n";
#$idsmysql->normalizeNumbers("SELECT * FROM `transactions`");
#$idsmysql->normalizeYears("SELECT * FROM `transactions`");
$idsmysql->normalizeYears("SELECT * FROM `transactions` WHERE `TempYear` IS NOT NULL");
$idsmysql->closeDbConnection();
echo 'END:'.date("H:i:s")."\n";

?>
</pre>

