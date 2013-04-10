<?php 
class SimpleXml 
{
  private $_parser;
	
	public function __construct($xml=NULL,$class_name=NULL,$options=NULL,$ns=NULL,$is_prefix=NULL){
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
	
	public function getXpathXml($xpath='',$get_plain_text=FALSE){
		$docs = array();
		foreach ( $this->_parser->xpath($xpath) as $obj ) { //NOTE: xpath method returns an array or false
			$docs[] = $obj->asXML();
		}
		if ( $get_plain_text === TRUE ) {
			return $this->getPlainText($docs);
		} else {
			return $docs;
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
}
?>
