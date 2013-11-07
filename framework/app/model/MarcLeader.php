<?php 

class MarcLeader
{
	private $_data = NULL;
	
	private function __construct($data=NULL){
		$this->_data = $data;
	}

	static public function build($data=NULL){
		if ( is_string($data) && strlen($data) === 24 ) {	
			return new MarcLeader($data);
		} else {
			return FALSE;
	 	}
	}
	
	public function getData(){
		return $this->_data;
	}
}
?>
