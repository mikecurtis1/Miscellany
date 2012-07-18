<?php 

// http://www.loc.gov/standards/sru/index.html

/*
Some SRU services
http://z3950.loc.gov:7090/voyager? . $sru
http://www.worldcat.org/webservices/catalog/search/sru? . $sru . &servicelevel=full&sortKeys=Relevance&frbrGrouping=on&wskey= . $cfg->wskey
http://opencontent.indexdata.com/gutenberg? . $sru
http://opencontent.indexdata.com/wikipedia? . $sru
http://opencontent.indexdata.com/oaister? . $sru
*/

class Sru {

  var $versions = array("1.1" => "1.1");

  var $operations = array(
  "searchRetrieve"=>"searchRetrieve", 
  "scan"=>"scan", 
  "explain"=>"explain"
  );

  // http://www.loc.gov/standards/sru/resources/schemas.html
  var $record_schema = array(
  "marcxml"=>"info:srw/schema/1/marcxml-v1.1", 
  "dc"=>"info:srw/schema/1/dc-v1.1"
  );

  var $record_packing = array(
  "xml"=>"xml"
  );

	public function __construct(){
	  $this->version = $versions['1.1'];
	  $this->operation = $operations['searchRetrieve'];
	  $this->startRecord = 1;
	  $this->maximumRecords = 10;
	  $this->recordSchema = $record_schema['marcxml'];
	  $this->recordPacking = $record_packing['xml'];
	  $this->resultSetTTL = 300;
	  $this->recordXPath = '';
	}
	
	$params = array_merge($defaults, $user_params);

		// if user params are not valid values, replace with defaults
		if(!isset($this->versions[$params['version']]))
		{
		$params['version'] = $defaults['version'];
		}
		if(!isset($this->operations[$params['operation']]))
		{
		$params['operation'] = $defaults['operation'];
		}
		if(!preg_match("/\d+/",$params['startRecord']))
		{
		$params['startRecord'] = $defaults['startRecord'];
		}
		if(!preg_match("/\d+/",$params['maximumRecords']))
		{
		$params['maximumRecords'] = $defaults['maximumRecords'];
		}
		if(isset($params['resultSetTTL']) && !preg_match("/\d+/",$params['resultSetTTL']))
		{
		unset($params['result_set_ttl']);
		}
		if(isset($params['recordXPath']) && !filter_var($params['recordXPath'], FILTER_VALIDATE_URL))
		{
		unset($params['record_xpath']);
		}
		if(!isset($this->record_schema[$params['recordSchema']]))
		{
		$params['recordSchema'] = $defaults['recordSchema'];
		}
		if(!isset($this->record_packing[$params['recordPacking']]))
		{
		$params['recordPacking'] = $defaults['recordPacking'];
		}

	$sru = http_build_query($params, "", "&");

	return $sru;

	}

}

?>
