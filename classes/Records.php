<?php 

class Records 
{
  public $records;
	
	public function __construct(){
		$this->records = array();
	}
	
	public function setRecord($arg){
		if ( $arg instanceof Record ) {
			$this->records[] = $arg;
		}
	}
	
	public function toArray(){
		return $this->_casteObjectsToArrays($this->records);
	}
	
	private function _casteObjectsToArrays($arg) {
		if ( is_object($arg) ) {
			$arg = (array) $arg;
		}
		if ( is_array($arg) ) {
			$new = array();
			foreach($arg as $key => $val) {
				//$new[$key] = $this->_casteObjectsToArrays($val); 
				//TODO: remove the null bytes from key and value which are created when casting to array
				// maybe only trim() is needed if null byte is a beginning of string
				$new[$this->_removeNullBytes($key)] = $this->_casteObjectsToArrays($this->_removeNullBytes($val));
			}
		}
		else { 
			$new = $arg;
		}
		return $new;
	}
	
	private function _removeNullBytes($arg=''){
		return str_replace("\0", '', $arg);
	}
}
?>
