<?php 

class SimpleXml 
{
  private $_parser;
	
	public function __construct($xml='',$class_name=NULL,$options=NULL,$ns=NULL,$is_prefix=NULL){
		try {
			$this->_parser = new SimpleXMLElement($xml,$class_name,$options,$ns,$is_prefix);
		} catch (Exception $e) {
			return 'Caught exception: ' . $e->getMessage();
		}
	}
	
	public function regNameSpaces($namespaces=array()){
		foreach ( $namespaces as $prefix => $uri ) {
			$this->_parser->registerXPathNamespace($prefix, $uri); //NOTE: determine namespace prefixes and URIs using Firebug
		}

		return;
	}
	
	public function getXpathXmlDocs($xpath=''){
		$docs = array();
		if ( $this->_isSimpleXMLElement($this->_parser) === TRUE ) {
			if ( is_array($this->_parser->xpath($xpath)) ) {
				foreach ( $this->_parser->xpath($xpath) as $obj ) { //NOTE: xpath method returns an array or false
					$docs[] = $obj->asXML();
				}
			}
		}
		
		return $docs;
	}
	
	public function getXpathText($xpath=''){
		$docs = $this->getXpathXmlDocs($xpath);
		if ( isset($docs[0]) ) {
			return $this->_normalizeWhiteSpace(strip_tags($docs[0]));
		} else {
			return '';
		}
	}
	
	public function getXpathAttr($xpath='',$attr_name=''){
		$a = $this->_parser->xpath($xpath);
		if ( isset($a[0]->attributes()->{$attr_name}) ) {
			return $a[0]->attributes()->{$attr_name};
		} else {
			return '';
		}
	}
	
	public function getPlainText($arg=NULL){
		if ( is_array($arg) ) {
			return $this->_normalizeWhiteSpace(strip_tags(array_shift($arg)));
		} elseif ( is_string($arg) ) {
			return $this->_normalizeWhiteSpace(strip_tags($arg));
		} else {
			return '';
		}
	}

	private function _normalizeWhiteSpace($string=''){
		return preg_replace('/\s{2,}/', ' ', trim($string));
	}
	
	private function _isSimpleXMLElement($object=NULL){
		if ( is_object($object) && get_class($object) === 'SimpleXMLElement' ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
