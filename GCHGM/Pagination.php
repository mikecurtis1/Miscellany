<?php

class Pagination
{

  public function __construct($limit=10,$urlbase=''){
		$this->hits = 0;
		$this->limit = $limit;
		$this->quantity = $this->limit;
		$this->start = 1;
		$this->end = 0;
		$this->previous = 0;
		$this->next = 0;
		$this->last = 0;
		$this->new_url = '';
		$this->start_url = '';
		$this->previous_url = '';
		$this->next_url = '';
		$this->last_url = '';
		$this->urlbase = $urlbase;
	}
  
	public function setValues($hits=0,$start=1){
		$this->hits = $hits;
		$this->start = $start;
		$this->end = ($this->start + $this->limit) - 1;
		$this->previous = $this->start - $this->limit;
		$this->next = $this->start + $this->limit;
		$this->last = ($this->hits - ($this->hits % $this->limit)) + 1;
		if ( $this->end > $this->hits ) {
			$this->end = $this->hits;
		}
		if ( $this->previous <= 0 ) {
			$this->previous = 1;
		}
		if ( $this->next > $this->hits ) {
			$this->next = $start;
		}
		if ( $this->last <= 0 ) {
			$this->last = 1;
		}
		if ( ($this->start + $this->limit) > $this->hits ) {
			$this->quantity = ($this->hits - $this->start) + 1;
		}
	}
  
	public function setURLs($g=array()){
		$g['start'] = 1;
		$this->start_url = $this->urlbase.'?'.http_build_query($g,'','&');
		$g['start'] = $this->previous;
		$this->previous_url = $this->urlbase.'?'.http_build_query($g,'','&');
		$g['start'] = $this->next;
		$this->next_url = $this->urlbase.'?'.http_build_query($g,'','&');
		$g['start'] = $this->last;
		$this->last_url = $this->urlbase.'?'.http_build_query($g,'','&');
		$g['start'] = 1;
		$g['query'] = '';
		$this->new_url = $this->urlbase.'?'.http_build_query($g,'','&');
	}
  
	public function getValue($name=NULL){
		if ( isset($this->$name) ) {
			return $this->$name;
		} else {
			return '';
		}
	}
}

?>
