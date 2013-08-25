<?php 

class TimeBlock
{
	private $_begin = NULL;
	private $_end = NULL;
	private $_begin_unix = NULL;
	private $_end_unix = NULL;
	
	private function __construct($begin=NULL,$end=NULL){
		$this->_begin_unix = $begin;
		$this->_end_unix = $end;
		$this->_begin = date("Y-m-d H:i:s",$begin);
		$this->_end = date("Y-m-d H:i:s",$end);
	}
	
	public static function create($begin=NULL,$end=NULL){
		if ( strtotime($begin) && strtotime($end) && (strtotime($begin) < strtotime($end)) ) {
			return new TimeBlock(strtotime($begin),strtotime($end));
		} else {
			return FALSE;
		}
	}
	
	public function getBegin(){
		return $this->_begin;
	}
	
	public function getEnd(){
		return $this->_begin;
	}
	
	public function getBeginUnix(){
		return $this->_begin_unix;
	}
	
	public function getEndUnix(){
		return $this->_end_unix;
	}
}
?>
