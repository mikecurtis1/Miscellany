<?php 

require_once(dirname(__FILE__).'/../ConfigPrivate.php');
$cfg = new ConfigPrivate();

require_once(dirname(__FILE__).'/../classes/MarcXml2Php.php');

#$marc = new MarcXml2Php('marc');
#$xml = file_get_contents('http://www.loc.gov/standards/marcxml/xml/collection.xml'); // namespace problem?

$marc = new MarcXml2Php();
#$xml = file_get_contents('');
#$xml = file_get_contents('http://www.loc.gov/standards/marcxml/Sandburg/sandburg.xml');
// http://ups.sunyconnect.suny.edu:4360/X?op=find&base=UPS01PUB&request=love
#$xml = file_get_contents('http://ups.sunyconnect.suny.edu:4360/X?op=present&set_no=001216&set_entry=000000001-000000003&format=marc');
$xml = file_get_contents('http://worldcat.org/webservices/catalog/search/sru?query=srw.kw+any+%22love%22&version=1.1&operation=searchRetrieve&recordSchema=info%3Asrw%2Fschema%2F1%2Fmarcxml&maximumRecords=10&startRecord=1&recordPacking=xml&servicelevel=full&sortKeys=relevance&resultSetTTL=300&recordXPath=&wskey='.$cfg->wskey);
$array = $marc->parse($xml);

echo '<pre>';
print_r($array);
echo '</pre>';

?>
