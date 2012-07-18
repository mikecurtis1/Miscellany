<?php 

// config
include('config.php');

// override config time for testing
#$cfg->now = time() - (60 * 25); // timeshift for testing
#$cfg->now = time() + (60 * 60 * 42); // timeshift for testing

// get 'reservation'

if($reservation = get_reservation($cfg->defaultusername,$cfg->day_begin,$cfg->day_end)){
	if(isset($reservation[0]['pcres_id'])){
		$pcres_id = $reservation[0]['pcres_id'];
	}
	/*if(isset($reservation[0]['start'])){
		$cfg->start = $reservation[0]['start'];
	}
	if(isset($reservation[0]['stop'])){
		$cfg->stop = $reservation[0]['stop'];
	}*/

	$temp_total_time = total_time_of_reservations($reservation);
	echo "total reservation time: {$temp_total_time}<br />\n";
	
	echo strtotime($reservation[0]['start']).' '.strtotime($reservation[0]['stop'])."<br />\n";
	
	echo strtotime($reservation[0]['stop']) - strtotime($reservation[0]['start'])."<br />\n";
	
}

?>
<pre>
<?php echo var_dump($cfg); ?>
</pre>
