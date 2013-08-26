<?php 

require_once(dirname(__FILE__).'/../classes/DbQuery.php');
require_once(dirname(__FILE__).'/../classes/DbConnection.php');
require_once(dirname(__FILE__).'/Computer.php');

class Computers
{
	private $_db_name = 'computer_keys';
	private $_db_connection = FALSE;
	private $_computers = array();
	
	private function __construct($c=NULL){
		$this->_db_connection = $c->getConnection();
		$this->_setComputers();
	}
	
	public static function create($db_host=NULL,$db_username=NULL,$db_password=NULL){
		if ( $c = DbConnection::create($db_host,$db_username,$db_password) ) {
			return new Computers($c);
		} else {
			return FALSE;
		}
	}
	
	private function _setComputers(){
		$sql = 'SELECT * FROM computers WHERE active = \'y\'';
		if ( $query = DbQuery::query($this->_db_connection,$this->_db_name,$sql) ) {
			foreach ( $query->getResults() as $row ) {
				if ( $c = Computer::create($row['name'],$row['id'],$row['ip']) ) {
					$this->_computers[$row['name']] = $c;
				}
			}
		}
	}
	
	public function getComputers(){
		return $this->_computers;
	}
}
?>
