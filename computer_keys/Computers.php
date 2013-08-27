<?php 

class Computers
{
	private $_dbc = NULL;
	private $_computers = array();
	
	private function __construct($arg=NULL){
		$this->_dbc = $arg;
		$this->_setComputers();
	}
	
	public static function create($arg=NULL){
		if ( $arg instanceof DbConnection ) {
			return new Computers($arg);
		} else {
			return FALSE;
		}
	}
	
	private function _setComputers(){
		$sql = 'SELECT * FROM computers WHERE active = \'y\'';
		if ( $query = DbQuery::query($this->_dbc->getLink(),$this->_dbc->getDbName(),$sql) ) {
			foreach ( $query->getResults() as $row ) {
				if ( $c = Computer::create($row['name'],$row['id'],$row['ip']) ) {
					$c->setSchedule($this->_dbc);
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
