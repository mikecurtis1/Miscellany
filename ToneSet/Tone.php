<?php 

class Tone
{
	private $_aspn = '';
	private $_letter = '';
	private $_accidental = '';
	private $_octave = '';
	private $_ordinal = '';
	private $_Hz = '';
	private $_natural = '';
	private $_sharp = '';
	private $_flat = '';
	
	private function __construct($aspn){
		$this->_aspn = $aspn;
		$this->_letter = MusicTables::getASPNValue($aspn,'letter');
		$this->_accidental = MusicTables::getASPNValue($aspn,'accidental');
		$this->_octave = MusicTables::getASPNValue($aspn,'octave');
		$this->_ordinal = MusicTables::getASPNValue($aspn,'key');
		$this->_Hz = MusicTables::getASPNValue($aspn,'Hz');
		$this->_natural = MusicTables::getASPNValue($aspn,'natural');
		$this->_sharp = MusicTables::getASPNValue($aspn,'sharp');
		$this->_flat = MusicTables::getASPNValue($aspn,'flat');
	}
	
	public static function create($aspn=''){
		if ( ! MusicTables::isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		} else {
			return new Tone($aspn);
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
	public function getOrdinal(){
		return $this->_ordinal;
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
