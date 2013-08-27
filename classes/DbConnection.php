<?php 

class DbConnection
{
	private $_db_name = NULL;
	private $_link_identifier = NULL;
	private $_mysql_link_resource_type = 'mysql link';
	
	private function __construct($db_name,$link_identifier){
		$this->_db_name = $db_name;
		$this->_link_identifier = $link_identifier;
	}
	
	public function __destruct() {
		if ( $this->_has_mysql_link_resource() ) {
			mysql_close($this->_link_identifier);
		}
	}
	
	static public function create($host=NULL,$username=NULL,$password=NULL,$db_name=NULL){
		if ( is_string($host) && is_string($username) && is_string($password) && is_string($db_name) ) {
			if ( $link_identifier = mysql_connect($host,$username,$password,$db_name) ) {
				return new DbConnection($db_name,$link_identifier);
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	public function getDbName(){
		return $this->_db_name;
	}
	
	public function getLink(){
		return $this->_link_identifier;
	}
	
	public function closeLink(){
		mysql_close($this->_link_identifier);
	}
	
	private function _has_mysql_link_resource(){
		if ( is_resource($this->_link_identifier) && get_resource_type($this->_link_identifier) === $this->_mysql_link_resource_type ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
