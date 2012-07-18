<?php
// function to process room key records
function process_room_key($v, $availability_match_phrase){
  $room_key = array(
  'z30_description', 
  'loan_status', 
  'due_hour', 
  'due_date', 
  'availability' => false, 
  'availability_statement' => 'This room is unavailable.', 
  'style' => 'unavailable');
  if(isset($v['z30-description'][0])){
    $room_key['z30_description'] = $v['z30-description'][0];
  }
  if(isset($v['loan-status'][0])){
    $room_key['loan_status'] = $v['loan-status'][0];
  }
  if(isset($v['due-hour'][0])){
    $room_key['due_hour'] = $v['due-hour'][0];
  }
  if(isset($v['due-date'][0])){
    $room_key['due_date'] = $v['due-date'][0];
    if($v['due-date'][0] == $availability_match_phrase){
      $room_key['availability'] = true;
      $room_key['availability_statement'] = 'Available.';
      $room_key['style'] = 'available';
    }
    else{
      $room_key['availability_statement'] = "Available after {$room_key['due_date']} {$room_key['due_hour']}.";
    }
  }
  return $room_key;
}
?>
