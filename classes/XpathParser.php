<?php

	function getValues($array,$delimiter=' '){
		$string = '';
		if(is_array($array)){
			foreach($array as $value){
				$string .= strip_tags($value->asXml().$delimiter); 
			}
		}
		
		return trim($string);
	}
	
	public function getItemXml(){
	
	}

	function parseItem($xml=NULL,$namespace=NULL){
	
	  /*$map = array(
	  array("Common","author","slim:datafield[@tag='100']/slim:subfield[@code='a']"," "),
	  array("Common","title","slim:datafield[@tag='245']/slim:subfield"," "),
	  array("Common","title_main","slim:datafield[@tag='245']/slim:subfield[@code='a']"," "),
	  array("Common","title_sub","slim:datafield[@tag='245']/slim:subfield[@code='b']"," "),
	  array("Common","title_number_of_part","slim:datafield[@tag='245']/slim:subfield[@code='n']"," "),
	  array("Common","title_name_of_part","slim:datafield[@tag='245']/slim:subfield[@code='p']"," "),
	  array("Common","imprint","slim:datafield[@tag='260']/slim:subfield"," "),
	  array("Common","leader","slim:leader"," "),
	  array("Common","phys_descip","slim:datafield[@tag='300']/slim:subfield"," "),
	  array("Common","medium","slim:datafield[@tag='245']/slim:subfield[@code='h']"," "),
	  array("LORI","topics","slim:datafield[@tag='650']/slim:subfield","|"),
	  array("EBSCO","an","slim:controlfield[@tag='001']"," "),
	  array("LORI","isbns","slim:datafield[@tag='020']/slim:subfield[@code='a']"," "),
	  array("EBSCO","issn","slim:datafield[@tag='022']/slim:subfield"," "),
	  array("EBSCO","link","slim:datafield[@tag='856']/slim:subfield","|"),
	  array("EBSCO","language","slim:datafield[@tag='546']/slim:subfield"," "),
	  array("LORI","price","slim:datafield[@tag='938']/slim:subfield[@code='c']"," "),
	  array("LORI","form","slim:datafield[@tag='650']/slim:subfield[@code='v']"," "),
	  array("LORI","edition","slim:datafield[@tag='250']/slim:subfield[@code='a']"," "),
	  array("LORI","publisher","slim:datafield[@tag='260']/slim:subfield[@code='b']"," "),
	  array("LORI","pubdate","slim:datafield[@tag='260']/slim:subfield[@code='c']"," "),
	  array("EBSCO","journal","slim:datafield[@tag='945']/slim:subfield[@code='t']"," "),
	  array("EBSCO","doi","slim:datafield[@tag='852']/slim:subfield"," "),
	  array("EBSCO","date","slim:datafield[@tag='945']/slim:subfield[@code='d']"," "),
	  array("EBSCO","volume","slim:datafield[@tag='945']/slim:subfield[@code='m']"," "),
	  array("EBSCO","issue","slim:datafield[@tag='945']/slim:subfield[@code='n']"," "),
	  array("EBSCO","startpage","slim:datafield[@tag='945']/slim:subfield[@code='p']"," ")
    );*/
	
		$item = array();
		libxml_use_internal_errors(true); // http://www.php.net/manual/en/simplexmlelement.construct.php#103058
		try {
			$xmlObject = new SimpleXMLElement($xml);
		} catch (Exception $e) {
			return 'Caught exception: ' . $e->getMessage();
		}
		
		/*

		slim:datafield[@tag='100']/slim:subfield[@code='a']
		/record/datafield[7]/subfield
		/collection/record/datafield[7]/subfield

		*/

		#$xmlObject->registerXPathNamespace('', '');
		#$xpath = "datafield[@tag='100']/subfield[@code='a']";
		
		#//$xmlObject->registerXPathNamespace('', '');
		#$xpath = "datafield[@tag='100']/subfield[@code='a']";
		
		//$xmlObject->registerXPathNamespace('slim', 'http://www.loc.gov/MARC21/slim');
		$xpath = "//collection/record/datafield[@tag='100']/subfield[@code='a']";
		
		$object = $xmlObject->xpath($xpath);
		$item['marc100'] = getValues($object,'|');
		
		//HACK: create a better parser map
		/*foreach($map as $line){
			$label = $line[1];
			$xpath = $line[2];
			$delimiter = $line[3];
			$object = $xmlObject->xpath($xpath);
			$item[$label] = getValues($object,$delimiter);
		}*/

		return $item;
	}

?>
