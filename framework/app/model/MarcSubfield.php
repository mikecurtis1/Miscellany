<?php 

class MarcSubfield
{
	private $_code = NULL;
	private $_data = NULL;
	private $_subfield_indicator = ''; // prefix to subfield codes
	private $_subfield_indicator_graphic = '';
	
	private function __construct($code=NULL,$data=NULL,$subfield_indicator_graphic=''){
		$this->_code = $code;
		$this->_data = $data;
		$this->_subfield_indicator = chr(31); // unit separator
		$this->_subfield_indicator_graphic = $subfield_indicator_graphic;
	}

	static public function build($code=NULL,$data=NULL,$subfield_indicator_graphic='$'){
		if ( ( is_string($code) && strlen($code) === 1 ) && is_string($data) && ( is_string($subfield_indicator_graphic) && strlen($subfield_indicator_graphic) === 1 ) ) {	
			return new MarcSubfield($code,$data,$subfield_indicator_graphic);
		} else {
			return FALSE;
	 	}
	}
	
	public function getCode(){
		return $this->_code;
	}
	
	public function getData(){
		return $this->_data;
	}
	
	public function getSubfieldStr($arg=FALSE){
		if ( $arg === TRUE ) {
			$d = str_replace($this->_subfield_indicator_graphic, '\\' . $this->_subfield_indicator_graphic, trim($this->_data));
			return trim($this->_code) . $this->_subfield_indicator_graphic . $d;
		} else {
			return trim($this->_code) . $this->_subfield_indicator . trim($this->_data);
		}
	}
}
?>
