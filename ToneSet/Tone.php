<?php 

class Tone
{
	private $_aspn = '';
	private $_letter = '';
	private $_accidental = '';
	private $_octave = '';
	private $_piano_key = '';
	private $_Hz = '';
	private $_natural = '';
	private $_sharp = '';
	private $_flat = '';
	
	private function __construct($aspn, $tables){
		$this->_aspn = $aspn;
		$this->_letter = $tables::getASPNValue($aspn, 'letter');
		$this->_accidental = $tables::getASPNValue($aspn, 'accidental');
		$this->_octave = $tables::getASPNValue($aspn, 'octave');
		$this->_piano_key = $tables::getASPNValue($aspn, 'piano_key');
		$this->_Hz = $tables::getASPNValue($aspn, 'Hz');
		$this->_natural = $tables::getASPNValue($aspn, 'natural');
		$this->_sharp = $tables::getASPNValue($aspn, 'sharp');
		$this->_flat = $tables::getASPNValue($aspn, 'flat');
	}
	
	public static function create($aspn='', MusicTables $tables){
		if ( $tables::isASPN($aspn) ) {
			return new Tone($aspn, $tables);
		} else {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
	}
	
	public function getASPN(){
		return $this->_aspn;
	}
	public function getLetter(){
		return $this->_letter;
	}
	public function getAccidental(){
		return $this->_accidental;
	}
	public function getOctave(){
		return $this->_octave;
	}
	public function getPianoKey(){
		return $this->_piano_key;
	}
	public function getHz(){
		return $this->_Hz;
	}
	public function getNatural(){
		return $this->_natural;
	}
	public function getSharp(){
		return $this->_sharp;
	}
	public function getFlat(){
		return $this->_flat;
	}
}
?>
