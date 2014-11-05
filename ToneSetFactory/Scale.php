<?php 

class Scale
{
	private $_tone_set = array();
	private $_tonic = '';
	private $_type = '';
	
	public function __construct($array=array(),$tonic='',$type=''){
		foreach ( $array as $i => $v ) {
			if ( $v instanceof Tone ) {
				$this->_tone_set[$i] = $v;
			} else {
				throw new Exception('Only Tone objects can be added to a Scale.');
			}
		}
		$this->_tonic = $tonic;
		$this->_type = $type;
	}
	
	public function getToneSet(){
		return $this->_tone_set;
	}
	
	public function getTonic(){
		return $this->_tonic;
	}
	
	public function getType(){
		return $this->_type;
	}
}
?>
