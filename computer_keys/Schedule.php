<?php 

class Schedule
{
	private $_dbc = NULL;
	private $_computer_id = NULL;
	private $_day_begin = NULL;
	private $_day_end = NULL;
	private $_time_blocks = array();
	private $_available_time_blocks = array();
	
	private function __construct($arg=NULL,$computer_id=NULL){
		$this->_dbc = $arg;
		$this->_computer_id = $computer_id;
		$this->_day_begin = (string) strtotime(date("Y-m-d").' 00:00:00');
		$this->_day_end = (string) strtotime(date("Y-m-d").' 23:59:59');
		$this->_setTimeBlocks();
		usort($this->_time_blocks,array('Schedule','cmpTimeBlocks'));
		$this->_setAvailableTimeBlocks();
	}
	
	public static function create($arg=NULL,$computer_id=NULL){
		if ( $arg instanceof DbConnection && is_numeric($computer_id) ) {
			return new Schedule($arg,$computer_id);
		} else {
			return FALSE;
		}
	}
	
	private function _setTimeBlocks(){
		$sql = 'SELECT * FROM computers LEFT JOIN key_schedule ON key_schedule.computer = computers.id WHERE key_schedule.computer = '.$this->_computer_id.' AND key_schedule.active = \'y\' AND begin < '.$this->_day_end.' AND end > '.time();
		if ( $query = DbQuery::query($this->_dbc->getLink(),$this->_dbc->getDbName(),$sql) ) {
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
		// if current time of day is not scheduled, add it
		if ( reset($scheduled_events) instanceof Event ) {
			if ( time() !== reset($scheduled_events)->getTime() && reset($scheduled_events)->getType() === 'BEGIN' ) {
				if ( $e = Event::create('BEGIN',time()) ) {
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
			if ( $previous_event instanceof Event && $previous_event->getType() === 'BEGIN' && $e->getType() === 'END' && $previous_event->getTime() >= time() ) {
				if ( $available_time_block = TimeBlock::create($previous_event->getTime(),$e->getTime(),'AVAILABLE',0,'') ) {
					$this->_available_time_blocks[] = $available_time_block;
				}
			}
			$previous_event = $e;
		}
	}
	
	public function addNewTimeBlock($time_block=NULL){
		if ( $this->_noConflictingTimeBlocks($time_block) ) {
			return $this->_insertKeySchedule($time_block);
		} else {
			return FALSE;
		}
	}

	private function _noConflictingTimeBlocks($arg=NULL){
		if ( $arg instanceof TimeBlock ) {
			foreach ( $this->_time_blocks as $cmp_time_block ) {
				if ( $this->_noTimeConflict($arg,$cmp_time_block) === FALSE ) {
					return FALSE;
				}
			}
			return TRUE;
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
		if ( $key = $this->_generateKey() ) {
			$sql = 'INSERT INTO key_schedule (`computer`,`begin`,`end`,`key`,`note`) VALUES (\''.$this->_computer_id.'\',\''.$time_block->getBegin().'\',\''.$time_block->getEnd().'\',\''.$key.'\',\''.'pending'.'\')';
			if ( $query = DbQuery::query($this->_dbc->getLink(),$this->_dbc->getDbName(),$sql) ) {
				return $key;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
		
	}
	
	public function deactivateKey($key_schedule_id=NULL){
		if ( is_string($key_schedule_id) ) {
			$sql = 'UPDATE key_schedule SET active = \'n\' WHERE id='.$key_schedule_id;
			if ( $query = DbQuery::query($this->_dbc->getLink(),$this->_dbc->getDbName(),$sql) ) {
				return $key_schedule_id;
			} else {
				return FALSE;
			}
		}
	}
	
	public function extendEnd($key_schedule_id=NULL,$interval=NULL){
		if ( !is_int($interval) ) {
			return FALSE;
		} 
		if ( $t = $this->_getTimeBlockById($key_schedule_id) ) {
			$new_end_time = strval($t->getEnd()+$interval);
			$max_end_time = $this->_getMaxEndTime($key_schedule_id);
			if ( $t->getEnd() === $max_end_time )	{
				return FALSE;
			}
			if ( $new_end_time > $max_end_time ) {
				$new_end_time = $max_end_time;
			}
			return $this->_updateEnd($key_schedule_id,$new_end_time);
		} else {
			return FALSE;
		}
	}
	
	private function _getMaxEndTime($key_schedule_id=NULL){
		foreach ( $this->_time_blocks as $i => $t ) {
			if ( $t->getId() === $key_schedule_id ) {
				if ( isset($this->_time_blocks[$i+1]) ) {
					return strval($this->_time_blocks[$i+1]->getBegin()-1);
				} else {
					return strval($this->_day_end);
				}
			}
		}
	}
	
	private function _getTimeBlockById($key_schedule_id=NULL){
		if ( is_string($key_schedule_id) ) {
			foreach ( $this->_time_blocks as $t ) {
				if ( $t->getId() === $key_schedule_id ) {
					return $t;
				}
			}
		} else {
			return FALSE;
		}
	}
	
	private function _updateEnd($key_schedule_id=NULL,$new_end_time=NULL){
		if ( is_string($key_schedule_id) ) {
			$sql = 'UPDATE key_schedule SET end = \''.$new_end_time.'\' WHERE id='.$key_schedule_id;
			if ( $query = DbQuery::query($this->_dbc->getLink(),$this->_dbc->getDbName(),$sql) ) {
				return $key_schedule_id;
			} else {
				return FALSE;
			}
		}
	}
	
	private function _generateKey(){
		// NOTE: removed letters and numbers that look the same
		// NOTE: 57^4 = 10,556,001 combinations!
		$chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'; 
		$unique = FALSE;
		while ( $unique !== TRUE ) {
			$key = substr(str_shuffle($chars),0,4);
			$sql = 'SELECT * FROM key_schedule WHERE `key` = \''.$key.'\' AND begin >= '.$this->_day_begin.' AND end <= '.$this->_day_end;
			if ( $query = DbQuery::query($this->_dbc->getLink(),$this->_dbc->getDbName(),$sql) ) {
				if ( $query->getCount() === 0 ) {
					$unique = TRUE;
				}
			} else {
				break;
				return FALSE;
			}
		}
		return $key;
	}
	
	public function getTimeBlocks(){
		return $this->_time_blocks;
	}
	
	public function getAvailableTimeBlocks(){
		return $this->_available_time_blocks;
	}
}
?>
