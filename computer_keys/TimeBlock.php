<?php 

class TimeBlock
{
	private $_begin = NULL;
	private $_end = NULL;
	
	private function __construct($begin=NULL,$end=NULL){
		$this->_begin = $begin;
		$this->_end = $end;
	}
	
	public static function create($begin=NULL,$end=NULL){
		if ( is_numeric($begin) && is_numeric($end) && ($begin < $end) ) {
			return new TimeBlock($begin,$end);
		} else {
			return FALSE;
		}
	}
	
	public function getBegin(){
		return $this->_begin;
	}
	
	public function getEnd(){
		return $this->_end;
	}
	
	public function __toString(){
		return date("M, j g:i:s a",$this->_begin).' to '.date("M, j g:i:s a",$this->_end);
    }
}
?>
