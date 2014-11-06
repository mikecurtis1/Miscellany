<?php 

abstract class Interval
{
	protected $_tone_set = array();
	
	protected function _getToneByIntervalAsc($aspn, $interval, $tables){
		$temp_piano_key = $tables::getASPNValue($aspn, 'piano_key') + $tables::getIntervalValue($interval, 'chromatic_steps');
		$temp_letter = $tables::getNextLetter($aspn, $interval);
		$temp_aspn = $tables::getPianoKeySpelling($temp_piano_key, $temp_letter);
		try {
			$tone = Tone::create($temp_aspn, $tables);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
		return $tone;
	}
	
	public function addTones($aspn, $interval, MusicTables $tables){}
	public function getToneSet(){}
	public function permute(){}
	public function retrograde(){}
	public function truncate(){}
	public function invert(){}
	public function transpose(){}
	public function filterByRange(){}
	public function extendRange(){}
}
?>
