<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8'); 
?>
<pre>
<?php 
#$xml = utf8_encode(file_get_contents('xml.xml'));
$xml = file_get_contents('xml.xml');
$parser = new SimpleXMLElement($xml);
echo var_dump($parser);
?>
<hr />
<?php 
$xpath = "/root/tagset/datum[@type='lccn']";
$result = $parser->xpath($xpath);
echo var_dump($result);
?>
<hr />
<?php 
$text = _getPlainText($result[0]);
echo var_dump($text);
?>
<hr />
<?php 
$xpath = "/root/tagset/datum";
$data = $parser->xpath($xpath);
echo var_dump($data);
foreach ( $data as $datum ) {
	$text = _getPlainText($datum);
	echo var_dump($text);
}
?>
</pre>
<?php 
  function _getPlainText($object=NULL){
    if ( _isSimpleXMLElement($object) === FALSE ) {
      return FALSE;
    }
    $content = '';
    $content = $object->asXML();
    $content = strip_tags($content);
    $content = _normalizeWhiteSpace($content);
    
    return $content;
  }
  
  function _isSimpleXMLElement($object=NULL){
    if ( is_object($object) && get_class($object) === 'SimpleXMLElement' ) {
      return TRUE;
    } else {
	  return FALSE;
	}
  }
  
  function _normalizeWhiteSpace($string=''){
	return preg_replace('/\s{2,}/', ' ', trim($string));
  }
?>