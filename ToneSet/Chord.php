<?php 

class Chord extends Interval implements ToneSet
{
	private $_root = 'C';
	private $_type = 'major';
	
	public function __construct(MusicTables $tables){
		$this->addTones('C4', 'P1', $tables);
		$this->addTones('E4', 'P1', $tables);
		$this->addTones('G4', 'P1', $tables);
		$this->addTones('C5', 'P1', $tables);
	}
	public function addTones($aspn='C4', $interval='P1', MusicTables $tables){
		if ( ! $tables::isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
		if ( ! $tables::isInterval($interval) ) {
			throw new Exception($interval . ' is NOT an interval abbreviation.');
		}
		$this->_tone_set[] = $this->_getToneByIntervalAsc($aspn, $interval, $tables);
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
