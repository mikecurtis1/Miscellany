<pre>
<?php 

class SimpleXMLTool  
{
  private $_parser;
  
  public function __construct($xml=NULL,$class_name=NULL,$options=NULL,$ns=NULL,$is_prefix=NULL){
    //NOTE: doesn't produce an object, but a system resource. be careful when examining results with var_dump
    //NOTE: http://www.php.net/manual/en/class.simplexmlelement.php#100811
    $this->_parser = new SimpleXMLElement($xml,$class_name,$options,$ns,$is_prefix);
	}
	
	public function regNameSpaces($namespaces=array()){
	  foreach ( $namespaces as $prefix => $uri ) {
	    $this->_regNameSpace($prefix,$uri);
	  }
	  
	  return;
	}
	
	private function _regNameSpace($prefix=NULL,$uri=NULL){
	  //NOTE: the only reliable way to determine namespace prefixes and URIs seems to be Firebug
	  if ( is_string($prefix) && is_string($uri) ) {
	    $this->_parser->registerXPathNamespace($prefix, $uri);
	  }
	  
	  return;
	}
  
  public function getXpathObjects($xpath=''){
		$result = '';
		$result = $this->_parser->xpath($xpath);
		
		return $result;
	}
	
  private function _getPlainText($object=NULL){
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
    $check = FALSE;
    if ( get_class($object) === 'SimpleXMLElement' ) {
      $check = TRUE;
    }
    
    return $check;
  }
  
  private function _normalizeWhiteSpace($string=''){
    $string = preg_replace('/\s{2,}/', ' ', $string);
    $string = trim($string);
    
    return $string;
  }
}
?>
<?php 

class IdsXpathParser
{
  public function __construct(){
  
  }
  
  public function parseXpaths($xpath_availability,$arr){
    while ( !empty($xpath_availability) ) {
      $data = array_shift($xpath_availability);
      $arr = $arr[0];
      $arr = $this->_parseXpath($arr,$data);
      return $this->parseXpaths($xpath_availability,$arr);
    } 
    return $arr;
  }

  private function _parseXpath($arr,$data,$results=array()){
    while ( !empty($arr) ) {
      $a = array_shift($arr);
      if ( is_object($a) && get_class($a) === 'SimpleXMLElement' ) {
        $o = $a->asXML();
        $simple_xml_tool = new SimpleXMLTool($o,NULL,NULL,NULL,NULL);
      } else {
        $simple_xml_tool = new SimpleXMLTool($a,NULL,NULL,NULL,NULL);
      }
      $simple_xml_tool->regNameSpaces(array($data['prefix']=>$data['uri']));
      $results[] = $simple_xml_tool->getXpathObjects($data['xpath']);
      return $this->_parseXpath($arr,$data,$results);
    }
    return $results;
  }
}
?>
<?php 

$xpath_availability = 
  array(
    'level1'=>array(
      'xpath'=>"bibliographicRecord/slim:record",
      'prefix'=>'slim',
      'uri'=>'http://www.loc.gov/MARC21/slim'
    ),
    'level2'=>array(
      'xpath'=>"slim:datafield",
      'prefix'=>'slim',
      'uri'=>'http://www.loc.gov/MARC21/slim'
    ),
    'level3'=>array(
      'xpath'=>"subfield[@code='a']",
      'prefix'=>NULL,
      'uri'=>NULL
    )
  );

$filepath = dirname(__FILE__).'/../marcxml/';
$xml = utf8_encode(file_get_contents($filepath.'iii_opacrecord.xml'));
$arr = array(array($xml));

$ids_xpath_parser = new IdsXpathParser();
$r = $ids_xpath_parser->parseXpaths($xpath_availability,$arr);

foreach($r as $a){
  echo " | " . (string) $a[0] . " | \n";
}

?>
</pre>
