<?php 

class AlephXserverClient{

  public function __construct($cfg,$g){
    $this->host = $cfg['host'];
    $this->port = $cfg['port'];
    $this->base = $cfg['base'];
    $this->g = $g;
    $this->ccl = '';
    $this->set_number = '';
    $this->no_records = 0; // number of actual matches, may be greater than no of entries
    $this->no_entries = 0; // number of entries added to results set, may be smaller than number of matched records
    $this->session_id = '';
    if(isset($g['query'])){
      $this->ccl = $g['query'];
    } else {
      $this->g['query'] = $this->ccl;
    }
    if(isset($g['set_number'])){
      $this->set_number = $g['set_number'];
    } else {
      $this->g['set_number'] = $this->set_number;
    }
    if(isset($g['no_entries'])){
      $this->no_entries = $g['no_entries'];
    } else {
      $this->g['no_entries'] = $this->no_entries;
    }
    $this->c1 = '';
    $this->o1 = '';
    $this->c2 = '';
    $this->o2 = '';
  }
  
  public function search($start,$end){
    if(preg_match("/^sys=(\d{9})$/i",$this->ccl,$sys_no)){
      if(isset($sys_no[1])){
        $this->_circStatus($sys_no[1]);
      }
    }
    if($this->set_number === '' && $this->ccl !== ''){
      $this->_find();
    }
    $xml = $this->_present($start,$end);

    return $xml;
  }
  
  private function _find(){
    if($this->ccl !== ''){
      $url = 'http://'.$this->host.':'.$this->port.'/X?op=find&base='.$this->base.'&request='.urlencode($this->ccl);
      $xml = file_get_contents($url);
      preg_match("/\<set\_number\>(.*?)\<\/set\_number\>/", $xml, $set);
      preg_match("/\<no\_records\>(.*?)\<\/no\_records\>/", $xml, $no_records);
      preg_match("/\<no\_entries\>(.*?)\<\/no\_entries\>/", $xml, $no_entries);
      preg_match("/\<session\-id\>(.*?)\<\/session\-id\>/", $xml, $session);
      if(isset($set[1])){
        $this->g['set_number'] = $set[1];
      }
      if(isset($no_entries[1])){
        $this->g['no_entries'] = $no_entries[1];
      }
    }
    //TODO: replace absolute path
    $location = 'http://localhost/curtis/aleph/index.php?'.http_build_query($this->g,'','&');
    header('Location: '.$location);
  }
  
  //TODO: sort should only happen once unless new sort params are given
  private function _sort(){
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
    if($this->set_number !== ''){
      if(($this->c1 !== '' && $this->o1 !== '') || ($this->c1 !== '' && $this->o1 !== '' && $this->c2 !== '' && $this->o2 !== '')){
        $temp_session = '';
        $url = 'http://'.$this->host.':'.$this->port.'/X?op=sort-set&library='.$this->base.'&set_number='.$this->set_number.'&sort_code_1='.$this->c1.'&sort_order_1='.$this->o1;
		    if($this->c2 !== '' && $this->o2 !== ''){
		      $url .= '&sort_code_2='.$this->c2.'&sort_order_2='.$this->o2;		
		    } else {
		      $url .= '&sort_code_2='.$this->c1.'&sort_order_2='.$this->o1;
		    }
        $xml = file_get_contents($url);
        preg_match("/\<session\-id\>(.*?)\<\/session\-id\>/", $xml, $session);
        if(isset($session[1])){
          $temp_session = $session[1];
          return $temp_session;
        }
      } else {
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }
  
  private function _present($start='',$end=''){
    $xml = '';
    if($this->set_number !== ''){
      $entry_numbers = sprintf("%09d",$start).'-'.sprintf("%09d",$end);
      $url = 'http://'.$this->host.':'.$this->port.'/X?op=present&set_no='.$this->set_number.'&set_entry='.$entry_numbers.'&format=marc';
      $xml = file_get_contents($url);
    }
    
    return $xml;
  }
  
  private function _circStatus($sys_no='000000000'){
    $xml = '';
    $url = 'http://'.$this->host.':'.$this->port.'/X?op=circ_status&sys_no='.$sys_no.'&library='.$this->base;
    $xml = file_get_contents($url);
    
    return $xml;
  }
}

?>
