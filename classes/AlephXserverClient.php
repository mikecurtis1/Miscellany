<?php 

class AlephXserverClient
{
	private $_host = '';
	private $_port = '';
	private $_base = '';
	private $_ccl_suffix = '';
	private $_ccl = '';
	private $_request = '';
	private $_set_number = '';
	private $_no_records = NULL; //NOTE: number of actual matches, may be greater than no of entries
	private $_no_entries = NULL; //NOTE: number of entries added to results set, may be smaller than number of matched records
	private $_session_id = '';
	private $_sort_date_code = '01';
	private $_sort_author_code = '02';
	private $_sort_title_code = '03';
	private $_sort_ascend_code = 'A';
	private $_sort_descend_code = 'D';
	private $_exceptions = array();
	
	public function __construct($host='',$port='',$base='',$ccl_suffix=''){
		$this->_host = $host;
		$this->_port = $port;
		$this->_base = $base;
		$this->_ccl_suffix = $ccl_suffix;
	}
	
	public function search($ccl='',$start='1',$end='1',$sort='',$no_entries='0',$set_number='',$session_id=''){
		$this->_ccl = $ccl;
		$this->_setRequest();
		$this->_no_entries = $no_entries;
		$this->_set_number = $set_number;
		$this->_session_id = $session_id;
		if ( $this->_set_number === '' ) {
			$this->_parseFindXml($this->_find());
		}
		if ( $this->_set_number !== '' && $sort !== '' ) {
			$sort_result = $this->_sort($sort); //NOTE: successful sort result contains only a session ID
		}
		
		return $this->_present($start,$end);
	}
	
	public function circ_status($sys_no='000000001'){
		return $this->_circStatus($sys_no);
	}
	
	private function _setRequest(){
		if ( $this->_ccl !== '' && $this->_ccl_suffix !== '' ) {
			$this->_request = $this->_ccl . ' AND ' . $this->_ccl_suffix;
		} elseif ( $this->_ccl === '' && $this->_ccl_suffix !== '' ) {
			$this->_request = $this->_ccl_suffix;
		}  elseif ( $this->_ccl !== '' && $this->_ccl_suffix === '' ) {
			$this->_request = $this->_ccl;
		} else {
			$this->_request = '';
		}
		
		return;
	}

	private function _find(){
		$xml = '';
		if ( $this->_request !== '' ) {
			$url = 'http://'.$this->_host.':'.$this->_port.'/X?op=find&base='.$this->_base.'&request='.urlencode($this->_request);
			$xml = file_get_contents($url);
		}
		
		return $xml;
	}
	
	private function _parseFindXml($xml=''){
		try {
			$resource = new SimpleXMLElement($xml);
		} catch (Exception $e) {
			$this->_exceptions[] = 'CLASS: '.get_class().'. FUNCTION: _parseFindXml(). EXCEPTION: '.$e->getMessage();
		}
		if ( empty($this->_exceptions) ) {		
			$this->_set_number = (string) $resource->set_number;
			$this->_no_records = (string) $resource->no_records;
			$this->_no_entries = (string) $resource->no_entries;
			$this->_session_id = (string) $resource->{'session-id'};
		}
		
		return;
	}
	
	private function _present($start='1',$end='1'){
		$xml = '';
		if ( $this->_set_number !== '' ) {
			$entry_numbers = sprintf("%09d",$start).'-'.sprintf("%09d",$end);
			$url = 'http://'.$this->_host.':'.$this->_port.'/X?op=present&set_no='.$this->_set_number.'&set_entry='.$entry_numbers.'&format=marc';
			$xml = file_get_contents($url);
		}

		return $xml;
	}

	private function _sort($sort=''){
		$xml = '';
		if ( $sort === 'author' ) {
			$c1 = $this->_sort_author_code;
			$o1 = $this->_sort_ascend_code;
			$c2 = $this->_sort_date_code;
			$o2 = $this->_sort_ascend_code;
		} elseif ( $sort === 'date' ) {
			$c1 = $this->_sort_date_code;
			$o1 = $this->_sort_descend_code;
			$c2 = $this->_sort_title_code;
			$o2 = $this->_sort_ascend_code;
		} elseif ( $sort === 'title' ) {
			$c1 = $this->_sort_title_code;
			$o1 = $this->_sort_ascend_code;
			$c2 = $this->_sort_date_code;
			$o2 = $this->_sort_descend_code;
		} else {
			$c1 = '';
			$o1 = '';
			$c2 = '';
			$o2 = '';
		}
		if ( ($c1 !== '' && $o1 !== '') || ($c1 !== '' && $o1 !== '' && $c2 !== '' && $o2 !== '') ) {
			$url = 'http://'.$this->_host.':'.$this->_port.'/X?op=sort-set&library='.$this->_base.'&set_number='.$this->_set_number.'&sort_code_1='.$c1.'&sort_order_1='.$o1;
			if ( $c2 !== '' && $o2 !== '' ) {
				$url .= '&sort_code_2='.$c2.'&sort_order_2='.$o2;		
			} else {
				$url .= '&sort_code_2='.$c1.'&sort_order_2='.$o1;
			}
			$xml = file_get_contents($url);
		}
		
		return $xml;
	}
	
	private function _circStatus($sys_no){
		$xml = '';
		$url = 'http://'.$this->_host.':'.$this->_port.'/X?op=circ_status&sys_no='.$sys_no.'&library='.$this->_base;
		$xml = file_get_contents($url);

		return $xml;
	}
	
	public function getCcl(){
		return $this->_ccl;
	}
	
	public function getCclSuffix(){
		return $this->_ccl_suffix;
	}
	
	public function getRequest(){
		return $this->_request;
	}
	
	public function getSetNumber(){
		return $this->_set_number;
	}
	
	public function getNoEntries(){
		return $this->_no_entries;
	}
	
	public function getSessionId(){
		return $this->_session_id;
	}
	
	public function getExceptions(){
		return $this->_exceptions;
	}
}
?>
