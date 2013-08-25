<?php 
// debug error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
// includes
require_once(dirname(__FILE__).'/ComputerKeyManager.php');
require_once(dirname(__FILE__).'/TimeBlock.php');
require_once(dirname(__FILE__).'/../classes/HttpRequest.php');
// init vars
//TODO: move out of public web root
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
// create instance
if ( $m = ComputerKeyManager::create($db_host,$db_username,$db_password) ) {
	$computers = $m->getComputers();
} else {
	die('Could NOT create ComputerKeyManager.');
}
// run 
$g_computer = HttpRequest::getValue('computer');
$g_begin = HttpRequest::getValue('begin');
$g_end = HttpRequest::getValue('end');
if ( $new_time_block = TimeBlock::create(strtotime($g_begin),strtotime($g_end),'SCHEDULED') ) {
	if ( $m->addNewTimeBlock($g_computer,$new_time_block) ) {
		header('Location: http://localhost/computer_keys/index.php');
	} else {
		echo "New time block <em>NOT</em> added.\n";
	}
}
?>
<form action="" method="get">
Computer: 
<select name="computer">
<?php 
foreach ( $computers as $computer ) {
	echo "<option value=\"{$computer->getName()}\">{$computer->getName()}</option>\n";
}
?>
</select>
<div>Begin: <input type="text" name="begin" value="<?php echo date("Y-m-d H:i:s"); ?>" /></div>
<div>End: <input type="text" name="end" value="<?php echo date("Y-m-d H:i:s",time()+1200); ?>" /></div>
<div><input type="submit" value="submit" /></div>
</form>
<hr />
<a href="index.php">Re-load</a>
<hr />
<pre>
<?php print_r($m->getComputers()); ?>
</pre>
<br />
<br />
