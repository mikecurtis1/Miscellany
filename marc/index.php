<?php header('Content-Type: text/html; charset=utf-8'); ?>
<pre>
<?php 

#$xml = utf8_encode(file_get_contents('marcxml/ebsco.xml'));
#$xml = utf8_encode(file_get_contents('marcxml/worldcat_content.xml'));
#$xml = utf8_encode(file_get_contents('marcxml/loc.xml'));
#$xml = utf8_encode(file_get_contents('marcxml/aleph_opacrecord.xml'));
#$xml = utf8_encode(file_get_contents('marcxml/iii_opacrecord.xml'));
#$xml = utf8_encode(file_get_contents('marcxml/loc_collection.xml')); // use namespace 'marc' argument in constructor when creating object
#$xml = utf8_encode(file_get_contents('marcxml/aleph_present.xml'));
$xml = utf8_encode(file_get_contents('marcxml/worldcat_search.xml'));

require_once(dirname(__FILE__).'/../classes/MarcXml2Php.php');
$marc = new MarcXml2Php(NULL);
$array = $marc->parse($xml);

foreach($array as $i => $rec){
  echo $marc->getFieldValue($i,'LEADER')."\n";
  echo $marc->getFieldValue($i,'001')."\n";
  echo $marc->getFieldValue($i,'100')."\n";
  echo $marc->getFieldValue($i,'245')."\n";
  $isbn = $marc->getFieldValues($i,'020');
  $topics = $marc->getFieldValues($i,'650');
  $urls = $marc->getFieldValues($i,'856','u');
  $price = $marc->getFieldValues($i,'938','a,c');
  print_r($isbn);
  print_r($topics);
  print_r($urls);
  print_r($price);
  #print_r($rec);
  echo "<hr />\n";
}

?>
</pre>
