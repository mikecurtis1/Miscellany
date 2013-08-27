<?php 
// debug error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
// includes
require_once(dirname(__FILE__).'/../../secure/php/classes/DbQuery.php');
require_once(dirname(__FILE__).'/../../secure/php/classes/DbConnection.php');
require_once(dirname(__FILE__).'/../../secure/php/classes/HttpRequest.php');
require_once(dirname(__FILE__).'/Computers.php');
require_once(dirname(__FILE__).'/Computer.php');
require_once(dirname(__FILE__).'/Schedule.php');
require_once(dirname(__FILE__).'/TimeBlock.php');
require_once(dirname(__FILE__).'/Event.php');
// init vars, TODO: move db info out of public web root
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'computer_keys';
$messages = array();
// create objects
if ( $dbc = DbConnection::create($db_host,$db_username,$db_password,$db_name) ) {
	if ( $c = Computers::create($dbc) ) {
		$computers = $c->getComputers();
	} else {
		die('Could NOT create Computers.');
	}
} else {
	die('Could NOT create Db connection.');
}
// gather user input 
$g_func = HttpRequest::getValue('func');
$g_computer = HttpRequest::getValue('computer');
$g_begin = HttpRequest::getValue('begin');
$g_end = HttpRequest::getValue('end');
$g_id = HttpRequest::getValue('id');
$g_new_computer = HttpRequest::getValue('new_computer');
$g_new_key = HttpRequest::getValue('new_key');
$g_deactivated_computer = HttpRequest::getValue('deactivated_computer');
$g_deactivated_key = HttpRequest::getValue('deactivated_key');
$g_extended_computer = HttpRequest::getValue('extended_computer');
$g_extended_key = HttpRequest::getValue('extended_key');
// process user input 
if ( $g_func === 'schedule' && $new_time_block = TimeBlock::create(strtotime($g_begin),strtotime($g_end),'SCHEDULED',0,'') ) {
	if ( $new_key = $computers[$g_computer]->getSchedule()->addNewTimeBlock($new_time_block) ) {
		header('Location: http://localhost/computer_keys/index.php?new_key='.$new_key.'&new_computer='.$g_computer);
	} else {
		$messages[] = '<div class="message"><span class="error">New time block <em>NOT</em> added. Check for time conflicts on '.$g_computer.' for '.$g_begin.' to '.$g_end.'</span></div>';
	}
} elseif ( $g_func === 'deactivate' && $g_id !== '' ) {
	if ( $computers[$g_computer]->getSchedule()->deactivateKey($g_id) ) {
		header('Location: http://localhost/computer_keys/index.php?deactivated_computer='.$g_computer.'&deactivated_key='.$g_id);
	} else {
		$messages[] = '<div class="message"><span class="error">ID: '.$g_id.' <em>NOT</em> de-activated.</span></div>';
	}
} elseif ( $g_func = 'extend' && $g_id !== '' ) {
	if ( $computers[$g_computer]->getSchedule()->extendEnd($g_id,(60*20)) ) {
		header('Location: http://localhost/computer_keys/index.php?extended_computer='.$g_computer.'&extended_key='.$g_id);
	} else {
		$messages[] = '<div class="message"><span class="error">ID: '.$g_id.' <em>NOT</em> extended.</span></div>';
	}
}
// compose markup for system response
if ( $g_new_computer !== '' && $g_new_key !== '' ) {
	$messages[] = '<div class="message"><span class="success">New time block added on <span class="new_computer">'.$g_new_computer.'</span>. KEY:<span class="new_key">'.$g_new_key.'</span></span></div>';
}
if ( $g_deactivated_computer !== '' && $g_deactivated_key !== '' ) {
	$messages[] = '<div class="message"><span class="success">Time block de-activated on <span class="deactivated_computer">'.$g_deactivated_computer.'</span>. KEY:<span class="deactivated_key">'.$g_deactivated_key.'</span></span></div>';
}
if ( $g_extended_computer !== '' && $g_extended_key !== '' ) {
	$messages[] = '<div class="message"><span class="success">Time block extended on <span class="extended_computer">'.$g_extended_computer.'</span>. KEY:<span class="extended_key">'.$g_extended_key.'</span></span></div>';
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
<div class="current_time">Current Time: <?php echo date("M, j g:i:s a"); ?></div>
<div class="reload"><a href="index.php">Re-load</a> page</div>
<div class="messages">
<?php 
if ( !empty($messages) ) {
	foreach ( $messages as $message ) {
		echo $message."\n";
	}
} 
?>
</div>
<div class="schedule">
<?php 
$schedule = array();
$html = '';
$default_duration = (60*60);
foreach ( $computers as $computer ) {
	$anchors = '';
	$temp = array_merge($computer->getSchedule()->getTimeBlocks(),$computer->getSchedule()->getAvailableTimeBlocks());
	usort($temp,array('Schedule','cmpTimeBlocks'));
	$html .= '<h2>'.$computer->getName().'</h2>'."\n";
	if ( !empty($temp) ) {
		foreach ( $temp as $t ) {
			$anchors = '';
			if ( $t->getType() === 'SCHEDULED' ) {
				$anchors = '<a href="index.php?func=extend&amp;computer='.$computer->getName().'&amp;id='.$t->getId().'" class="extend">Extend</a><a href="index.php?func=deactivate&amp;computer='.$computer->getName().'&amp;id='.$t->getId().'" class="deactivate">De-Activate</a><span class="id">ID:'.$t->getId().'</span><span class="key">KEY:<a href="windows_client_manager.php?computer='.$computer->getName().'&amp;key='.$t->getKey().'&amp;debug=debug">'.$t->getKey().'</a></span>';
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
			$html .= '<div class="time_block '.strtolower($t->getType()).'"><span class="data">'.$t.'</span>'.$anchors.'</div>'."\n";
		}
	} else {
		$begin = date("Y-m-d H:i:s");
		$end = date("Y-m-d H:i:s",time()+$default_duration);
		$anchors = '<a href="index.php?func=schedule&amp;computer='.$computer->getName().'&amp;begin='.$begin.'&amp;end='.$end.'" class="schedule">Schedule</a>';
		$html .= '<div class="time_block available">AVAILABLE: '.date("g:i a").' to 11:59 pm '.$anchors.'</div>'."\n";
	}
	$html .= '<br class="clear" />'."\n";
	$schedule[$computer->getName()] = $temp;
}
echo $html;
?>
</div>
<h3>Manual Schedule Form</h3>
<form action="" method="get">
<input type="hidden" name="func" value="schedule" />
Computer: 
<select name="computer">
<?php 
foreach ( $computers as $computer ) {
	echo "<option value=\"{$computer->getName()}\">{$computer->getName()}</option>\n";
}
?>
</select>
<div>Begin: <input type="text" name="begin" value="<?php echo date("Y-m-d h:i A"); ?>" /></div>
<div>End: <input type="text" name="end" value="<?php echo date("Y-m-d h:i A",time()+1200); ?>" /></div>
<div><input type="submit" value="submit" /></div>
</form>
</body>
</html>
