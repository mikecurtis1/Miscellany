<?php
// time for browser cache
$fifteen_minutes = time() + (15 * 60);
$expires = date("D, j M Y H:i:s",$fifteen_minutes)." GMT";
header("Expires: ".$expires);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Upstate Health Sciences Library :: Study Rooms</title>
<link rel="stylesheet" href="room_keys.css" type="text/css" media="screen">
</head>
<body>
<?php

// open controllers
require_once('controllers.php');

// open xml file
// TO-DO: some cache function should be used to reduce Aleph requests
$url = "http://ups.sunyconnect.suny.edu:4360/X?op=circ_status&sys_no=000087884&library=ups01";
$xml = implode('', file($url));

// use XMLthing parser to put data in a PHP array
//TODO: replace xmlthing with simplexml parser
require_once('xmlthing.php');
$parser = new XMLThing();
$study_room_keys_array_temp = $parser->parse($xml);
$study_room_keys_array = $study_room_keys_array_temp['circ-status'][0]['item-data'];

// availability match phrase
$availability_match_phrase = 'Available';

// process view for each room key
foreach($study_room_keys_array as $i => $v){
  $room_key = process_room_key($v, $availability_match_phrase);
?>
<div class="room <?php echo $room_key['style']; ?>">
<div><?php echo $room_key['z30_description']; ?></div>
<div><?php echo $room_key['loan_status']; ?></div>
<div><?php echo $room_key['availability_statement']; ?></div>
</div>
<?php 
}
?>
</body>
</html>
