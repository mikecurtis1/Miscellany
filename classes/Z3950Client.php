<?php 

/**
 * http://www.loc.gov/z3950/agency/defns/bib1.html
 */
 
class Z3950Client{

	public function __construct($cfg){
		$this->host = $cfg['host'];
		$this->port = $cfg['port'];
		$this->database = $cfg['database'];
		$this->username = $cfg['username'];
		$this->password = $cfg['password'];
		$this->syntax = $cfg['syntax'];
		$this->search_type = $cfg['search_type'];
		$this->rec_type = $cfg['rec_type'];
		$this->z_connection = $this->_zConnect();
	}
  
	public function zSearch($rpn){
		yaz_syntax($this->z_connection, $this->syntax);
		yaz_search($this->z_connection, $this->search_type, $rpn);
		yaz_wait();
		//TODO: I'm not sure this error checking works the way I want
		$error = yaz_error($this->z_connection);
		if(!empty($error)){
			$error = "z3950 error: $error";
			return $error;
		} 
		$hits = $this->_getHitCount();
		
		return $hits;
	}
	  
	private function _getHitCount(){
		$hits = yaz_hits($this->z_connection);
		
		return $hits;
	}
  
	public function getRecords($start=1,$quantity=1){
		$c = $start;
		$recs = array();
		yaz_range($this->z_connection, $start, $quantity);
		for($i = $start; $i < ($start + $quantity); $i++){
			$r = yaz_record($this->z_connection, $i, $this->rec_type);
			$recs[$c] = $r;
			$c++;
		}
		
		return $recs;
	}
  
	private function _zConnect(){
		if($this->username !== '' && $this->password !== ''){
			$z_connection = yaz_connect($this->host.":".$this->port."/".$this->database, $this->username."/".$this->password);
		} else {
			$z_connection = yaz_connect($this->host.":".$this->port."/".$this->database);
		}
		
		return $z_connection;
	}
}
?>
