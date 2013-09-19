<?php 

class Collection
{
	private $_name = NULL;
	private $_collections = array();
	private $_set = array();
	
	private function __construct($name){
		$this->_name = $name;
	}
	
	public static function create($name=NULL){
		if ( is_string($name) && $name !== '' ) {
			return new Collection($name);
		} else {
			throw new Exception('Error: collection name must be a non-empty string.');
		}
	}
	
	public function addColl($arg=NULL,$key=NULL){
		if ( $arg instanceof Collection  ) {
			$this->_collections[$key] = $arg;
		} else {
			throw new Exception('Error: only a Collection can be added to collections.');
		}
	}
		
	public function addMember($arg=NULL,$key=NULL){
		if ( $arg instanceof Member ) {
			$this->_set[$arg->getKey()] = $arg;
		} else {
			throw new Exception('Error: only a Member can be added to a set.');
		}
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function getColl(){
		return $this->_collections;
	}
	
	public function getSet(){
		return $this->_set;
	}
}
?>
