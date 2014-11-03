<?php 

class MarcDataField
{
	private $_tag = NULL;
	private $_identifier1 = NULL;
	private $_identifier2 = NULL;
	private $_subfields = array();
	private $_field_terminator = '';
	
	private function __construct($tag=NULL,$i1=NULL,$i2=NULL){
		$this->_tag = $tag;
		$this->_identifier1 = $i1;
		$this->_identifier2 = $i2;
		$this->_field_terminator = chr(30); // record separator
	}

	static public function build($tag=NULL,$i1=NULL,$i2=NULL){
		if ( ( is_string($tag) && strlen($tag) === 3 ) && ( is_string($i1) && strlen($i1) === 1 ) && ( is_string($i2) && strlen($i2) === 1 ) ) {	
			return new MarcDataField($tag,$i1,$i2);
		} else {
			return FALSE;
	 	}
	}
	
	public function addSubfield($arg=NULL){
		if ( $arg instanceof MarcSubfield ) {
			$this->_subfields[$arg->getCode()][] = $arg->getData();
		}
	}
	
	public function getTag(){
		return $this->_tag;
	}
	
	public function getId1(){
		return $this->_identifier1;
	}
	
	public function getId2(){
		return $this->_identifier2;
	}
	
	public function getSubfields(){
		return $this->_subfields;
	}
	
	public function getDataField($arg=FALSE){
		if ( is_bool($arg) && $arg === TRUE ) {
			return $this->_identifier1 . $this->_identifier2 . trim($this->_subfields) . $this->_field_terminator;
		} else {
			return $this->_identifier1 . $this->_identifier2 . trim($this->_subfields);
		}
	}
}
?>
