<?php 

class HSLIdentifier 
{
	private $_type = '';
	private $_delimiter = '';
	private $_value = '';
		
	private function __construct($type=NULL,$delimiter=NULL,$value=NULL){
		$this->_type = $type;
		$this->_delimiter = $delimiter;
		$this->_value = $value;
	}
	
	static public function build($type=NULL,$delimiter=NULL,$value=NULL){
		if ( is_string($type) && is_string($delimiter) && is_string($value) ) {	
			return new HSLIdentifier($type,$delimiter,$value);
		} else {
			return FALSE;
	 	}
	}
	
	public function __toString(){
		return $this->_type.$this->_delimiter.$this->_value;
	}
	
	public function getType(){
		return $this->_type;
	}
	
	public function getDelimiter(){
		return $this->_delimiter;
	}
	
	public function getValue(){
		return $this->_value;
	}
	
	public function isType($type=''){
		if ( $type === $this->_type ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
