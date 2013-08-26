<?php 

require_once(dirname(__FILE__).'/../classes/DbQuery.php');
require_once(dirname(__FILE__).'/../classes/DbConnection.php');
require_once(dirname(__FILE__).'/TimeBlock.php');
require_once(dirname(__FILE__).'/Event.php');

class Schedule
{
	private static $_db_host = 'localhost'; //TODO: move out of public web root
	private static $_db_username = 'root'; //TODO: move out of public web root
	private static $_db_password = ''; //TODO: move out of public web root
	private $_db_name = 'computer_keys';
	private $_db_connection = FALSE;
	private $_computer_id = NULL;
	private $_day_begin = NULL;
	private $_day_end = NULL;
	private $_time_blocks = array();
	private $_available_time_blocks = array();
	
	private function __construct($c=NULL,$computer_id=NULL){
		$this->_db_connection = $c->getConnection();
		$this->_computer_id = $computer_id;
		$this->_day_begin = (string) strtotime(date("Y-m-d").' 00:00:00');
		$this->_day_end = (string) strtotime(date("Y-m-d").' 23:59:59');
		$this->_setTimeBlocks();
		$this->_setAvailableTimeBlocks();
	}
	
	public static function create($computer_id=NULL){
		if ( $c = DbConnection::create(self::$_db_host,self::$_db_username,self::$_db_password) ) {
			return new Schedule($c,$computer_id);
		} else {
			return FALSE;
		}
	}
	
	private function _setTimeBlocks(){
		$sql = 'SELECT * FROM computers LEFT JOIN key_schedule ON key_schedule.computer = computers.id WHERE key_schedule.computer = '.$this->_computer_id.' AND begin <= '.$this->_day_end.' AND end >= '.$this->_day_begin;
		if ( $query = DbQuery::query($this->_db_connection,$this->_db_name,$sql) ) {
			foreach ( $query->getResults() as $scheduled_key ) {
				if ( $time_block = TimeBlock::create($scheduled_key['begin'],$scheduled_key['end'],'SCHEDULED',$scheduled_key['id'],$scheduled_key['key']) ) {
					$this->_time_blocks[] = $time_block;
				}
			}
		}
	}
	
	public static function cmpTimeBlocks($a,$b){
		if ( $a instanceof TimeBlock && $b instanceof TimeBlock ) {
			return ($a->getBegin() < $b->getBegin()) ? -1 : 1;
		} else {
			return 1;
		}
	}
	
	//TODO: make this function smaller, break it up?
	private function _setAvailableTimeBlocks(){
		// init arrays
		$scheduled_events = array();
		$inverted_events = array();
		$previous_event = NULL;
		// sort first
		usort($this->_time_blocks,array('Schedule','cmpTimeBlocks'));
		// list scheduled events
		foreach ( $this->_time_blocks as $block ) {
			if ( $e = Event::create('BEGIN',$block->getBegin()) ) {
				$scheduled_events[] = $e;
			}
			if ( $e = Event::create('END',$block->getEnd()) ) {
				$scheduled_events[] = $e;
			}
		}
		// 'invert' scheduled events
		foreach ( $this->_time_blocks as $block ) {
			if ( $e = Event::create('END',$block->getBegin()-1) ) {
				$inverted_events[] = $e;
			}
			if ( $e = Event::create('BEGIN',$block->getEnd()+1) ) {
				$inverted_events[] = $e;
			}
		}
		// if beginning of day is not scheduled, add it
		if ( reset($scheduled_events) instanceof Event ) {
			if ( $this->_day_begin !== reset($scheduled_events)->getTime() && reset($scheduled_events)->getType() === 'BEGIN' ) {
				if ( $e = Event::create('BEGIN',$this->_day_begin) ) {
					array_unshift($inverted_events,$e);
				}
			}
		}
		// if end of day is not scheduled, add it
		if ( end($scheduled_events) instanceof Event ) {
			if ( $this->_day_end !== end($scheduled_events)->getTime() && end($scheduled_events)->getType() === 'END' ) {
				if ( $e = Event::create('END',$this->_day_end) ) {
					array_push($inverted_events,$e);
				}
			}
		}
		// read inverted event sequentially, create available time blocks for BEGIN/END pairs
		foreach ( $inverted_events as $e ) {
			if ( $previous_event instanceof Event && $previous_event->getType() === 'BEGIN' && $e->getType() === 'END' ) {
				if ( $available_time_block = TimeBlock::create($previous_event->getTime(),$e->getTime(),'AVAILABLE',0,'') ) {
					$this->_available_time_blocks[] = $available_time_block;
				}
			}
			$previous_event = $e;
		}
	}
	
	public function addNewTimeBlock($time_block=NULL){
		if ( $time_block instanceof TimeBlock ) {
			foreach ( $this->_time_blocks as $cmp_time_block ) {
				if ( $this->_noTimeConflict($time_block,$cmp_time_block) === FALSE ) {
					return FALSE;
				}
			}
			return $this->_insertKeySchedule($time_block);
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
	
	private function _insertKeySchedule($time_block=NULL){
		$sql = 'INSERT INTO key_schedule (`computer`,`begin`,`end`,`key`,`note`) VALUES (\''.$this->_computer_id.'\',\''.$time_block->getBegin().'\',\''.$time_block->getEnd().'\',\''.$this->_generateKey().'\',\''.'pending'.'\')';
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
	
	public function getTimeBlocks(){
		return $this->_time_blocks;
	}
	
	public function getAvailableTimeBlocks(){
		return $this->_available_time_blocks;
	}
}
?>
