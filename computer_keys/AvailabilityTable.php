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
	private $_db_name = 'computer_keys';
	private $_db_connection = FALSE;
	private $_computers = array();
	
	private function __construct($c=NULL){
		$this->_db_connection = $c->getConnection();
		$this->_today = date("Y-m-d");
		$this->_setComputers();
		$this->_setTimeBlocks();
	}
	
	public static function create($db_host=NULL,$db_username=NULL,$db_password=NULL){
		if ( $c = DbConnection::create($db_host,$db_username,$db_password) ) {
			return new AvailabilityTable($c);
		} else {
			return FALSE;
		}
	}
	
	private function _setComputers(){
		$sql = 'SELECT * FROM computers';
		if ( $query = DbQuery::query($this->_db_connection,$this->_db_name,$sql) ) {
			foreach ( $query->getResults() as $computer ) {
				if ( $this->_computers[$computer['name']] = Computer::create($computer['name'],$computer['id'],$computer['ip']) ) {
					continue;
				}
			}
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

	public function addNewTimeBlock($computer_name=NULL,$time_block=NULL){
		if ( isset($this->_computers[$computer_name]) && $time_block instanceof TimeBlock ) {
			foreach ( $this->_computers[$computer_name]->getTimeBlocks() as $cmp_time_block ) {
				if ( $this->_noTimeConflict($time_block,$cmp_time_block) === FALSE ) {
					return FALSE;
				}
			}
			return $this->_insertKeySchedule($this->_computers[$computer_name]->getId(),$time_block);
		} else {
			return FALSE;
		}
	}
	
	private function _noTimeConflict($arg1=NULL,$arg2=NULL){
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
	
	private function _insertKeySchedule($computer_id=NULL,$time_block=NULL){
		$sql = 'INSERT INTO key_schedule (`computer`,`begin`,`end`,`key`,`note`) VALUES (\''.$computer_id.'\',\''.$time_block->getBegin().'\',\''.$time_block->getEnd().'\',\''.$this->_generateKey().'\',\''.'pending'.'\')';
		if ( $query = DbQuery::query($this->_db_connection,$this->_db_name,$sql) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function _generateKey(){
		$chars = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		return substr(str_shuffle($chars),0,4);
	}
	
	public function getComputers(){
		return $this->_computers;
	}
	
	//TODO: function, given a set of time blocks in a computer object return a set of time blocks for the remainder of the day
	private function getAvailableTimeBlocks($computer_name=NULL){
		return array();
	}
}
?>
