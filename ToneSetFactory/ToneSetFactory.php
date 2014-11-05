<?php 

class ToneSetFactory 
{
	private $_tone_set = array();
	private $_American_standard_pitch_notation = array();
	private $_chromatic_series = array();
	private $_diatonic_series = array();
	private $_intervals = array();
	private $_letter_sequence_asc = array();
	private $_letter_sequence_desc = array();
	private $_chord_intervals = array();
	
	public function __construct(){
		$this->_tone_set = array();
		$this->_American_standard_pitch_notation = json_decode(file_get_contents('American_standard_pitch_notation.json'), TRUE);
		$this->_chromatic_series = json_decode(file_get_contents('chromatic_series.json'), TRUE);
		$this->_diatonic_series = json_decode(file_get_contents('diatonic_series.json'), TRUE);
		$this->_intervals = json_decode(file_get_contents('intervals.json'), TRUE);
		$this->_letter_sequence_asc = json_decode(file_get_contents('letter_sequence_asc.json'), TRUE);
		$this->_letter_sequence_desc = json_decode(file_get_contents('letter_sequence_desc.json'), TRUE);
		$this->_chord_intervals = json_decode(file_get_contents('chord_intervals.json'), TRUE);
	}
	
	public function getNoteByIntervalAsc($aspn='C4',$interval='P1'){
		if ( ! $this->_isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
		if ( ! $this->_isInterval($interval) ) {
			throw new Exception($interval . ' is NOT an interval abbreviation.');
		}
		try {
			$this->_tone_set[] = $this->_getToneByIntervalAsc($aspn,$interval);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		try {
			return new Note($this->_tone_set);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	public function getScaleAsc($aspn='C4',$scale_type='major'){
		if ( ! $this->_isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
		if ( ! $this->_isScaleType($scale_type) ) {
			throw new Exception($scale_type . ' is NOT a diatonic scale type.');
		}
		$this->_tone_set = array();
		foreach ( $this->_diatonic_series as $o => $data ) {
			try {
				$this->_tone_set[$o] = $this->_getToneByIntervalAsc($aspn,$data['interval_' . $scale_type]);
			} catch  (Exception $e) {
				throw new Exception($e->getMessage());
			}
		}
		$tonic = substr($aspn,0,-1);
		try {
			return new Scale($this->_tone_set,$tonic,$scale_type);
		} catch  (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	public function getChord($aspn='C4',$chord_type='major'){
		if ( ! $this->_isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
		if ( ! $this->_isChordType($chord_type) ) {
			throw new Exception($chord_type . ' is NOT a chord type.');
		}
		$this->_tone_set = array();
		foreach ( $this->_chord_intervals[$chord_type] as $chord_tone => $interval ) {
			try {
				$this->_tone_set[$chord_tone] = $this->_getToneByIntervalAsc($aspn,$interval);
			} catch  (Exception $e) {
				throw new Exception($e->getMessage());
			}
		}
		$root = substr($aspn,0,-1);
		try {
			return new Chord($this->_tone_set,$root,$chord_type);
		} catch  (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	private function _isASPN($aspn=NULL){
		if ( isset($this->_American_standard_pitch_notation[$aspn]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function _isInterval($interval=NULL){
		if ( isset($this->_intervals[$interval]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function _isScaleType($scale_type=NULL){
		if ( isset($this->_diatonic_series[1]['interval_' . $scale_type]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function _isChordType($chord_type=NULL){
		if ( isset($this->_chord_intervals[$chord_type]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function _getToneByIntervalAsc($aspn='C4',$interval='P1'){
		if ( ! $this->_isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
		if ( ! $this->_isInterval($interval) ) {
			throw new Exception($interval . ' is NOT an interval abbreviation.');
		}
		$ordinal = $this->_American_standard_pitch_notation[$aspn]['key'] + $this->_intervals[$interval]['chromatic_steps'];
		$letter = $this->_getNextLetter($aspn,$interval);
		$spelling = $this->_getPianoKeySpelling($ordinal,$letter);
		try {
			$tone = new Tone($spelling);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
		return $tone;
	}
	
	private function _getPianoKeySpelling($piano_key=0,$letter='C'){
		foreach ( $this->_American_standard_pitch_notation as $aspn => $data ) {
			if ( $data['key'] === $piano_key && $data['letter'] === $letter ) {
				return $aspn;
			}
		}
	}
	
	private function _getNextLetter($aspn='C4',$interval='P1'){
		$ordinal = $this->_letter_sequence_asc[substr($aspn,0,1)];
		$steps = $this->_intervals[$interval]['diatonic_steps'];
		$sum = $ordinal + $steps;
		if ( $sum > 7 ) {
			$end = ( $sum ) % 7;
		} else {
			$end = $sum;
		}
		
		return $this->_letter_sequence_asc[$end];
	}
}
?>
