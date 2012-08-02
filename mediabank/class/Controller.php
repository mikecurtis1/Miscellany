<?php

/**
 * A controller can send commands to its associated view 
 * to change the view's presentation of the model (for 
 * example, by scrolling through a document). It can 
 * send commands to the model to update the model's 
 * state (e.g. editing a document).
 */

include_once('Model.php');
include_once('View.php');

class Controller
{
	
	public function __construct($host,$username,$password){
		$this->model = new Model($host,$username,$password);
		$this->view = new View();
		$this->skip = 0; 
		$this->first = 10;
		$this->back = 0;
		$this->modificamacchina = 'VB0000';
		$this->search = '';
	}
	
	public function httpRequest($get){
		if (isset($get['modificamacchina'])) {
			$this->modificamacchina = $get['modificamacchina'];
		}
		if (isset($get['search'])) {
			$this->search = $get['search'];
		}
		if (isset($get['skip'])) {
			$this->skip = $get['skip'];
			$this->back = $this->skip - $this->first;
			$this->next = $this->skip + $this->first;
		} else {
			$this->back = $this->skip;
			$this->next = $this->first;
		}
		if ($this->back < 0) {
			$this->back = 0;
		}

		return;
	}
	
	public function httpResponse(){
		try {
			$data = $this->model->requestData($this->first,$this->skip,$this->search,$this->modificamacchina);
		} catch (Exception $e) {
			//TODO: need a method to capture and handle errors instead of echo
			echo "EXCEPTION: Message: ".$e->getMessage().". File: ".$e->getFile().". Line: ".$e->getLine()."\n";
		}
		$items = $this->view->markupItems($data);
		$this->view->setNavURLs($this->search,$this->modificamacchina,$this->back,$this->next);
		include_once('template.html');
		
		return $items;
	}
	
	public function htmlEcho($name){
		if (isset($this->$name)) {
			echo htmlspecialchars($this->$name);
		} elseif(isset($this->view->$name)) {
			echo htmlspecialchars($this->view->$name);
		} else {
			
			return FALSE;
		}
	}
}
?>
