<?php 

class Chord
{
	private $_tone_set = array();
	private $_root = '';
	private $_type = '';
	
	public function __construct($array=array(),$root='',$type=''){
		foreach ( $array as $i => $v ) {
			if ( $v instanceof Tone ) {
				$this->_tone_set[$i] = $v;
			} else {
				throw new Exception('Only Tone objects can be added to a Chord.');
			}
		}
		$this->_root = $root;
		$this->_type = $type;
	}
	
	public function getToneSet(){
		return $this->_tone_set;
	}
	
	public function getRoot(){
		return $this->_root;
	}
	
	public function getType(){
		return $this->_type;
	}
}
?>
