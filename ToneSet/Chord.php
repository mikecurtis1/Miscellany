<?php 

class Chord extends Interval implements ToneSet
{
	private $_root = '';
	private $_type = '';
	private $_chord_tone_index = array();
	
	public function __construct($aspn='', $chord_type='', MusicTables $tables){
		if ( ! $tables::isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
		if ( ! $tables::isChordType($chord_type) ) {
			throw new Exception($chord_type . ' is NOT a chord type.');
		}
		$this->_root = substr($aspn,0,-1);
		$this->_type = $chord_type;
		foreach ( $tables::getChordIntervals($chord_type) as $chord_tone => $interval ) {
			try {
				$this->addTones($this->_getToneByIntervalAsc($aspn, $interval, $tables));
				$this->_chord_tone_index[] = $chord_tone;
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		}
	}
	
	public function addTones(Tone $tone){
		$this->_tone_set[] = $tone;
	}
	
	public function getToneSet(){
		return $this->_tone_set;
	}
	
	public function permuteSet(){}
	public function retrogradeSet(){}
	public function truncateSet(){}
	public function invertSet(){}
	public function transposeSet(){}
	public function filterByRangeSet(){}
	public function extendRangeSet(){}
}
?>
