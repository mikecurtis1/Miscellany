<?php 

// PHP settings
set_time_limit(300);
error_reporting(E_ALL);

// create objects
require_once(dirname(__FILE__).'/../ConfigPrivate.php');
$cfg_private = new ConfigPrivate();
require_once('IdsMySql.php');
$idsmysql = new IdsMySql($cfg_private->sql_host,$cfg_private->sql_user,$cfg_private->sql_passwd,'illiad');

// procedure
echo 'START:'.date("H:i:s")."\n";
echo var_dump($idsmysql);
/*$sql = "SELECT * FROM `items`";
$items = $idsmysql->titleWords($sql,'marc245','worldcat','item_id',NULL);*/
/*$sql = "SELECT * FROM `transactions` WHERE `LoanTitle` != ''";
$items = $idsmysql->titleWords($sql,'LoanTitle','ILLiadLT','id','TransactionStatus');*/
$sql = "SELECT * FROM `transactions` WHERE `PhotoJournalTitle` != ''";
$items = $idsmysql->titleWords($sql,'PhotoJournalTitle','ILLiadPT','id','TransactionStatus');
$idsmysql->closeDbConnection();
echo 'END:'.date("H:i:s")."\n";

?>
