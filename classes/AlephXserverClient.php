<?php 

class AlephXserverClient
{
	private $_host = '';
	private $_port = '';
	private $_base = '';
	private $_g = array();
	private $_ccl = '';
	private $_ccl_filter = '';
	private $_set_number = '';
	private $_no_records = 0; //NOTE: number of actual matches, may be greater than no of entries
	private $_no_entries = 0; //NOTE: number of entries added to results set, may be smaller than number of matched records
	private $_session_id = '';
	private $_sort_date_code = '01';
	private $_sort_author_code = '02';
	private $_sort_title_code = '03';
	private $_sort_ascend_code = 'A';
	private $_sort_descend_code = 'D';
	
	public function __construct($host='',$port='',$base='',$g=array()){
		$this->_host = $host;
		$this->_port = $port;
		$this->_base = $base;
		$this->_g = $g;
	}
	
	public function circStatus($sys_no='000000001'){
		$xml = '';
		$url = 'http://'.$this->_host.':'.$this->_port.'/X?op=circ_status&sys_no='.$sys_no.'&library='.$this->_base;
		$xml = file_get_contents($url);

		return $xml;
	}

	public function search($ccl='',$ccl_filter='',$start,$end){
		$xml = '';
		$this->_ccl = $ccl;
		$this->_ccl_filter = $ccl_filter;
		if ( isset($this->_g['set_number']) ) {
			$this->_set_number = $this->_g['set_number'];
		} else {
			$this->_g['set_number'] = $this->_set_number;
		}
		if ( isset($this->_g['no_entries']) ) {
			$this->_no_entries = $this->_g['no_entries'];
		} else {
			$this->_g['no_entries'] = $this->_no_entries;
		}
		if ( isset($this->_g['session_id']) ) {
			$this->_session_id = $this->_g['session_id'];
		} else {
			$this->_g['session_id'] = $this->_session_id;
		}
		if ( $this->_session_id === '' && $this->_ccl !== '' ) {
			$this->_find();
		}
		if ( $this->_set_number !== '' && isset($this->_g['sort']) ) {
			$this->_sort();
		}
		if ( $this->_set_number !== '' ) {
			$xml = $this->_present($start,$end);
		}
		
		return $xml;
	}
	
	private function _setCcl() {
		if ( $this->_ccl !== '' ) {
			if ( is_string($this->_ccl_filter) && $this->_ccl_filter !== '' ) {
				$this->_ccl .= ' AND ' . $this->_ccl_filter;
			}
		} elseif ( $this->_ccl === '' ) {
			$this->_ccl = $this->_ccl_filter;
		} else {
			$this->_ccl = '';
		}
		
		return;
	}

	private function _find(){
		$this->_setCcl();
		if ( $this->_ccl !== '' ) {
			$url = 'http://'.$this->_host.':'.$this->_port.'/X?op=find&base='.$this->_base.'&request='.urlencode($this->_ccl);
			$xml = file_get_contents($url);
			preg_match("/\<set\_number\>(.*?)\<\/set\_number\>/", $xml, $set);
			preg_match("/\<no\_records\>(.*?)\<\/no\_records\>/", $xml, $no_records);
			preg_match("/\<no\_entries\>(.*?)\<\/no\_entries\>/", $xml, $no_entries);
			preg_match("/\<session\-id\>(.*?)\<\/session\-id\>/", $xml, $session);
			if ( isset($set[1]) ) {
				$this->_g['set_number'] = $set[1];
			}
			if ( isset($no_entries[1]) ) {
				$this->_g['no_entries'] = $no_entries[1];
			}
			if ( isset($session[1]) ) {
				$this->_session_id = $session[1];
				$this->_g['session_id'] = $session[1];
			}
		}
		$this->_redirect();
	}

	private function _sort(){
		if ( $this->_g['sort'] === 'author' ) {
			$c1 = $this->_sort_author_code;
			$o1 = $this->_sort_ascend_code;
			$c2 = $this->_sort_date_code;
			$o2 = $this->_sort_ascend_code;
		} elseif ( $this->_g['sort'] === 'date' ) {
			$c1 = $this->_sort_date_code;
			$o1 = $this->_sort_descend_code;
			$c2 = $this->_sort_title_code;
			$o2 = $this->_sort_ascend_code;
		} elseif ( $this->_g['sort'] === 'title' ) {
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
			preg_match("/\<session\-id\>(.*?)\<\/session\-id\>/", $xml, $session);
			if ( isset($session[1]) ) {
				unset($this->_g['sort']);
				$this->_redirect();
			}
		} else {
			return FALSE;
		}
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
	
	public function getNoEntries(){
		return $this->_no_entries;
	}
	
	private function _redirect(){
		//TODO: handle protocol and port
		$location = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.http_build_query($this->_g,'','&');
		header('Location: '.$location);
	}
}
?>
