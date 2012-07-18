<?php 

// config
include('config.php');

// don't allow duplicate reservations
$dups = find_duplicate_reservations($cfg->start,$cfg->stop,$cfg->barcode);
if(count($dups)>0){
	$config_errors[] = 'This request is a duplicate reservation.';
}

// do some error checking of the requested time values
$temp_config_errors = check_start_stop($cfg->start,$cfg->stop,'reservation',$cfg);
$config_errors = array_merge($temp_config_errors, $config_errors);

// require a valid Aleph barcode
if(authenticate_aleph_barcode($cfg->barcode) === FALSE){
	$config_errors[] = 'A valid Aleph barcode is required to make a reservation. [barcode:'.$cfg->barcode.']';
}

// check time gap between reservations for Aleph barcode
$res_by_bar = get_reservation(NULL,$cfg->day_begin,$cfg->day_end,$cfg->barcode,TRUE);
if(reservation_gap_too_small($res_by_bar,$cfg->start) === TRUE){
	$config_errors[] = 'You must allow a time gap between reservations. (minimum gap: '.$cfg->reservation_gap_time.' seconds.)';
}

// check total time for the day for Aleph barcode
if(res_over_daily_limit($res_by_bar) === TRUE){
	$config_errors[] = 'You cannot reserve more time blocks than the daily limit: ['.$cfg->reservations_daily_total_blocks.']';
}

/* this error checking of time values should 
be separated into a function called by other 
time functions. It shouldn't be happening inside
a template or operation script */
// run error function if errors found
if(count($config_errors) > 0){
	$config_errors[] = "START: {$cfg->start} &amp; STOP: {$cfg->stop}";
	$config_function = "Errors";
	errors($config_errors);
}

// get a username that is unique for the 'day'
$unique_check = FALSE;
while($unique_check != TRUE){
	$pw = generate_password();
	$unique_check = check_username_unique($pw,$cfg->day_begin,$cfg->day_end);
}

// make schedule entry
if(schedule_user(NULL,$pw,$pw,$cfg->barcode,$cfg->start,$cfg->stop)){
	header("Location: index.php?operation=Confirmation&pw={$pw}&start={$cfg->start}&stop={$cfg->stop}&barcode={$cfg->barcode}");
} else {
	$config_errors[] = "Could not schedule the request, &quot;schedule_user&quot; failed.";
}

// run error function if errors found
if(count($config_errors) > 0){
	$config_function = "Errors";
	errors($config_errors);
}

?>
