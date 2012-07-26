<?php

class XpathParser {

	public function __construct($xml='',$prefix='',$uri=''){
	  #$this->parser = new SimpleXMLElement($xml, NULL, FALSE, $prefix, TRUE); // http://us.php.net/manual/en/simplexmlelement.construct.php
	  #$this->parser = new SimpleXMLElement($xml, NULL, FALSE, $uri, FALSE);
	  $this->parser = new SimpleXMLElement($xml);
	  $this->parser->registerXPathNamespace($prefix, $uri);
	}
	
	public function getValues($array,$delimiter=' '){
		$string = '';
		if(is_array($array)){
			foreach($array as $value){
				$string .= strip_tags($value->asXml().$delimiter); 
			}
		}
		
		return trim($string);
	}
	
	//TODO: the xml returned can produce namespace problems when parsed with this object
	public function getNodesAsXml($xpath=''){
	  $nodes = array();
	  $result = $this->parser->xpath($xpath);
    while(list( , $node) = each($result)) {
      $nodes[] = $node->asXML();
    }
    
    return $nodes;
	}

	public function parseXpath($xpath='',$delimiter=''){
		$object = $this->parser->xpath($xpath);
		$string = $this->getValues($object,$delimiter);
		
		return $string;
	}
}
?>
