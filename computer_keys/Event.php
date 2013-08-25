<?php 

class Event
{
	private $_type = NULL;
	private $_time = NULL;
	
	private function __construct($type=NULL,$time=NULL){
		$this->_type = $type;
		$this->_time = $time;
	}
	
	public static function create($type=NULL,$time=NULL){
		if ( is_string($type) && is_numeric($time) ) {
			return new Event($type,$time);
		} else {
			return FALSE;
		}
	}
	
	public function getType(){
		return $this->_type;
	} 
	
	public function getTime(){
		return $this->_time;
	}
	
	public function __toString(){
		return $this->_type.' '.date("Y-m-d g:i:s a",$this->_time);
    }
}
?>
