<?php 
abstract class MySimpleXML 
{
	private $_parser;
	
	public function __construct($xml=NULL,$class_name=NULL,$options=NULL,$ns=NULL,$is_prefix=NULL){
		try {
			$this->_parser = new SimpleXMLElement($xml,$class_name,$options,$ns,$is_prefix);
		} catch (Exception $e) {
			//HACK: always return a valid simplexmlelement 
			#return 'Caught exception: ' . $e->getMessage();
			return $this->_parser = new SimpleXMLElement('<root><msg>Caught exception: ' . $e->getMessage() . '</msg></root>');
		}
	}
	
	public function regNameSpaces($namespaces=array()){
		foreach ( $namespaces as $prefix => $uri ) {
			$this->_regNameSpace($prefix,$uri);
		}

		return;
	}

	private function _regNameSpace($prefix=NULL,$uri=NULL){
		//NOTE: I determine namespace prefixes and URIs using Firebug
		if ( is_string($prefix) && is_string($uri) ) {
			$this->_parser->registerXPathNamespace($prefix, $uri);
		}

		return;
	}
	
	public function getXpathObjects($xpath=''){
		//NOTE: xpath method returns an array or false
		return $this->_parser->xpath($xpath); 
	}
	
	public function getPlainText($object=NULL){
		if ( $this->_isSimpleXMLElement($object) === FALSE ) {
			return FALSE;
		}
		$content = '';
		$content = $object->asXML();
		$content = strip_tags($content);
		$content = $this->_normalizeWhiteSpace($content);

		return $content;
	}

	private function _isSimpleXMLElement($object=NULL){
		if ( is_object($object) && get_class($object) === 'SimpleXMLElement' ) {
		return TRUE;
		} else {
		return FALSE;
		}
	}

	private function _normalizeWhiteSpace($string=''){
		return preg_replace('/\s{2,}/', ' ', trim($string));
	}
}
?>