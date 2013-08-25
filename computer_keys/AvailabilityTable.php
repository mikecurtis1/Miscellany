<?php 

require_once(dirname(__FILE__).'/../classes/DbQuery.php');
require_once(dirname(__FILE__).'/../classes/DbConnection.php');
require_once(dirname(__FILE__).'/Computer.php');
require_once(dirname(__FILE__).'/TimeBlock.php');

class AvailabilityTable
{
	private $_day_begin_time = '00:00:00';
	private $_day_end_time = '23:59:59';
	private $_today = NULL;
	private $_db_host = 'localhost';
	private $_db_username = 'root'; //TODO: move out of public web root
	private $_db_password = ''; //TODO: move out of public web root
	private $_db_name = 'computer_keys';
	private $_db_connection = FALSE;
	private $_db_count = NULL;
	private $_db_results = NULL;
	private $_db_fields = NULL;
	private $_db_html_table = '';
	private $_computers = array();
	
	public function __construct(){
		$this->_today = date("Y-m-d");
		if ( $c = DbConnection::create($this->_db_host,$this->_db_username,$this->_db_password) ) {
			$this->_db_connection = $c->getConnection();
		} else {
			return FALSE;
		}
		$this->_setComputers();
		$this->_setTimeBlocks();
	}
	
	private function _setComputers(){
		$sql = 'SELECT * FROM computers';
		if ( $query = DbQuery::query($this->_db_connection,$this->_db_name,$sql) ) {
			foreach ( $query->getResults() as $computer ) {
				if ( $this->_computers[$computer['name']] = Computer::create($computer['name'],$computer['id'],$computer['ip']) ) {
					//continue;
				}
			}
			$this->_db_html_table = $query->htmlResultsTable();
		}
	}
	
	private function _setTimeBlocks(){
		$sql = 'SELECT * FROM computers LEFT JOIN key_schedule ON key_schedule.computer = computers.id';
		if ( $query = DbQuery::query($this->_db_connection,$this->_db_name,$sql) ) {
			foreach ( $query->getResults() as $scheduled_key ) {
				if ( isset($this->_computers[$scheduled_key['name']]) ) {
					if ( $time_block = TimeBlock::create($scheduled_key['begin'],$scheduled_key['end']) ) {
						$this->_computers[$scheduled_key['name']]->addTimeBlock($time_block);
					} else {
						return FALSE;
					}
				} else {
					return FALSE;
				}
			}
		}
	}

	public function setNewTimeBlock($computer_name=NULL,$time_block=NULL){
		if ( isset($this->_computers[$computer_name]) && $time_block instanceof TimeBlock ) {
			foreach ( $this->_computers[$computer_name]->getTimeBlocks() as $cmp_time_block ) {
				if ( $this->noTimeConflict($time_block,$cmp_time_block) === FALSE ) {
					return FALSE;
				}
			}
			return $this->_insertKeySchedule($this->_computers[$computer_name]->getId(),$time_block);
		} else {
			return FALSE;
		}
	}
	
	private function _generateKey(){
		$chars = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		return substr(str_shuffle($chars),0,4);
	}
	
	private function _insertKeySchedule($computer_name=NULL,$time_block=NULL){
		$sql = 'INSERT INTO key_schedule (`computer`,`begin`,`end`,`key`,`note`) VALUES (\''.$computer_name.'\',\''.$time_block->getBegin().'\',\''.$time_block->getEnd().'\',\''.$this->_generateKey().'\',\''.'pending'.'\')';
		if ( $query = DbQuery::query($this->_db_connection,$this->_db_name,$sql) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function getHTMLTable(){
		return $this->_db_html_table;
	}
	
	public function getComputers(){
		return $this->_computers;
	}
	
	public function getComputerNames(){
		return array_keys($this->_computers);
	}
	
	public function noTimeConflict($arg1=NULL,$arg2=NULL){
		if ( $arg1 instanceof TimeBlock && $arg2 instanceof TimeBlock ) {
			if ( ( $arg1->getBeginUnix() >= $arg2->getBeginUnix() && $arg1->getBeginUnix() <= $arg2->getEndUnix() ) || ( $arg1->getEndUnix() >= $arg2->getBeginUnix() && $arg1->getEndUnix() <= $arg2->getEndUnix() ) ) {
				return FALSE;
			} elseif ( ( $arg2->getBeginUnix() >= $arg1->getBeginUnix() && $arg2->getBeginUnix() <= $arg1->getEndUnix() ) || ( $arg2->getEndUnix() >= $arg1->getBeginUnix() && $arg2->getEndUnix() <= $arg1->getEndUnix() ) ) {
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return FALSE;
		}
	}
	
	//TODO: function, given a set of time blocks in a computer object return a set of time blocks for the remainder of the day
	private function getAvailableTimeBlocks($computer_name=NULL){
		return array();
	}
}
?>
