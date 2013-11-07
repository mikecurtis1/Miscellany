<?php 

class MarcSubfield
{
	private $_code = NULL;
	private $_data = NULL;
	
	private function __construct($code=NULL,$data=NULL){
		$this->_code = $code;
		$this->_data = $data;
	}

	static public function build($code=NULL,$data=NULL){
		if ( ( is_string($code) && strlen($code) === 1 ) && is_string($data) ) {	
			return new MarcSubfield($code,$data);
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
}
?>
