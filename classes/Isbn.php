<?php 

class Isbn 
{
	private $_string = '';
	private $_matches = array();
	private $_numbers = array();
	private $_isbn10 = array();
	private $_isbn13 = array();
	
	public function __construct($arg='') {
		if ( is_string($arg) ) {
			$this->_string = $arg;
		}
		if ( $this->_string !== '' ) {
			$this->_cleanString();
			$this->_setMatches();
			$this->_filterMatches();
		}
	}
	
	private function _cleanString(){
		$this->_string = trim($this->_string);
		$this->_string = preg_replace("/([0-9xX])\-([0-9xX])/","$1$2", $this->_string);
		$this->_string = preg_replace("/([0-9xX]) ([0-9xX])/","$1$2", $this->_string);
	}
	
	private function _setMatches(){
		preg_match_all("/\b([0-9]{9}[0-9xX]|[0-9]{12}[0-9xX])\b/", $this->_string, $matches);
		if (isset($matches[1])) {
			$this->_matches = $matches[1]; 
		}
	}
	
	private function _filterMatches(){
		foreach ( $this->_matches as $match ) {
			if ( $this->_is_isbn($match) === TRUE ) {
				if ( strlen($match) === 10 ) {
					$this->_isbn10[] = $match;
					$this->_numbers[] = $match;
				} elseif ( strlen($match) === 13 ) {
					$this->_isbn13[] = $match;
					$this->_numbers[] = $match;
				}
			}
		}
	}
	
	public function getNumbers(){
		return $this->_numbers;
	}
	
	public function getFirstISBN10(){
		if ( isset($this->_isbn10[0]) ) {
			return $this->_isbn10[0];
		} else {
			return '';
		}
	}
	
	public function getFirstISBN13(){
		if ( isset($this->_isbn13[0]) ) {
			return $this->_isbn13[0];
		} else {
			return '';
		}
	}
	
	//NOTE: http://stackoverflow.com/questions/14095778/regex-differentiating-between-isbn-10-and-isbn-13
	private function _getIsbn10CheckDigit($isbn=''){
		$check = 0;
		for ($i = 0; $i < 10; $i++) {
			if ('x' === strtolower($isbn[$i])) {
				$check += 10 * (10 - $i);
			} elseif (is_numeric($isbn[$i])) {
				$check += (int)$isbn[$i] * (10 - $i);
			} else {
				return false;
			}
		}
		return (0 === ($check % 11)) ? 1 : false;
	}

	private function _getIsbn13CheckDigit($isbn=''){
		$check = 0;
		for ($i = 0; $i < 13; $i += 2) {
			$check += (int)$isbn[$i];
		}
		for ($i = 1; $i < 12; $i += 2) {
			$check += 3 * $isbn[$i];
		}
		return (0 === ($check % 10)) ? 2 : false;
	}
		
	private function _is_isbn($arg=''){
		if ( is_string($arg) ) {
			$arg = trim($arg);
			$length = strlen($arg);
			if ( $length === 13 ) {
				if ( $this->_getIsbn13CheckDigit($arg) === FALSE ) {
					return FALSE;
				} else {
					return TRUE;
				}
			} elseif ( $length === 10 ) {
				if ( $this->_getIsbn10CheckDigit($arg) === FALSE ) {
					return FALSE;
				} else {
					return TRUE;
				}
			}
			else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	public function __toString(){
		if ( isset($this->_numbers[0]) ) {
			return $this->_numbers[0];
		} else {
			return '';
		}
	}
}
?>
