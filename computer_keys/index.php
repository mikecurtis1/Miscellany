<?php 
// debug error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
// includes
require_once(dirname(__FILE__).'/AvailabilityTable.php');
require_once(dirname(__FILE__).'/TimeBlock.php');
require_once(dirname(__FILE__).'/../classes/HttpRequest.php');
// create instance
$a = new AvailabilityTable();
// run 
$g_computer = HttpRequest::getValue('computer');
$g_begin = HttpRequest::getValue('begin');
$g_end = HttpRequest::getValue('end');
if ( $new_time_block = TimeBlock::create($g_begin,$g_end) ) {
	if ( $a->setNewTimeBlock($g_computer,$new_time_block) ) {
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
$computers = $a->getComputers();
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
<?php print_r($a->getComputers()); ?>
</pre>
<br />
<br />
