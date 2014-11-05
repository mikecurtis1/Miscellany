<?php 

class Tone
{
	private $_letter;
	private $_accidental;
	private $_octave;
	
	public function __construct($aspn='C4'){
		if ( ! $this->_isASPN($aspn) ) {
			throw new Exception($aspn . ' is NOT an ASPN value.');
		}
		$this->_letter = strtoupper(substr($aspn,0,1));
		$this->_accidental = substr($aspn,1,-1);
		$this->_octave = substr($aspn,-1);
	}

	private function _isASPN($aspn=NULL){
		$temp = json_decode(file_get_contents('American_standard_pitch_notation.json'), TRUE);
		if ( isset($temp[$aspn]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
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
	
	public function getASPN($accidental_mode='txt',$show_natural=FALSE){
		return $this->_letter . $this->_getAccidental($accidental_mode,$show_natural) . $this->_octave;
	}
	
	public function getHelmholtzPitchNotation($accidental_mode='txt',$show_natural=FALSE){
		$helmholtz = '';
		if ( $this->_octave >= 3 ) {
			$helmholtz .= strtolower($this->_letter);
		} else {
			$helmholtz .= $this->_letter;
		}
		$helmholtz .= $this->_getAccidental($accidental_mode,$show_natural);
		if ( $this->_octave === 0 ) {$helmholtz .= ',,';}
		if ( $this->_octave === 1 ) {$helmholtz .= ',';}
		if ( $this->_octave === 4 ) {$helmholtz .= "'";}
		if ( $this->_octave === 5 ) {$helmholtz .= "''";}
		if ( $this->_octave === 6 ) {$helmholtz .= "'''";}
		if ( $this->_octave === 7 ) {$helmholtz .= "''''";}
		if ( $this->_octave === 8 ) {$helmholtz .= "'''''";}
		
		return $helmholtz;
	}
	
	private function _getAccidental($mode='txt',$show_natural=FALSE){
		if ( $mode === 'txt' ) {
			if ( $this->_accidental === '' ) {return '';}
			if ( $this->_accidental === '#' ) {return '#';}
			if ( $this->_accidental === 'b' ) {return 'b';}
			if ( $this->_accidental === '##' ) {return '##';}
			if ( $this->_accidental === 'bb' ) {return 'bb';}
		}
		if ( $mode === 'html' ) {
			if ( $show_natural === TRUE ) {
				if ( $this->_accidental === '' ) {return '&#9838;';}
			} else {
				if ( $this->_accidental === '' ) {return '';}
			}
			if ( $this->_accidental === '#' ) {return '&#9839;';}
			if ( $this->_accidental === 'b' ) {return '&#9837;';}
			if ( $this->_accidental === '##' ) {return '&#9839;&#9839;';} // &#119082;
			if ( $this->_accidental === 'bb' ) {return '&#9837;&#9837;';} // &#119083;
		}
		if ( $mode === 'abc' ) {
			if ( $this->_accidental === '' ) {return '';}
			if ( $this->_accidental === '#' ) {return '^';}
			if ( $this->_accidental === 'b' ) {return '^^';}
			if ( $this->_accidental === '##' ) {return '_';}
			if ( $this->_accidental === 'bb' ) {return '__';}
		}
		if ( $mode === 'unicode' ) {
			if ( $show_natural === TRUE ) {
				if ( $this->_accidental === '' ) {return mb_convert_encoding('&#9838;', 'UTF-8', 'HTML-ENTITIES');}
			} else {
				if ( $this->_accidental === '' ) {return '';}
			}
			if ( $this->_accidental === '#' ) {return mb_convert_encoding('&#9839;', 'UTF-8', 'HTML-ENTITIES');}
			if ( $this->_accidental === 'b' ) {return mb_convert_encoding('&#9837;', 'UTF-8', 'HTML-ENTITIES');}
			if ( $this->_accidental === '##' ) {return mb_convert_encoding('&#119082;', 'UTF-8', 'HTML-ENTITIES');}
			if ( $this->_accidental === 'bb' ) {return mb_convert_encoding('&#119083;', 'UTF-8', 'HTML-ENTITIES');}
		}
	}
}
?>
