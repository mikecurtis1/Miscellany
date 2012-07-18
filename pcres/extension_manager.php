<?php 

// config
include('config.php');

// override config time for testing
#$cfg->now = time() - (60 * 25); // timeshift for testing
#$cfg->now = time() + (60 * 60 * 42); // timeshift for testing

// default values
$response = 'Unknown status';

// get 'reservation'
if($reservation = get_reservation($cfg->defaultusername,$cfg->day_begin,$cfg->day_end)){
	if(isset($reservation[0]['pcres_id'])){
		$pcres_id = $reservation[0]['pcres_id'];
	}
	if(isset($reservation[0]['start'])){
		$cfg->start = $reservation[0]['start'];
	}
	if(isset($reservation[0]['stop'])){
		$cfg->stop = $reservation[0]['stop'];
	}
	if(check_extension_window($cfg->stop,$cfg) === FALSE){
		$unix_start = strtotime($cfg->start) + $cfg->default_time_block;
		$unix_stop = strtotime($cfg->stop) + $cfg->default_time_block;
		$extended_start = date("Y-m-d H:i:s",$unix_start);
		$extended_stop = date("Y-m-d H:i:s",$unix_stop);
		$config_errors = check_start_stop($extended_start,$extended_stop,'extension',$cfg);
		if(count($config_errors)<=0){
			$response = 'This extension request is within the max allowed time.';
			if(extend_user($pcres_id,$extended_stop) === TRUE){
				$response = 'Reservation ['.$cfg->defaultusername.'] extended til '.$extended_stop.'.';
			}
		} else {
			print_r($config_errors);
		}
	} else {
		$response = 'You are NOT within the extension window ['.($cfg->extension_request_window_time/60).'m]: '.$cfg->stop;
	}
} else {
	$response = "Unable to get reservation for [{$cfg->defaultusername}]";
}

// display the data
header('Content-type: text/plain');
echo $response;

?>
