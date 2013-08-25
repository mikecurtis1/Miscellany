<?php 

require_once(dirname(__FILE__).'/Event.php');
require_once(dirname(__FILE__).'/TimeBlock.php');

class Computer
{
	private $_name = NULL;
	private $_ip = NULL;
	private $_id = NULL;
	private $_day_begin = NULL;
	private $_day_end = NULL;
	private $_time_blocks = array();
	private $_available_time_blocks = array();
	
	private function __construct($name=NULL,$id=NULL,$ip=NULL){
		$this->_name = $name;
		$this->_id = $id;
		$this->_ip = $ip;
		$this->_day_begin = (string) strtotime(date("Y-m-d").' 00:00:00');
		$this->_day_end = (string) strtotime(date("Y-m-d").' 23:59:59');
	}
	
	public static function create($name=NULL,$id=NULL,$ip=NULL){
		if ( is_string($name) && is_numeric($id) && filter_var($ip,FILTER_VALIDATE_IP) ) {
			return new Computer($name,$id,$ip);
		} else {
			return FALSE;
		}
	}
	
	public function addTimeBlock($arg=NULL){
		if ( $arg instanceof TimeBlock ) {
			$this->_time_blocks[] = $arg;
		}
	}

	//TODO: make this function smaller, break it up?
	public function setAvailableTimeBlocks(){
		// init arrays
		$scheduled_events = array();
		$inverted_events = array();
		$previous_event = NULL;
		// sort first
		$this->sortTimeBlocks();
		// list scheduled events
		foreach ( $this->getTimeBlocks() as $block ) {
			if ( $e = Event::create('BEGIN',$block->getBegin()) ) {
				$scheduled_events[] = $e;
			}
			if ( $e = Event::create('END',$block->getEnd()) ) {
				$scheduled_events[] = $e;
			}
		}
		// 'invert' scheduled events
		foreach ( $this->getTimeBlocks() as $block ) {
			if ( $e = Event::create('END',$block->getBegin()-1) ) {
				$inverted_events[] = $e;
			}
			if ( $e = Event::create('BEGIN',$block->getEnd()+1) ) {
				$inverted_events[] = $e;
			}
		}
		// if beginning of day is not scheduled, add it
		if ( $this->_day_begin !== reset($scheduled_events)->getTime() && reset($scheduled_events)->getType() === 'BEGIN' ) {
			if ( $e = Event::create('BEGIN',$this->_day_begin) ) {
				array_unshift($inverted_events,$e);
			}
		}
		// if end of day is not scheduled, add it
		if ( $this->_day_end !== end($scheduled_events)->getTime() && end($scheduled_events)->getType() === 'END' ) {
			if ( $e = Event::create('END',$this->_day_end) ) {
				array_push($inverted_events,$e);
			}
		}
		// read inverted event sequentially, create available time blocks for BEGIN/END pairs
		foreach ( $inverted_events as $e ) {
			if ( $previous_event instanceof Event && $previous_event->getType() === 'BEGIN' && $e->getType() === 'END' ) {
				if ( $available_time_block = TimeBlock::create($previous_event->getTime(),$e->getTime(),'AVAILABLE') ) {
					$this->_available_time_blocks[] = $available_time_block;
				}
			}
			$previous_event = $e;
		}
	}
	
	public function sortTimeBlocks(){
		usort($this->_time_blocks,array('Computer','cmpTimeBlocks'));
	}
	
	public static function cmpTimeBlocks($a,$b){
		if ( $a instanceof TimeBlock && $b instanceof TimeBlock ) {
			return ($a->getBegin() < $b->getBegin()) ? -1 : 1;
		} else {
			return 1;
		}
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function getIP(){
		return $this->_ip;
	}
	
	public function getTimeBlocks(){
		return $this->_time_blocks;
	}
	
	public function getAvailableTimeBlocks(){
		return $this->_available_time_blocks;
	}
}
?>
