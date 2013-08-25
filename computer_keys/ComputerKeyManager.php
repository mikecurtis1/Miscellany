<?php 

require_once(dirname(__FILE__).'/../classes/DbQuery.php');
require_once(dirname(__FILE__).'/../classes/DbConnection.php');
require_once(dirname(__FILE__).'/Computer.php');
require_once(dirname(__FILE__).'/TimeBlock.php');

class ComputerKeyManager
{
	private $_day_begin = NULL;
	private $_day_end = NULL;
	private $_db_name = 'computer_keys';
	private $_db_connection = FALSE;
	private $_computers = array();
	
	private function __construct($c=NULL){
		$this->_db_connection = $c->getConnection();
		$this->_setComputers();
		$this->_setTimeBlocks();
		$this->_setAvailableTimeBlocks();
	}
	
	public static function create($db_host=NULL,$db_username=NULL,$db_password=NULL){
		if ( $c = DbConnection::create($db_host,$db_username,$db_password) ) {
			return new ComputerKeyManager($c);
		} else {
			return FALSE;
		}
	}
	
	private function _setComputers(){
		$sql = 'SELECT * FROM computers';
		if ( $query = DbQuery::query($this->_db_connection,$this->_db_name,$sql) ) {
			foreach ( $query->getResults() as $computer ) {
				if ( $c = Computer::create($computer['name'],$computer['id'],$computer['ip']) ) {
					$this->_computers[$computer['name']] = $c;
				}
			}
		}
	}
	
	private function _setTimeBlocks(){
		//TODO: limit this to the current day
		$sql = 'SELECT * FROM computers LEFT JOIN key_schedule ON key_schedule.computer = computers.id';
		if ( $query = DbQuery::query($this->_db_connection,$this->_db_name,$sql) ) {
			foreach ( $query->getResults() as $scheduled_key ) {
				if ( isset($this->_computers[$scheduled_key['name']]) ) {
					if ( $time_block = TimeBlock::create($scheduled_key['begin'],$scheduled_key['end'],'SCHEDULED') ) {
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
	
	private function _setAvailableTimeBlocks(){
		foreach ( $this->_computers as $computer ) {
			$computer->setAvailableTimeBlocks();
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
			if ( ( $arg1->getBegin() >= $arg2->getBegin() && $arg1->getBegin() <= $arg2->getEnd() ) || ( $arg1->getEnd() >= $arg2->getBegin() && $arg1->getEnd() <= $arg2->getEnd() ) ) {
				return FALSE;
			} elseif ( ( $arg2->getBegin() >= $arg1->getBegin() && $arg2->getBegin() <= $arg1->getEnd() ) || ( $arg2->getEnd() >= $arg1->getBegin() && $arg2->getEnd() <= $arg1->getEnd() ) ) {
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
		//TODO: add db query to check for unique
		$chars = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		return substr(str_shuffle($chars),0,4);
	}
	
	public function getComputers(){
		return $this->_computers;
	}
}
?>
