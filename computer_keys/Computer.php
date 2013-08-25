<?php 

require_once(dirname(__FILE__).'/Event.php');

class Computer
{
	private $_name = NULL;
	private $_ip = NULL;
	private $_id = NULL;
	private $_time_blocks = array();
	private $_scheduled_events = array();
	private $_available_events = array();
	
	private function __construct($name=NULL,$id=NULL,$ip=NULL){
		$this->_name = $name;
		$this->_id = $id;
		$this->_ip = $ip;
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
	
	public function getScheduledEvents(){
		return $this->_scheduled_events;
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
	
	public function setScheduledEvents(){
		$this->sortTimeBlocks();
		foreach ( $this->getTimeBlocks() as $block ) {
			$this->_addScheduledEvent(Event::create('BEGIN',$block->getBegin()));
			$this->_addScheduledEvent(Event::create('END',$block->getEnd()));
		}
	}
	
	private function _addScheduledEvent($arg=NULL){
		if ( $arg instanceof Event ) {
			$this->_scheduled_events[] = $arg;
		}
	}
}
?>
