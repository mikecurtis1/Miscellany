<?php

/**
 * A model notifies its associated views and controllers when 
 * there has been a change in its state. This notification allows 
 * the views to produce updated output, and the controllers to 
 * change the available set of commands. A passive implementation 
 * of MVC omits these notifications, because the application does 
 * not require them or the software platform does not support them.
 */

class State{

	public function __construct(){
		$this->skip = 0; 
		$this->first = 10;
		$this->back = 0;
		$this->modificamacchina = 'VB0000';
		$this->modificamacchinaurl = '';
		$this->search = '';
		$this->searchurl = '';
	}
	
	public function setState($get){

		if(isset($get['modificamacchina'])){
			$this->modificamacchina = $get['modificamacchina'];
			$this->modificamacchinaurl = '&amp;modificamacchina='.urlencode($this->modificamacchina);
		}
	
		if(isset($get['search'])){
			$this->search = $get['search'];
			$this->searchurl = '&amp;search='.urlencode($this->search);
		}

		if(isset($get['skip'])){
			$this->skip = $get['skip'];
			$this->back = $this->skip - $this->first;
			$this->next = $this->skip + $this->first;
		} else {
			$this->back = $this->skip;
			$this->next = $this->first;
		}

		if($this->back < 0){
			$this->back = 0;
		}

		$this->new_url = 'index.php?skip=0'.$this->modificamacchinaurl;
		$this->start_url = 'index.php?skip=0'.$this->modificamacchinaurl.$this->searchurl;
		$this->back_url = 'index.php?skip='.$this->back.$this->modificamacchinaurl.$this->searchurl;
		$this->next_url = 'index.php?skip='.$this->next.$this->modificamacchinaurl.$this->searchurl;
		$this->last_url = 'index.php?skip='.$this->back.$this->modificamacchinaurl.$this->searchurl;
		
		return;
	}
}

?>
