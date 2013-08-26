<?php 

class Schedule
{
	private $_day_begin = NULL;
	private $_day_end = NULL;
	private $_time_blocks = array();
	private $_available_time_blocks = array();

	public function __construct(){
		$this->_day_begin = (string) strtotime(date("Y-m-d").' 00:00:00');
		$this->_day_end = (string) strtotime(date("Y-m-d").' 23:59:59');
  }
}
?>
