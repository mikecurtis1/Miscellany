<?php 

class WorldCatFetch{

  public function __construct($wskey=NULL,$mode=NULL){
    $this->wskey = $wskey;
    if($mode == 'OCLC'){
      $this->modepath = '';
    } elseif ($mode == 'ISBN') {
      $this->modepath = 'isbn/';
    } else {
      $this->modepath = '';
    }
  }

  public function fetchWorldCatRecords($numbers=array()){
    foreach($numbers as $line => $number){
      $number = trim($number);
      $filename = '/media/sf_Documents/xml/'.$number.'.xml'; // run: sudo php fetch.php
      $errorString = '';
      if(!is_file($filename)){
        $xml = $this->_getMarcXML($number,$errorString);
        if($xml != FALSE){
          $this->_makeFile($filename, $xml, 'w');
        }
        sleep(1);
      } else {
         $errorString = 'file already exists.';
      }
      #echo date("c")." : {$line} : {$filename} : {$errorString} \n ----- \n";
    }
  }

  private function _makeFile($filename, $content, $mode='w'){
    if(!is_file($filename)){
      $ourFileHandle = fopen($filename, $mode); // mode a = append, w = write
      fwrite($ourFileHandle, $content); 
      fclose($ourFileHandle);
    }
  }

  private function _getMarcXML($number,  &$errorString=NULL){
    $url = $this->_makeWorldCatUrl($number);
	$errorString = 'success';
	$ctx = stream_context_create(array('http' => array('timeout' => 2))); // timeout in seconds
	$xml = file_get_contents($url, 0, $ctx);
	if($xml === FALSE){
		$errorString = 'file_get_content timed out: ' . $number . "\n" . $url;
		return FALSE;
	}
	
    return $xml;
  }

  private function _makeWorldCatUrl($number){
    $url = 'http://www.worldcat.org/webservices/catalog/content/'.$this->modepath.urlencode($number).'?servicelevel=full&wskey='.$this->wskey;

    return $url;  
  }
  
}

?>
