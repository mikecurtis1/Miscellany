<?php 

abstract class Interval
{
	protected $_tone_set = array();
	
	protected function _getToneByIntervalAsc($str,$interval){
		$ordinal = MusicTables::getASPNValue($str,'key') + MusicTables::getIntervalValue($interval,'chromatic_steps');
		$letter = MusicTables::getNextLetter($str,$interval);
		$aspn = MusicTables::getPianoKeySpelling($ordinal,$letter);
		try {
			$tone = Tone::create($aspn);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
		return $tone;
	}
	
	public function addTones($aspn='C4',$interval='P1'){
		if ( ! MusicTables::isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
		if ( ! MusicTables::isInterval($interval) ) {
			throw new Exception($interval . ' is NOT an interval abbreviation.');
		}
		$this->_tone_set[0] = $this->_getToneByIntervalAsc($aspn,$interval);
	}
	
	public function getToneSet(){
		return $this->_tone_set;
	}
	
	public function permute(){}
	public function retrograde(){}
	public function truncate(){}
	public function invert(){}
	public function transpose(){}
	public function filterByRange(){}
	public function extendRange(){}
}
?>
