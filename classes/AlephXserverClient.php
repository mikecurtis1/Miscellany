<?php 

class AlephXserverClient
{

	public function __construct($host='',$port='',$base='',$g=array(),$ccl_filter=''){
		$this->host = $host;
		$this->port = $port;
		$this->base = $base;
		$this->g = $g;
		$this->ccl = '';
		$this->url = '';
		$this->ccl_filter = $ccl_filter;
		$this->set_number = '';
		$this->no_records = 0; //NOTE: number of actual matches, may be greater than no of entries
		$this->no_entries = 0; //NOTE: number of entries added to results set, may be smaller than number of matched records
		$this->session_id = '';
		$this->run_sort = FALSE;
		if ( isset($g['query']) ) {
			$this->ccl = $g['query'];
		} else {
			$this->g['query'] = $this->ccl;
		}
		if ( isset($g['set_number']) ) {
			$this->set_number = $g['set_number'];
		} else {
			$this->g['set_number'] = $this->set_number;
		}
		if ( isset($g['no_entries']) ) {
			$this->no_entries = $g['no_entries'];
		} else {
			$this->g['no_entries'] = $this->no_entries;
		}
		if ( isset($g['session_id']) ) {
			$this->session_id = $g['session_id'];
		} else {
			$this->g['session_id'] = $this->session_id;
		}
		if ( isset($g['sort']) ) {
			/**
			* common Aleph sort codes: 
			* 01 = date
			* 02 = author
			* 03 = title
			* 04 = LOC
			* common Aleph sort orders: 
			* A = ascending
			* D = descending
			*/
			$this->run_sort = TRUE;
			if ( $g['sort'] === 'author' ) {
				$this->c1 = '02';
				$this->o1 = 'A';
				$this->c2 = '01';
				$this->o2 = 'A';
			} elseif ( $g['sort'] === 'date' ) {
				$this->c1 = '01';
				$this->o1 = 'D';
				$this->c2 = '03';
				$this->o2 = 'A';
			} elseif ( $g['sort'] === 'title' ) {
				$this->c1 = '03';
				$this->o1 = 'A';
				$this->c2 = '01';
				$this->o2 = 'A';
			}
		} else {
			$this->c1 = '';
			$this->o1 = '';
			$this->c2 = '';
			$this->o2 = '';
		}
	}

	public function search($start,$end){
		$xml = '';
		$this->_setCcl();
		if ( preg_match("/^sys_no=(\d{9})$/i",$this->ccl,$sys_no) ) {
			if ( isset($sys_no[1]) ) {
				return $this->_circStatus($sys_no[1]);
			}
		}
		if ( $this->session_id === '' && $this->ccl !== '' ) {
			$this->_find();
		}
		if ( $this->set_number !== '' && $this->run_sort === TRUE ) {
			$this->_sort();
		}
		if ( $this->set_number !== '' ) {
			$xml = $this->_present($start,$end);
		}

		return $xml;
	}
	
	private function _setCcl() {
		if ( $this->ccl !== '' ) {
			if ( $this->ccl_filter !== '' ) {
				$this->ccl .= ' AND ' . $this->ccl_filter;
			}
		} elseif ( $this->ccl === '' ) {
			$this->ccl = $this->ccl_filter;
		} else {
			$this->ccl = '';
		}
	}

	private function _find(){
		
		if ( $this->ccl !== '' ) {
			$url = 'http://'.$this->host.':'.$this->port.'/X?op=find&base='.$this->base.'&request='.urlencode($this->ccl);
			echo $url;
			$xml = file_get_contents($url);
			echo htmlspecialchars($xml);
			preg_match("/\<set\_number\>(.*?)\<\/set\_number\>/", $xml, $set);
			preg_match("/\<no\_records\>(.*?)\<\/no\_records\>/", $xml, $no_records);
			preg_match("/\<no\_entries\>(.*?)\<\/no\_entries\>/", $xml, $no_entries);
			preg_match("/\<session\-id\>(.*?)\<\/session\-id\>/", $xml, $session);
			if ( isset($set[1]) ) {
				$this->g['set_number'] = $set[1];
			}
			if ( isset($no_entries[1]) ) {
				$this->g['no_entries'] = $no_entries[1];
			}
			if ( isset($session[1]) ) {
				$this->session_id = $session[1];
				$this->g['session_id'] = $session[1];
			}
		}
		//TODO: handle protocol and port
		$location = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"].'?'.http_build_query($this->g,'','&');
		header('Location: '.$location);
	}

	private function _sort(){
		if ( ($this->c1 !== '' && $this->o1 !== '') || ($this->c1 !== '' && $this->o1 !== '' && $this->c2 !== '' && $this->o2 !== '') ) {
			$url = 'http://'.$this->host.':'.$this->port.'/X?op=sort-set&library='.$this->base.'&set_number='.$this->set_number.'&sort_code_1='.$this->c1.'&sort_order_1='.$this->o1;
			if ( $this->c2 !== '' && $this->o2 !== '' ) {
				$url .= '&sort_code_2='.$this->c2.'&sort_order_2='.$this->o2;		
			} else {
				$url .= '&sort_code_2='.$this->c1.'&sort_order_2='.$this->o1;
			}
			$xml = file_get_contents($url);
			preg_match("/\<session\-id\>(.*?)\<\/session\-id\>/", $xml, $session);
			if ( isset($session[1]) ) {
				return $session[1];
			}
		} else {
			return FALSE;
		}
	}

	private function _present($start='',$end=''){
		$xml = '';
		if ( $this->set_number !== '' ) {
			$entry_numbers = sprintf("%09d",$start).'-'.sprintf("%09d",$end);
			$url = 'http://'.$this->host.':'.$this->port.'/X?op=present&set_no='.$this->set_number.'&set_entry='.$entry_numbers.'&format=marc';
			$xml = file_get_contents($url);
		}

		return $xml;
	}

	private function _circStatus($host='',$port='',$base='',$sys_no='000000000'){
		$xml = '';
		$url = 'http://'.$this->host.':'.$this->port.'/X?op=circ_status&sys_no='.$sys_no.'&library='.$this->base;
		$xml = file_get_contents($url);
		if ( strpos($xml, '<item-data>') ) {
			$this->no_entries = '000000001';
		}

		return $xml;
	}
}
?>
