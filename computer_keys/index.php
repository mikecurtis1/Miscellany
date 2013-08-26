<?php 
// debug error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
// includes
require_once(dirname(__FILE__).'/Computers.php');
require_once(dirname(__FILE__).'/TimeBlock.php');
require_once(dirname(__FILE__).'/../classes/HttpRequest.php');
// init vars
//TODO: move out of public web root
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
// create instance
if ( $c = Computers::create($db_host,$db_username,$db_password) ) {
	$computers = $c->getComputers();
} else {
	die('Could NOT create Computers.');
}
// run 
$g_computer = HttpRequest::getValue('computer');
$g_begin = HttpRequest::getValue('begin');
$g_end = HttpRequest::getValue('end');
if ( $new_time_block = TimeBlock::create(strtotime($g_begin),strtotime($g_end),'SCHEDULED',0,'') ) {
	if ( $computers[$g_computer]->getSchedule()->addNewTimeBlock($new_time_block) ) {
		header('Location: http://localhost/computer_keys/index.php');
	} else {
		echo "New time block <em>NOT</em> added.\n";
	}
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Computer Key Manager</title>
<link rel="stylesheet" type="text/css" href="main.css" media="screen" />
</head>
<body>
<h1>Computer Keys</h1>
Current Time: <?php echo date("M, j g:i:s a"); ?><br />
<a href="index.php">Re-load</a> page<br />
<?php 
$schedule = array();
$html = '';
$default_duration = (60*20);
foreach ( $computers as $computer ) {
	$anchors = '';
	$temp = array_merge($computer->getSchedule()->getTimeBlocks(),$computer->getSchedule()->getAvailableTimeBlocks());
	usort($temp,array('Schedule','cmpTimeBlocks'));
	$html .= '<h2>'.$computer->getName().'</h2>'."\n";
	if ( !empty($temp) ) {
		foreach ( $temp as $t ) {
			$anchors = '';
			if ( $t->getType() === 'SCHEDULED' ) {
				$anchors = '<a href="index.php?func=modify&amp;id='.$t->getId().'" class="modify">Modify</a><a href="index.php?func=deactivate&amp;id='.$t->getId().'" class="deactivate">De-Activate</a><span class="id">ID:'.$t->getId().'</span><span class="key">KEY:'.$t->getKey().'</span>';
			}
			if ( $t->getType() === 'AVAILABLE' ) {
				$begin = $t->getBegin();
				$end = $begin + $default_duration;
				if ( $end > $t->getEnd() ) {
					$end = $t->getEnd();
				}
				$begin = date("Y-m-d H:i:s",$begin);
				$end = date("Y-m-d H:i:s",$end);
				$anchors = '<a href="index.php?func=schedule&amp;computer='.$computer->getName().'&amp;begin='.$begin.'&amp;end='.$end.'" class="schedule">Schedule</a>';
			}
			$html .= '<div class="time_block '.strtolower($t->getType()).'">'.$t.$anchors.'</div>'."\n";
		}
	} else {
		$begin = date("Y-m-d H:i:s");
		$end = date("Y-m-d H:i:s",time()+$default_duration);
		$anchors = '<a href="index.php?func=schedule&amp;computer='.$computer->getName().'&amp;begin='.$begin.'&amp;end='.$end.'" class="schedule">Schedule</a>';
		$html .= '<div class="time_block available">AVAILABLE: '.$anchors.'</div>'."\n";
	}
	$html .= '<br class="clear" />'."\n";
	$schedule[$computer->getName()] = $temp;
}
echo $html;
?>
<hr />
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
<pre>
<?php print_r($schedule); ?>
</pre>
<hr />
<pre>
<?php print_r($computers); ?>
</pre>
<br />
<br />
</body>
</html>
