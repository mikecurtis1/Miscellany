<?php 

class MarcControlField
{
	private $_tag = NULL;
	private $_data = NULL;
	static private $_control_field_tags = array('FMT','001','003','005','006','007','008','009');
	private $_field_terminator = '';
	
	private function __construct($tag=NULL,$data=NULL){
		$this->_tag = $tag;
		$this->_data = $data;
		$this->_field_terminator = chr(30); // record separator
	}

	static public function build($tag=NULL,$data=NULL){
		if ( in_array($tag,self::$_control_field_tags,TRUE) && is_string($data) ) {	
			return new MarcControlField($tag,$data);
		} else {
			return FALSE;
	 	}
	}
	
	public function getTag(){
		return $this->_tag;
	}
	
	public function getData(){
		return $this->_data;
	}
	
	public function getControlField($arg=FALSE){
		if ( is_bool($arg) && $arg === TRUE ) {
			return trim($this->_data) . $this->_field_terminator;
		} else {
			return trim($this->_data);
		}
	}
}
?>
