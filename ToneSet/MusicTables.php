<?php 

class MusicTables
{
	private static $_aspn = array();
	private static $_intervals = array();
	private static $_letter_sequence_asc = array();
	private static $_chord_intervals = array();
	private static $_diatonic_series = array();
	
	protected function __construct(){}
	private function __clone(){}
	private function __wakeup(){}
	
	public static function config($json_path='',$type=''){
		self::$_aspn = json_decode(file_get_contents('American_standard_pitch_notation.json'),TRUE);
		self::$_intervals = json_decode(file_get_contents('intervals.json'),TRUE);
		self::$_letter_sequence_asc = json_decode(file_get_contents('letter_sequence_asc.json'),TRUE);
		self::$_chord_intervals = json_decode(file_get_contents('chord_intervals.json'),TRUE);
		self::$_diatonic_series = json_decode(file_get_contents('diatonic_series.json'),TRUE);
		static $instance = null;
		if ( NULL === $instance ) {
			$instance = new static();
		}
		return $instance;
	}
	
	public static function getASPNValue($key1='',$key2=''){
		if ( isset(self::$_aspn[$key1][$key2]) ) {
			return self::$_aspn[$key1][$key2];
		} else {
			return FALSE;
		}
	}
	
	public static function getIntervalValue($key1='',$key2=''){
		if ( isset(self::$_intervals[$key1][$key2]) ) {
			return self::$_intervals[$key1][$key2];
		} else {
			return FALSE;
		}
	}
	
	//TODO: add validation and failure return value
	public static function getNextLetter($aspn,$interval){
		$ordinal = self::$_letter_sequence_asc[substr($aspn,0,1)];
		$steps = self::$_intervals[$interval]['diatonic_steps'];
		$sum = $ordinal + $steps;
		if ( $sum > 7 ) {
			$end = ( $sum ) % 7;
		} else {
			$end = $sum;
		}

		return self::$_letter_sequence_asc[$end];
	}
	
	public static function getPianoKeySpelling($piano_key,$letter){
		foreach ( self::$_aspn as $aspn => $data ) {
			if ( $data['piano_key'] === $piano_key && $data['letter'] === $letter ) {
				return $aspn;
			}
		}
		
		return FALSE;
	}
	
	public static function getChordIntervals($chord_type){
		if ( isset(self::$_chord_intervals[$chord_type]) ) {
			return self::$_chord_intervals[$chord_type];
		} else {
			return FALSE;
		}
	}
	
	//TODO: add validation and failure return value
	public static function getScaleIntervals($scale_type){
		$temp = array();
		foreach ( self::$_diatonic_series as $data ) {
			$temp[] = array($data['solfege_' . $scale_type], $data['interval_' . $scale_type]);
		}
		
		return $temp;
	}
	
	public static function isASPN($aspn){
		if ( isset(self::$_aspn[$aspn]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public static function isInterval($interval){
		if ( isset(self::$_intervals[$interval]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public static function isChordType($chord_type){
		if ( isset(self::$_chord_intervals[$chord_type]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public static function isScaleType($scale_type){
		if ( isset(self::$_diatonic_series[1]['interval_' . $scale_type]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
