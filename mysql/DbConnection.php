<?php 
class DbConnection
{
	private $_connection;
	
	private function __construct($connection){
		$this->_connection = $connection;
	}
	
	static public function create($host=NULL,$username=NULL,$password=NULL){
		if ( is_string($host) && is_string($username) && is_string($password) ) {
			if ( $connection = mysql_connect($host,$username,$password) ) {
				return new DbConnection($connection);
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	public function getConnection(){
		return $this->_connection;
	}
	
	public function closeConnection(){
		mysql_close($this->_connection);
	}
}
?>
