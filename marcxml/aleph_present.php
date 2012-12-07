<?php 
// dev environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8'); 
?>
<pre>
<?php 
// create instance
require_once(dirname(__FILE__).'/../../classes/MarcXML.php');
$xml = file_get_contents('xml/aleph_present.xml');
$r = new MarcXML($xml);
$records = $r->getXpathObjects('//present/record/metadata');
?>
<?php foreach ( $records as $cur ) {$x = new MarcXML($cur->asXML(),null,null,null,false); ?>
<?php 
// get data using xpath-based methods
$leader = $x->getFirstMarcTag('//oai_marc/fixfield[@id="LDR"]');
$identifier = $x->getFirstMarcTag('//oai_marc/fixfield[@id="001"]');
$title = $x->getFirstMarcTag('//oai_marc/varfield[@id="245"]');
$pubyear = $x->getFirstMarcTag('//oai_marc/varfield[@id="260"]/subfield[@label="c"]');
$people = $x->getAllMarcTags('//oai_marc/varfield[@id="700"]');
$topics = $x->getAllMarcTagsWithSubfields('//oai_marc/varfield[@id="650"]',array('a','z'),'label');
?>
<?php 
// display
echo $leader."\n";
echo $identifier."\n";
echo $title."\n";
echo $pubyear."\n";
print_r($people);
print_r($topics);
?>
<hr />
<?php } ?>
</pre>