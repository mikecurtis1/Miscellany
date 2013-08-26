<?php 

class TimeBlock
{
	private $_begin = NULL;
	private $_end = NULL;
	private $_type = NULL;
	private $_id = NULL;
	private $_key = NULL;
	private static $_type_scheduled = 'SCHEDULED';
	private static $_type_available = 'AVAILABLE';
	
	private function __construct($begin=NULL,$end=NULL,$type=NULL,$id=NULL,$key=NULL){
		$this->_begin = $begin;
		$this->_end = $end;
		$this->_type = $type;
		$this->_id = $id;
		$this->_key = $key;
	}
	
	public static function create($begin=NULL,$end=NULL,$type=NULL,$id=NULL,$key=NULL){
		if ( is_numeric($begin) && is_numeric($end) && ($type === self::$_type_scheduled || $type === self::$_type_available) && ($begin < $end) && is_numeric($id) && is_string($key) ) {
			return new TimeBlock($begin,$end,$type,$id,$key);
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
	
	public function getId(){
		return $this->_id;
	}
	
	public function getKey(){
		return $this->_key;
	}
	
	public function __toString(){
		#return $this->_type.': '.date("M, j g:i:s a",$this->_begin).' to '.date("M, j g:i:s a",$this->_end);
		return $this->_type.': '.date("g:i a",$this->_begin).' to '.date("g:i a",$this->_end);
	}
}
?>
