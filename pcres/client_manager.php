<?php 

// config
include('config.php');

// override config time for testing
#$config_now = time() - (60 * 25); // timeshift for testing
#$config_now = time() + (60 * 60 * 42); // timeshift for testing

// default values
$status = 'logout';
$sec_remaining = 0;
$clock_now = date("H:i:s",$cfg->now);
$clock_stop = '00:00:00';
$clock_remaining = '00:00:00';

// get 'reservation'
if($reservation = get_reservation($cfg->defaultusername,$cfg->day_begin,$cfg->day_end)){
	// perform client check in
	if(isset($reservation[0]['pcres_id']) && isset($reservation[0]['stop'])){
		$unix_stop = strtotime($reservation[0]['stop']);
		$clock_stop = date("H:i:s",$unix_stop);
		if($clock_remaining = clock_remaining($cfg->now,$unix_stop)){
			$sec_remaining = $clock_remaining['seconds'];
			$clock_remaining = $clock_remaining['clock'];
		}
		if($sec_remaining > 0){
			$status = 'active';
		}
		if(client_checkin($reservation[0]['pcres_id'],$cfg->computername) == FALSE){
			$status .= ":unable to check in for [{$cfg->defaultusername}]";
		}
	}
} else {
	$status = "error:unable to get reservation for [{$cfg->defaultusername}]";
}
// set time/clock values for the reservation
$response = "{$status}|{$sec_remaining}|{$clock_now}|{$clock_stop}|{$clock_remaining}";

// display the data
echo $response;

?>
