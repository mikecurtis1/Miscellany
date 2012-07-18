<?php 

class PcResConfig {
	
	function __construct($get) {
		$this->loadValues('config.json');
		$this->loadGetArray($get);
	}

	private function loadValues($file=NULL){
		if(is_file($file)){
			$contents = file_get_contents($file);
			$json = json_decode($contents);
			// basic scheduling values
			$this->seats = $json->seats;
			$this->default_time_block = 60 * $json->default_time_block_mins; 
			// calculate extension values
			$this->extension_max = $json->extension_max;
			$this->extension_max_time = ($config_default_time_block + ($this->default_time_block * $this->extension_max))-1;
			$this->extension_request_window_time = 60 * $json->extension_request_window_mins;
			// calculate limits for Aleph barcode identities
			#$this->reservations_daily_total_time = ($this->default_time_block + ($this->default_time_block * $this->extension_max)) * $json->reservations_daily_total_blocks;
			$this->reservations_daily_total_time = 60 * $json->reservations_daily_total_time_min;
			$this->reservation_gap_time = ($this->default_time_block + ($this->default_time_block * $json->reservation_gap_blocks))-1;
			// set time/clock variables
			$this->now = time() + $json->timeshift;
			$this->today = date("Y-m-d",$this->now);
			$this->length_of_day = ($this->default_time_block * $json->length_of_day_blocks) - 1;
			// reset day when time block extends over a standard 24 day
			$this->extend_begin = $this->today.' 00:00:01';
			$this->extend_end = $this->today.$json->extend_end_time;
			if((strtotime($this->extend_begin) <= $this->now) && (strtotime($this->extend_end) >= $this->now)){
				$this->today = date("Y-m-d",$this->now - (60*60*24));
			}
			// set 'day' beginning and end times
			$this->day_begin = $this->today.$json->day_begin_time;
			$this->day_end = date("Y-m-d H:i:s",strtotime($this->day_begin)+$this->length_of_day);
		}
	}
		
	private function loadGetArray($get_array=array()){
		if(isset($get_array['operation'])){$this->operation = $get_array['operation'];}
		if(isset($get_array['action'])){$this->action = $get_array['action'];}
		if(isset($get_array['start'])){$this->start = $get_array['start'];}
		if(isset($get_array['stop'])){$this->stop = $get_array['stop'];}
		if(isset($get_array['defaultusername'])){$this->defaultusername = $get_array['defaultusername'];}
		if(isset($get_array['barcode'])){$this->barcode = $get_array['barcode'];}
		if(isset($get_array['username'])){$this->username = $get_array['username'];}
		if(isset($get_array['computername'])){$this->computername = $get_array['computername'];}
	}
	
}

?>
