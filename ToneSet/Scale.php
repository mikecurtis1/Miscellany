<?php 

class Scale extends Interval implements ToneSet
{
	private $_root = '';
	private $_type = '';
	private $_scale_tone_index = array();
	
	public function __construct($aspn='', $scale_type='', MusicTables $tables){
		if ( ! $tables::isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
		if ( ! $tables::isScaleType($scale_type) ) {
			throw new Exception($scale_type . ' is NOT a scale type.');
		}
		$this->_root = substr($aspn,0,-1);
		$this->_type = $scale_type;
		foreach ( $tables::getScaleIntervals($scale_type) as $data ) {
			try {
				$scale_tone = $data[0];
				$interval = $data[1];
				$this->addTones($this->_getToneByIntervalAsc($aspn, $interval, $tables));
				$this->_scale_tone_index[] = $scale_tone;
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
