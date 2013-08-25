<?php 

class TimeBlock
{
	private $_begin = NULL;
	private $_end = NULL;
	private $_type = NULL;
	private static $_type_scheduled = 'SCHEDULED';
	private static $_type_available = 'AVAILABLE';
	
	private function __construct($begin=NULL,$end=NULL,$type=NULL){
		$this->_begin = $begin;
		$this->_end = $end;
		$this->_type = $type;
	}
	
	public static function create($begin=NULL,$end=NULL,$type=NULL){
		if ( is_numeric($begin) && is_numeric($end) && ($type === self::$_type_scheduled || $type === self::$_type_available) && ($begin < $end) ) {
			return new TimeBlock($begin,$end,$type);
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
	
	public function getType(){
		return $this->_type;
	}
	
	public function __toString(){
		return $this->_type.': '.date("M, j g:i:s a",$this->_begin).' to '.date("M, j g:i:s a",$this->_end);
    }
}
?>
