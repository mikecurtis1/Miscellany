<?php 

/**
 * Documentation
 * http://www.loc.gov/standards/sru/index.html
 * http://www.loc.gov/standards/sru/resources/schemas.html
 * http://www.loc.gov/standards/sru/specs/search-retrieve.html
 * 
 * Some SRU services
 * http://z3950.loc.gov:7090/voyager? . $sru
 * http://www.worldcat.org/webservices/catalog/search/sru?servicelevel=full&sortKeys=Relevance&frbrGrouping=on&wskey= . $cfg->wskey . $sru
 * http://opencontent.indexdata.com/gutenberg? . $sru
 * http://opencontent.indexdata.com/wikipedia?operation=searchRetrieve&version=1.1&query=dc.title%3D%22love%22&startRecord=1&maximumRecords=10
 * http://opencontent.indexdata.com/oaister? . $sru
 */

class SruClient {

	public function __construct($b='http://z3950.loc.gov:7090/voyager?',$m='10',$p='xml',$c='marcxml',$x='',$t='',$y=''){
	  $this->baseurl = $b;
	  $this->sru['operation'] = 'searchRetrieve'; // mandatory
	  $this->sru['version'] = '1.1'; // mandatory
	  $this->sru['query'] = ''; // mandatory
	  $this->sru['startRecord'] = '';
	  $this->sru['maximumRecords'] = $m;
	  $this->sru['recordPacking'] = $p;
	  $this->sru['recordSchema'] = $c;
	  if(filter_var($x, FILTER_VALIDATE_URL)){
		  $this->sru['recordXPath'] = $x;
		} else {
		  $this->sru['recordXPath'] = '';
		}
	  if(preg_match("/\d+/",$t)){
		  $this->sru['resultSetTTL'] = $t;
		} else {
		  $this->sru['resultSetTTL'] = '';
		}
	  $this->sru['sortKeys'] = '';
	  if(filter_var($x, FILTER_VALIDATE_URL)){
		  $this->sru['stylesheet'] = $y;
		} else {
		  $this->sru['stylesheet'] = '';
		}
	  $this->sru_string = '';
	  $this->url = '';
	  $this->response['numberOfRecords'] = '0';
	  $this->response['nextRecordPosition'] = '1';
	  $this->response['diagnostics'] = '';
	}
	
	public function search($q='',$s='1',$k=''){
	  $xml = '';
	  $this->sru['query'] = $q;
	  $this->sru['startRecord'] = $s;
	  $this->sru['sortKeys'] = $k;
	  $this->sru_string = $this->_setSruString();
	  $url = $this->_setURL();
	  $xml = file_get_contents($url);
	  $this->_setResponseValues($xml);
	  
	  return $xml;
	}
	
	private function _setSruString(){
	  $temp = array();
	  foreach($this->sru as $key => $value){
	    if($value !== ''){
	      $temp[$key] = $value;
	    }
	  }
	  $string = http_build_query($temp,'','&');
	  
	  return $string;
	}
	
	private function _setURL(){
	  $url = $this->baseurl.$this->sru_string;
	  $this->url = $url;
	  
	  return $url;
	}
	
	//HACK: regex XML parsing with .*? to avoid namespace issues
  private function _setResponseValues($xml){
    preg_match("/\<.*?numberOfRecords\>(.*?)\<\/.*?numberOfRecords\>/", $xml, $numberOfRecords);
    preg_match("/\<.*?nextRecordPosition\>(.*?)\<\/.*?nextRecordPosition\>/", $xml, $nextRecordPosition);
    preg_match("/\<.*?diagnostics\>(.*?)\<\/.*?diagnostics\>/", $xml, $diagnostics);
    if(isset($numberOfRecords[1])){
      $this->response['numberOfRecords'] = $numberOfRecords[1];
    }
    if(isset($nextRecordPosition[1])){
      $this->response['nextRecordPosition'] = $nextRecordPosition[1];
    }
    if(isset($diagnostics[1])){
      $this->response['diagnostics'] = $diagnostics[1];
    }
  }
}
	
?>
