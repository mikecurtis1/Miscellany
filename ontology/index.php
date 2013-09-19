<?php 
// PHP environment and init vars
error_reporting(E_ALL);
ini_set('display_errors', '1');
$exceptions = array();
$om = NULL;
$m = NULL;
$delimiter = '::';
$name = 'Animals and other stuff';
require_once('Ontology.php');
require_once('Collection.php');
require_once('Member.php');
// create instance of Ontology
try {
	$om = Ontology::create($name,$delimiter);
} catch (Exception $e) {
	$exceptions[] = $e->getMessage();
}
if ( ! $om instanceof Ontology ) {
	echo "---\n";
	echo var_dump($exceptions);
	die('There is no Ontology instance, cannot continue.');
}
// create and add Members to Ontology
if ( $arr = parse_ini_file('individuals.ini', TRUE) ) {
	foreach ( $arr as $key => $i ) {
		if ( isset($i['path']) && isset($i['name'])&& isset($i['uri']) ) {
			try {
				$m = Member::create($i['path'],$key,$i['name'],$i['uri']);
			} catch (Exception $e) {
				$exceptions[] = $e->getMessage();
			}
		}
		if ( isset($i['path']) && isset($i['name'])&& isset($i['alias']) ) {
			try {
				$m = Member::createAlias($i['path'],$key,$i['name'],$i['alias']);
			} catch (Exception $e) {
				$exceptions[] = $e->getMessage();
			}
		}
		if ( isset($m) && isset($i['tags']) ) {
			try {
				$m->setTags($i['tags']);
			} catch (Exception $e) {
				$exceptions[] = $e->getMessage();
			}
		}
		try {
			$om->addMember($m);
		} catch (Exception $e) {
			$exceptions[] = $e->getMessage();
		}
	}
}
// set relations between members
if ( $arr = parse_ini_file('relations.ini', TRUE) ) {
	foreach ( $arr as $key1 => $relations ) {
		foreach ( $relations as $key2 ) {
			$rel = $om->setMemberRelationByKey($key1,$key2);
			$rel = $om->setMemberRelationByKey($key2,$key1); // redundant relations
		}
	}
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<title>Ontology</title>
<link rel="stylesheet" type="text/css" href="main.css" media="screen" />
</head>
<body>
<h1><a href="http://en.wikipedia.org/wiki/Om#Hinduism">&#x0950;</a></h1>
<ul>
<li><a href="http://en.wikipedia.org/wiki/Ontology_%28information_science%29">Ontology</a> (information_science)</li>
<li><a href="http://www.w3.org/wiki/Lists_of_ontologies">List of ontologies</a></li>
<li><a href="http://en.wikipedia.org/wiki/Taxonomy_%28general%29">Taxonomy</a> (general)</li>
<li><a href="http://en.wikipedia.org/wiki/Hierarchy#Nomenclature">Hierarchy</a> (nomenclature)</li>
<li><a href="http://en.wikipedia.org/wiki/Domain_%28biology%29">Domain</a> (biology)</li>
<li><a href="http://en.wikipedia.org/wiki/Class_%28set_theory%29">Class</a> (set theory)</li>
<li><a href="http://en.wikipedia.org/wiki/Set_%28mathematics%29">Set </a>(mathematics)</li>
</ul>
<h2><?php echo $name; ?></h2>
<?php 
// use HTML display methods of Ontology instance
echo "<hr />\n";
$start = microtime();
$html = $om->buildHTMLList(NULL);
$end = microtime();
echo "<div>START: {$start} - END:{$end} = ".($end-$start)."</div>\n";
echo "<div class=\"menu\">\n";
echo $html;
echo "</div>\n";
echo "<hr />\n";
?>
<pre>
<?php 
// dump out object data, exceptions, etc.
echo "---\n";
echo "EXCEPTIONS: \n";
echo var_dump($exceptions);
echo "---\n";
echo 'URI for KEY:X2:'.$om->getMemberUriByKey('X2')."\n";
echo "---\n";
$path = 'Music::Classical';
echo "MEMBERS of path: ".$path."\n";
$members = $om->getBranchMembers($path);
echo var_dump($members);
echo "---\n";
print_r($om);
echo "---\n";
?>
</pre>
</body>
</html>
