<?php 

class EscapeString 
{
	private $_escape_op = '';
	private $_chars = array();
	private $_escaped = array();

	public function __construct($arg=''){ 
		$this->_escape_op = chr(92); // chr 92 is backslash
		if ( is_string($arg) ) {
			$this->_chars = str_split($arg);
		} else {
			throw new Exception('Argument must be a string.');
		}
		$this->_parse();
	}

	private function _parse(){
		foreach ( $this->_chars as $i => $char ) {
			if ( $this->_isEscaped($i) ) {
				$this->_escaped[$i] = array('chr'=>$char,'is_escaped'=>TRUE);
			} else {
				if ( $char !== $this->_escape_op ) {
					$this->_escaped[$i] = array('chr'=>$char,'is_escaped'=>FALSE);
				}
			}
		}
	}

	private function _isEscaped($i=0){
		$escaped = FALSE;
		$c = $this->_countEscapeChars($i);
		if ( $c % 2 !== 0 ) {
			$escaped = TRUE;
		}

		return $escaped;
	}

	private function _countEscapeChars($i=0){
		$pi = $i-1;
		$prev = NULL;
		$c = 0;
		while ( $pi >= 0 ) {
			if ( isset($this->_chars[$pi]) ) {
				$prev = $this->_chars[$pi];
			}
			if ( $prev !== $this->_escape_op ) {
				break;
			}
			$pi--;
			$c++;
		}

		return $c;
	}
	
	public function getEscaped(){
		return $this->_escaped;
	}
	
	public function getEscapeChr(){
		return $this->_escape_op;
	}
}
?>
