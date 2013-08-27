<?php 

class Computer
{
	private $_name = NULL;
	private $_ip = NULL;
	private $_id = NULL;
	private $_schedule = NULL;
	
	private function __construct($name=NULL,$id=NULL,$ip=NULL){
		$this->_name = $name;
		$this->_id = $id;
		$this->_ip = $ip;
	}
	
	public static function create($name=NULL,$id=NULL,$ip=NULL){
		if ( is_string($name) && is_numeric($id) && filter_var($ip,FILTER_VALIDATE_IP) ) {
			return new Computer($name,$id,$ip);
		} else {
			return FALSE;
		}
	}
	
	public function setSchedule($arg=NULL){
		$this->_schedule = Schedule::create($arg,$this->_id);
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function getIP(){
		return $this->_ip;
	}
	
	public function getSchedule(){
		return $this->_schedule;
	}
}
?>
