<?php 

class Items {

  public function __construct(){
    
  }  
  
  private function _myImplode($delimiter,$var){
    if(!is_array($var)){
      return '';
    } else {
      return implode($delimiter,$var);
    }
  }

  public function parseItem($xml){
    $item = array();
    libxml_use_internal_errors(true); // http://www.php.net/manual/en/simplexmlelement.construct.php#103058
    try {
      $xmlObject = new SimpleXMLElement($xml);
    } catch (Exception $e) {
      return 'Caught exception: ' . $e->getMessage();
    }
    $xmlObject->registerXPathNamespace('slim', 'http://www.loc.gov/MARC21/slim');
    $xmlObject->registerXPathNamespace('diagnostic', 'http://www.loc.gov/zing/srw/diagnostic/');
    $message = (array) $xmlObject->xpath("/diagnostics/diagnostic:diagnostic/diagnostic:message");
    $marc100a = (array) $xmlObject->xpath("slim:datafield[@tag='100']/slim:subfield[@code='a']");
    $marc245 = (array) $xmlObject->xpath("slim:datafield[@tag='245']/slim:subfield");
    $marc938c = (array) $xmlObject->xpath("slim:datafield[@tag='938']/slim:subfield[@code='c']");
    $marc650v = (array) $xmlObject->xpath("slim:datafield[@tag='650']/slim:subfield[@code='v']");
    $marc650 = (array) $xmlObject->xpath("slim:datafield[@tag='650']/slim:subfield");
    $marc250a = (array) $xmlObject->xpath("slim:datafield[@tag='250']/slim:subfield[@code='a']");
    $marc020a = (array) $xmlObject->xpath("slim:datafield[@tag='020']/slim:subfield[@code='a']");
    $marc260b = (array) $xmlObject->xpath("slim:datafield[@tag='260']/slim:subfield[@code='b']");
    $marc260c = (array) $xmlObject->xpath("slim:datafield[@tag='260']/slim:subfield[@code='c']");
    $item['message'] = $this->_myImplode('',$message);
    $item['author'] = $this->_myImplode('',$marc100a);
    $item['title'] = $this->_myImplode(' ',$marc245);
    $item['price'] = $this->_myImplode('|',$marc938c);
    $item['form'] = $this->_myImplode('|',$marc650v);
    $item['topics'] = $this->_myImplode('|',$marc650);
    $item['edition'] = $this->_myImplode('|',$marc250a);
    $item['isbns'] = $this->_myImplode('|',$marc020a);
    $item['publisher'] = $this->_myImplode('|',$marc260b);
    $item['pubdate'] = $this->_myImplode('|',$marc260c);
    
    return $item;
  }

}

?>
