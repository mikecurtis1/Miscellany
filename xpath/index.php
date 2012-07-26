<pre>
<?php 

// set PHP environment
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../classes/XpathParser.php');

// create object arguments and instance
$xml = utf8_encode(file_get_contents('marcxml/worldcat_content.xml'));
$prefix = 'slim';
$uri = 'http://www.loc.gov/MARC21/slim';
$x = new XpathParser($xml,$prefix,$uri);

// set xpaths for desired data
$xpath_title = "slim:datafield[@tag='245']/slim:subfield[@code='a']";
$xpath_topics = "slim:datafield[@tag='650']/slim:subfield";

// display
echo $x->parseXpath($xpath_title,NULL)."\n";
echo $x->parseXpath($xpath_topics,'|')."\n";
echo var_dump($x);
echo htmlspecialchars($xml);

/*$xml = utf8_encode(file_get_contents('marcxml/ebsco.xml'));
$prefix = 'slim';
$uri = 'http://www.loc.gov/MARC21/slim';
$x = new XpathParser($xml,$prefix,$uri);
$xpath = "slim:datafield[@tag='655']/slim:subfield";
#$xpath = "slim:datafield[@tag='100']/slim:subfield[@code='a']";*/

/*$xml = file_get_contents('marcxml/loc.xml');
$prefix = 'slim';
$uri = 'http://www.loc.gov/MARC21/slim';
$x = new XpathParser($xml,$prefix,$uri);
$xpath = "slim:record/slim:datafield[@tag='650']/slim:subfield";
#$xpath = "slim:record/slim:datafield[@tag='245']/slim:subfield[@code='a']";*/

/*$xml = file_get_contents('marcxml/aleph_opacrecord.xml');
$prefix = 'slim';
$uri = 'http://www.loc.gov/MARC21/slim';
$x = new XpathParser($xml,$prefix,$uri);
#$xpath = "bibliographicRecord/slim:record/slim:datafield[@tag='650']/slim:subfield";
$xpath = "bibliographicRecord/slim:record/slim:datafield[@tag='245']/slim:subfield[@code='a']";*/

/*$xml = utf8_encode(file_get_contents('marcxml/iii_opacrecord.xml'));
$prefix = 'slim';
$uri = 'http://www.loc.gov/MARC21/slim';
$x = new XpathParser($xml,$prefix,$uri);
$xpath = "bibliographicRecord/slim:record/slim:datafield[@tag='650']/slim:subfield";
#$xpath = "bibliographicRecord/slim:record/slim:datafield[@tag='245']/slim:subfield[@code='a']";*/

// set of marcxml records

/*$xml = utf8_encode(file_get_contents('marcxml/loc_collection.xml'));
$prefix = 'marc';
$uri = 'http://www.loc.gov/MARC21/slim';
$x = new XpathParser($xml,$prefix,$uri);
// marc:collection/marc:record[1]
$xpath = "marc:collection/marc:record";*/

/*$xml = file_get_contents('marcxml/loc_collection.xml');
$prefix = 'marc';
$uri = 'http://www.loc.gov/MARC21/slim';
$x = new XpathParser($xml,$prefix,$uri);
$xpath = "marc:record";
$recs = $x->getNodesAsXml($xpath);
#echo var_dump($recs);*/

?>
</pre>
