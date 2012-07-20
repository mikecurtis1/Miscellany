<?php 

class WorldCat{

  var $operators = array(
  "and"=>"and",
  "or"=>"or",
  "not"=>"not"
  );

  var $relations = array(
  "all"=>"all",
  "="=>"=",
  "exact"=>"exact"
  );

  // http://worldcat.org/devnet/wiki/WorldCat_API_Index_Tips
  var $indexes = array(
  "kw" => "Keyword", 
  "au" => "Author", 
  "su" => "Subject", 
  "ti" => "Title", 
  "yr" => "Year", 
  "bn" => "ISBN", 
  "in" => "ISSN", 
  "no" => "OCLC #", 
  "dn" => "LCCN", 
  "sn" => "Standard #", 
  "mt" => "Material type", 
  "dt" => "Document type", 
  "li" => "Lib holdings", 
  "am" => "Access method", 
  "cn" => "Corp/conf name", 
  "dd" => "Dewey class #", 
  "pc" => "DLC limit", 
  "gn" => "Gov doc #", 
  "la" => "Language code", 
  "ln" => "Language", 
  "lc" => "LC class #", 
  "cg" => "Lib hold. group", 
  "mn" => "Music/pub. #", 
  "nt" => "Notes", 
  "pn" => "Personal name", 
  "pl" => "Place of pub.", 
  "pb" => "Publisher", 
  "se" => "Series"
  );

  var $sort_keys = array(
  "Relevance"=>"relevance",
  "Title"=>"title",
  "Author"=>"author",
  "Date,,0"=>"date",
  "Library Count"=>"library count",
  "Score,,0"=>"score",
  "OCLC Number"=>"OCLC number"
  );

  var $frbr_grouping = array(
  "on"=>"on",
  "off"=>"off"
  );

  public function __construct($wskey=NULL){
    $this->wskey = $wskey;
  }

  private function _getResponse($url, &$errorString=NULL){
	$errorString = 'success';
	$ctx = stream_context_create(array('http' => array('timeout' => 2))); // timeout in seconds
	$response = file_get_contents($url, 0, $ctx);
	if($response === FALSE){
		$errorString = 'file_get_content failed: ' . $url;
		return FALSE;
	}
	
    return $response;
  }

  private function _buildSearchURL($sru=NULL, $query=NULL, $service_level='full', $frbr_grouping='on'){
    $url = 'http://www.worldcat.org/webservices/catalog/search/sru?'.$sru.'&query='.$query.'&servicelevel='.$service_level.'&frbrGrouping='.$frbr_grouping.'&wskey='.$this->wskey;

    return $url;
  }

  private function _buildContentUrl($mode=NULL,$number=NULL,$service_level='full'){
    if($mode == 'OCLC'){
      $modepath = '';
    } elseif ($mode == 'ISBN') {
      $modepath = 'isbn/';
    } else {
      $modepath = '';
    }
    $url = 'http://www.worldcat.org/webservices/catalog/content/'.$modepath.urlencode($number).'?servicelevel='.$service_level.'&wskey='.$this->wskey;

    return $url;  
  }
  
}

?>
