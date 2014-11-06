<?php 

class Chord extends Interval implements ToneSet
{
	private $_root = 'C';
	private $_type = 'major';
	
	public function build(){
		$this->addTones('C4','P1');
		$this->addTones('E4','P1');
		$this->addTones('G4','P1');
		$this->addTones('C5','P1');
	}
	public function addTones($aspn='C4',$interval='P1'){
		if ( ! MusicTables::isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
		if ( ! MusicTables::isInterval($interval) ) {
			throw new Exception($interval . ' is NOT an interval abbreviation.');
		}
		$this->_tone_set[] = $this->_getToneByIntervalAsc($aspn,$interval);
	}
	
	public function getToneSet(){
		return $this->_tone_set;
	}
	
	public function permuteSet(){
		return array();
	}
	public function retrogradeSet(){
		return array();
	}
	public function truncateSet(){
		return array();
	}
	public function invertSet(){ // ASPN array required
		return array();
	}
	public function transposeSet(){ // ASPN array required
		return array();
	}
	public function filterByRangeSet(){ // ASPN array required
		return array();
	}
	public function extendRangeSet(){ // ASPN array required
		return array();
	}
}
?>
