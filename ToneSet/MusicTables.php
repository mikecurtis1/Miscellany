<?php 

class MusicTables
{
	private static $_aspn = array();
	private static $_intervals = array();
	private static $_letter_sequence_asc = array();
	
	public static function config(){
		self::$_aspn = json_decode(file_get_contents('American_standard_pitch_notation.json'),TRUE);
		self::$_intervals = json_decode(file_get_contents('intervals.json'),TRUE);
		self::$_letter_sequence_asc = json_decode(file_get_contents('letter_sequence_asc.json'),TRUE);
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
			if ( $data['key'] === $piano_key && $data['letter'] === $letter ) {
				return $aspn;
			}
		}
	}
	
	public static function isASPN($key){
		if ( isset(self::$_aspn[$key]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public static function isInterval($key){
		if ( isset(self::$_intervals[$key]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>