<?php

function errors ($config_errors = array()){
	include('template_errors.php');
	exit;
}

function generate_password (){
	$password = 'password';
	$consonants = str_split('bcdfghjklmnprstvwz'); // In English, q, y, and x make harder to pronounce syllables, don't use those consonants.
	$vowels = str_split('aeiou');
	$numbers = str_split('0123456789');
	$password = $consonants[rand(0,count($consonants)-1)]
		.$vowels[rand(0,count($vowels)-1)]
		.$consonants[rand(0,count($consonants)-1)]
		.$vowels[rand(0,count($vowels)-1)]
		.$numbers[rand(0,count($numbers)-1)]
		.$numbers[rand(0,count($numbers)-1)];
	return $password;
}

function barometer ($dividend=1,$divisor=1,$split=NULL,$mode=NULL){
	$barometer = '';
		if(!is_int($split)){
			$split = 25;
		}
	$percentage = intval(($dividend/$divisor)*100);
		if($mode == 'colors'){
			if($percentage >= $split){$barometer = '#99FF99';} // green
			if($percentage > 0 && $percentage < $split){$barometer = '#FFFF66';} // yellow
			if($percentage <= 0){$barometer = '#FF9999';} // red 
		} else {
			$barometer = $percentage;
		}
	return $barometer;
}

function clock_remaining ($start,$stop){
	$return['seconds'] = 0;
	$return['clock'] = '00:00:00';
	if(!(is_int($stop) && is_int($stop))){
		return (boolean) FALSE;
	}
	$sec_remaining = $stop - $start;
	if($sec_remaining < 0){
		$return['seconds'] = $sec_remaining;
		return $return;
	}
	$clock_day_remaining = intval((($stop - $start) / (60 * 60 * 24)));
	$clock_day_remaining_phrase = '';
		if($clock_day_remaining > 0){
		$clock_day_remaining_phrase = number_format($clock_day_remaining).' days, ';
		}
	$clock_hrs_remaining = sprintf("%02d",intval((($stop - $start) / (60 * 60)) % 24));
	$clock_min_remaining = sprintf("%02d",intval((($stop - $start) / 60) % 60));
	$clock_sec_remaining = sprintf("%02d",($stop - $start) % 60);
	$return['seconds'] = $sec_remaining;
	$return['clock'] = $clock_day_remaining_phrase.$clock_hrs_remaining.':'.$clock_min_remaining.':'.$clock_sec_remaining;
	return $return;
}

function create_time_blocks ($cfg) {
	$blocks = array();
	$start_timestamp = strtotime($cfg->day_begin);
	$stop_timestamp = $start_timestamp+($cfg->default_time_block-1);
	$end_timestamp = strtotime($cfg->day_end);
	$c = 0;
	while($stop_timestamp <= $end_timestamp){
		$blocks[$c]['start']['timestamp'] = $start_timestamp;
		$blocks[$c]['start']['sql'] = date("Y-m-d H:i:s",$start_timestamp);
		$blocks[$c]['stop']['timestamp'] = $stop_timestamp;
		$blocks[$c]['stop']['sql'] = date("Y-m-d H:i:s",$stop_timestamp);
		$start_timestamp = $start_timestamp + $cfg->default_time_block;
		$stop_timestamp = $stop_timestamp + $cfg->default_time_block;
		$c++;
	}
	return $blocks;
}

function check_valid_time_block ($start,$stop,$cfg) {
	$start_mod = (strtotime($start)-strtotime($cfg->day_begin)) % $cfg->default_time_block;
	$stop_mod = (strtotime($stop)-strtotime($cfg->day_end)) % $cfg->default_time_block;
	if($start_mod == 0 && $stop_mod == 0){
		return (boolean) TRUE;
	}
	return (boolean) FALSE;
}

function check_start_stop($start,$stop,$mode=NULL,$cfg){
	$errors = array();
	if((strtotime($start) == false) || (strtotime($start) == false)){
		$errors[] = 'Both boxes must contain valid date/time values.';
	}
	if(!check_valid_time_block($start,$stop,$cfg)){
		$errors[] = 'The times you entered do not match a defined time block.';
	}
	if(strtotime($stop) <= time()){
		$errors[] = 'STOP time must be in the future.';
	}
	if((strtotime($start) >= strtotime($stop))){
		$errors[] = 'START time must be before the STOP time.';
	}
	if($mode == NULL){
		$errors[] = 'Mode expected in check_start_stop().';
	}
	$open_seats = $cfg->seats - find_schedule_conflicts($start,$stop,TRUE,$cfg->defaultusername,TRUE,TRUE);
	if($open_seats <= 0){
		$errors[] = 'There are no open seats for this time range: open seats = '.$open_seats.'.';
	}
	$temp_resvs = get_reservation(NULL,NULL,NULL,$cfg->barcode);
	$temp_total_time = total_time_of_reservations($temp_resvs) + (strtotime($stop) - strtotime($start));
	if($temp_total_time >= $cfg->reservations_daily_total_time){
		$errors[] = 'You cannot request more time ('.$temp_total_time.') than the daily limit ['.$cfg->reservations_daily_total_time.']';
	}
	if($mode == 'reservation'){
		if((strtotime($stop) - strtotime($start)) > $cfg->default_time_block){
			$errors[] = 'Reservation length cannot exceed one time block. '.$cfg->default_time_block.' '.(strtotime($stop) - strtotime($start));
		}
	}
	if($mode == 'extension'){
		if(strtotime($stop) > strtotime($cfg->day_end)){
			$errors[] = 'You cannot extend past the end of the day: '.$stop.' '.$cfg->day_end;
		}
		$total_reservation_time = (strtotime($stop) - strtotime($start));
		echo "{$start}, {$stop}. {$total_reservation_time} > {$cfg->extension_max_time}\n";
		if($total_reservation_time > $cfg->extension_max_time){
			$errors[] = 'This extension request exceeds the maximum time allowed, please make a new reservation.';
		}
	}
	return $errors;
}

function check_workstation_for_current_reservation ($workstation=NULL,$now=NULL) {
	if($now == NULL){
		$now = date("Y-m-d H:i:s",time());
	}
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$sql = "
		SELECT start,stop,status,client,samba FROM `pcres` 
		WHERE workstation = '{$workstation}' AND start <= '{$now}' AND stop >= '{$now}'
	";
	if(($result = mysql_query($sql)) !== FALSE){
		mysql_close($link);
		if(is_array(mysql_fetch_assoc($result))){
			return TRUE;
		} else {
			return FALSE;
		}
	} else {
		mysql_close($link);
		return (boolean) FALSE;
	}
}

function find_schedule_conflicts ($start,$stop,$count=TRUE,$username=NULL,$exclude_default_user=FALSE,$only_active_reservations=FALSE) {
	$exclude_default_user_sql = '';
	$only_active_reservations_sql = '';
	if($exclude_default_user==TRUE){
		$exclude_default_user_sql = "(NOT username = '{$username}') AND ";
	}
	if($only_active_reservations==TRUE){
		$only_active_reservations_sql = "(samba = 'active') AND ";
	}
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$sql = "
		SELECT * 
		FROM `pcres` 
		WHERE {$exclude_default_user_sql}{$only_active_reservations_sql}((start <= '{$start}' AND stop >= '{$start}') OR (stop >= '{$stop}' AND start <= '{$stop}'))
	";
	if(($result = mysql_query($sql)) !== FALSE){
		while($row = mysql_fetch_assoc($result)){
			$conflicts[] = $row;
		}
		mysql_close($link);
		if($count==TRUE){
			return count($conflicts);
		} else {
			return $conflicts;
		}
	} else {
		return (boolean) FALSE;
	}
}

function find_duplicate_reservations ($start,$stop,$barcode=NULL) {
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$sql = "
		SELECT * 
		FROM `pcres` 
		WHERE barcode = '{$barcode}' AND ((start <= '{$start}' AND stop >= '{$start}') OR (stop >= '{$stop}' AND start <= '{$stop}'))
	";
	if(($result = mysql_query($sql)) !== FALSE){
		while($row = mysql_fetch_assoc($result)){
			$conflicts[] = $row;
		}
		mysql_close($link);
		if($count==TRUE){
			return count($conflicts);
		} else {
			return $conflicts;
		}
	} else {
		return (boolean) FALSE;
	}
}

function check_username_unique($username,$start,$stop){
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$sql = "
		SELECT COUNT(*) as count 
		FROM `pcres` 
		WHERE username = '{$username}' AND start = '{$start}' AND stop = '{$stop}'
	";
	$result = mysql_query($sql);
	$db = array();
	while($row = mysql_fetch_assoc($result)){
		$count = $row['count'];
	}
	mysql_close($link);
	if($count==0){
		return (boolean) TRUE;
	}
	return (boolean) FALSE;
}

function get_reservation($username=NULL,$start=NULL,$stop=NULL,$barcode=NULL,$only_active_reservations=FALSE){
	$username_sql = '';
	$timeblock_sql = '';
	$barcode_sql = '';
	if($username != NULL){
		$username_sql = "(username = '{$username}') AND ";
	}
	if($start != NULL && $stop != NULL){
		$timeblock_sql = "(start >= '{$start}' AND stop <= '{$stop}') AND ";
	}
	if($barcode != NULL){
		$barcode_sql = "(barcode = '{$barcode}') AND ";
	}
	if($only_active_reservations==TRUE){
		$only_active_reservations_sql = "(samba = 'active') AND ";
	}
	$where = substr($username_sql.$timeblock_sql.$barcode_sql.$only_active_reservations_sql,0,-5);
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$sql = "
		SELECT * FROM `pcres` 
		WHERE {$where}
	";
	if(($result = mysql_query($sql)) !== FALSE){
		$db = array();
		while($row = mysql_fetch_assoc($result)){
			$db[] = $row;
		}
		mysql_close($link);
		return $db;
	} else {
		mysql_close($link);
		return (boolean) FALSE;
	}
}

function total_time_of_reservations($reservations=array()){
	$seconds = (int) 0;
	foreach($reservations as $reservation){
		if(isset($reservation['start']) && isset($reservation['stop'])){
			$seconds += ((strtotime($reservation['stop'])) - strtotime($reservation['start']));
		}
	}
	return $seconds;
}

function check_extension_window($stop,$cfg){
	$return = (boolean) FALSE;
	// from here... FIXME:
	$unix_stop = strtotime($stop);
	$clock_stop = date("H:i:s",$unix_stop);
	if($clock_remaining = clock_remaining($cfg->now,$unix_stop)){
		$sec_remaining = $clock_remaining['seconds'];
		$clock_remaining = $clock_remaining['clock'];
	}
	// ...to here should be a unique function
	if(($sec_remaining > 0) && ($sec_remaining > $cfg->extension_request_window_time)){
		$return = (boolean) TRUE;
	}
	return $return;
}

function authenticate_aleph_barcode ($barcode='') {
	$response = file_get_contents('http://ups.sunyconnect.suny.edu:4360/X?op=bor-by-key&library=ups50&bor_id='.$barcode);
	if(preg_match("/\<internal\-id\>(.*?)\<\/internal\-id\>/is",$response,$matches)){
		return $matches[1];
	} else {
		return FALSE;
	}
}

function reservation_gap_too_small ($reservations=array(),$start) {
	$res_gap_check = 0;
	foreach($reservations as $reservation){
		$diff = strtotime($start) - strtotime($reservation['start']);
		if(($diff < $cfg->reservation_gap_time) && ($diff > 0)){
			$res_gap_check++;
		}
		if($res_gap_check > 0){
			$reservation_gap_too_small = TRUE;
		} else {
			$reservation_gap_too_small = FALSE;
		}
	}
	return $reservation_gap_too_small;
}

function res_over_daily_limit ($reservations=array()){
	if(count($reservations)>$cfg->reservations_daily_total_time){
		$res_over_daily_limit = TRUE;
	} else {
		$res_over_daily_limit = FALSE;
	}
	return $res_over_daily_limit;
}

/* function below this line MODIFY table data 
 */

 function client_checkin($pcres_id,$computername){
	$date = date("Y-m-d H:i:s");
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$sql = "
		UPDATE pcres 
		SET `last_checkin` = '{$date}', `workstation` = '{$computername}', `client` = 'checked in'
		WHERE pcres_id = {$pcres_id} 
	";
	if(mysql_query($sql) === TRUE){
		mysql_close($link);
		return (boolean) TRUE;
	} else {
		mysql_close($link);
		return (boolean) FALSE;
	}
}

function schedule_user ($workstation,$username,$password,$barcode,$start,$stop){
	$date = date("Y-m-d H:i:s");
	$response = array();
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$sql = "
		INSERT INTO `pcres` (`pcres_id`,`workstation`,`username`,`password`,`barcode`,`start`,`stop`,`last_checkin`,`client`,`samba`) 
		VALUES ('','','{$username}','{$password}','{$barcode}','{$start}','{$stop}','{$date}','pending','active')
	";
	if(mysql_query($sql) === TRUE){
		mysql_close($link);
		return (boolean) TRUE;
	} else {
		mysql_close($link);
		return (boolean) FALSE;
	}
}

function extend_user($pcres_id,$stop){
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$sql = "
		UPDATE pcres 
		SET `stop` = '{$stop}'
		WHERE pcres_id = {$pcres_id} 
	";
	if(mysql_query($sql) === TRUE){
		mysql_close($link);
		return (boolean) TRUE;
	} else {
		mysql_close($link);
		return (boolean) FALSE;
	}
}

?>
