<?php 
class DbQuery
{
  private $_count = 0;
	private $_results = array();
	
	private function __construct($results){
		$this-> _count = mysql_num_rows($results);
		while($row = mysql_fetch_assoc($results)){
			$this->_results[] = $row;
		}
	}
	
	static public function query($conn=NULL,$db=NULL,$sql=NULL){
		mysql_select_db($db,$conn);
		if ( $results = mysql_query($sql,$conn) ) {
			return new DbQuery($results);
		} else {
			return FALSE;
		}
	}
	
	public function getCount(){
		return $this->_count;
	}
	
	public function getResults(){
		return $this->_results;
	}
}
?>
