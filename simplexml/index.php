<?php 
// dev environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8'); 
?>
<pre>
<?php 
// create instance, deal with namespaces
require_once(dirname(__FILE__).'/SimpleXml.php');
$xml = file_get_contents('https://raw.github.com/mikecurtis1/curtis/master/marcxml/xml/wc_collection.xml');
$x = new SimpleXml($xml,NULL,NULL,'http://www.loc.gov/zing/srw/',FALSE);
$ns = array('srw'=>'http://www.loc.gov/zing/srw/');
$x->regNameSpaces($ns);
$records = $x->getXpathXml('//srw:searchRetrieveResponse/srw:records/srw:record/srw:recordData');
?>
<?php 
foreach ( $records as $cur ) {
  $x = new SimpleXml($cur,NULL,NULL,'http://www.loc.gov/MARC21/slim',FALSE);
	$ns = array('slim'=>'http://www.loc.gov/MARC21/slim');
	$x->regNameSpaces($ns);
?>
<?php 
// get data using xpath-based methods
echo $x->getXpathXml('//slim:record/slim:datafield[@tag="245"]',TRUE);
$topics = $x->getXpathXml('//slim:record/slim:datafield[@tag="650"]/slim:subfield[@code="a"]');
	foreach ( $topics as $topic ) {
		echo $x->getPlainText($topic)."\n";
	}
$links = $x->getXpathXml('//slim:record/slim:datafield[@tag="856"]');
	foreach ( $links as $link ) {
		$l = new SimpleXml($link,NULL,NULL,NULL,FALSE);
		echo $l->getXpathXml('//datafield[@tag="856"]/subfield[@code="u"]',TRUE)."\n";
		echo $l->getXpathXml('//datafield[@tag="856"]/subfield[@code="3"]',TRUE)."\n";
	}
echo $x->getXpathXml('//slim:record/slim:leader',TRUE)."\n";
echo $x->getXpathXml('//slim:record/slim:controlfield[@tag="001"]',TRUE)."\n";
?>
<hr />
<?php } ?>
</pre>
