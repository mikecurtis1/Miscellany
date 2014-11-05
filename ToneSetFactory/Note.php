<?php 

class Note
{
	private $_tone_set = array();
	
	public function __construct($array=array()){
		foreach ( $array as $i => $v ) {
			if ( $v instanceof Tone ) {
				$this->_tone_set[$i] = $v;
			} else {
				throw new Exception('Only Tone objects can be added to a Note.');
			}
		}
	}
	
	public function getToneSet(){
		return $this->_tone_set;
	}
}
?>
