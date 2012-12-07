<?php 
// dev environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<pre>
<?php 
// create instance
require_once(dirname(__FILE__).'/../../classes/MarcXML.php');
$xml = file_get_contents('xml/aleph.xml');
$x = new MarcXML($xml,null,null,'http://www.loc.gov/MARC21/slim',false);
$ns = array('slim'=>'http://www.loc.gov/MARC21/slim');
$x->regNameSpaces($ns);
?>
<?php 
// get data using xpath-based methods
$leader = $x->getFirstMarcTag('//slim:record/slim:leader');
$identifier = $x->getFirstMarcTag('//slim:record/slim:controlfield[@tag="001"]');
$title = $x->getFirstMarcTag('//slim:record/slim:datafield[@tag="245"]');
$pubyear = $x->getFirstMarcTag('//slim:record/slim:datafield[@tag="260"]/slim:subfield[@code="c"]');
$people = $x->getAllMarcTags('//slim:record/slim:datafield[@tag="700"]');
$topics = $x->getAllMarcTagsWithSubfields('//slim:record/slim:datafield[@tag="650"]',array('a','z'));
?>
<?php include_once('opac_data.php'); ?>
<?php 
// display
header('Content-Type: text/html; charset=utf-8'); 
echo $leader."\n";
echo 'oclc:'.$identifier."\n";
echo $title."\n";
echo $pubyear."\n";
print_r($people);
print_r($topics);
?>
<hr />
<?php
echo var_dump($x);
?>
</pre>