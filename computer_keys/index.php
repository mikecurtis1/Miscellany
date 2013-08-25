<pre>
<?php 
// debug error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
// includes
require_once(dirname(__FILE__).'/AvailabilityTable.php');
require_once(dirname(__FILE__).'/TimeBlock.php');
require_once(dirname(__FILE__).'/../classes/HttpRequest.php');
// run
$a = new AvailabilityTable();
$computers = $a->getComputerNames();
print_r($a->getComputers());
// test TimeBlock object 
$begin1 = 'foo'; // bad, non-time strings return false
$end1 = 'bar';
$begin2 = date("Y-m-d H:i:s",time()+3600); // begin after end returns false
$end2 = date("Y-m-d H:i:s",time());
$begin3 = date("Y-m-d H:i:s",time());
$end3 = date("Y-m-d H:i:s",time()+3600);
$begin4 = date("Y-m-d H:i:s",time()-7200);
$end4 = date("Y-m-d H:i:s",time()-3600);
$begin5 = '2013-08-24 08:00:00';
$end5 = '2013-08-24 11:20:00';
$begin6 = '2013-08-24 09:00:01';
$end6 = '2013-08-24 10:30:00';
$block = TimeBlock::create(HttpRequest::getValue('begin'),HttpRequest::getValue('end'));
$block1 = TimeBlock::create($begin1,$end1);
$block2 = TimeBlock::create($begin2,$end2);
$block3 = TimeBlock::create($begin3,$end3);
$block4 = TimeBlock::create($begin4,$end4);
$block5 = TimeBlock::create($begin5,$end5);
$block6 = TimeBlock::create($begin6,$end6);
echo var_dump($block1);
echo var_dump($block2);
echo var_dump($block3);
echo var_dump($block4);
echo var_dump($block5);
echo var_dump($block6);
// test time conflict
#echo var_dump($a->noTimeConflict($block3,$block4));
#echo var_dump($a->noTimeConflict($block4,$block5));
#echo var_dump($a->noTimeConflict($block5,$block6));
?>
</pre>
<hr />
<?php 
echo var_dump($a->noTimeConflict($block5,$block6));
#$a->noTimeConflict($block5,$block6)
?>
<hr />
<?php echo $a->getHTMLTable(); ?>
<hr />
<form action="" method="get">
Computer: 
<select name="computer">
<?php 
foreach ( $computers as $name ) {
	echo "<option value=\"{$name}\">{$name}</option>\n";
}
?>
</select>
<div>Begin: <input type="text" name="begin" value="<?php echo date("Y-m-d H:i:s"); ?>" /></div>
<div>End: <input type="text" name="end" value="<?php echo date("Y-m-d H:i:s",time()+1200); ?>" /></div>
<div><input type="submit" value="submit" /></div>
</form>
<pre>
<?php echo var_dump($block); ?>
</pre>
<hr />
<br />
<br />
