<?php 
// PHP environment and init vars
error_reporting(E_ALL);
ini_set('display_errors', '1');
$exceptions = array();
$om = NULL;
$m = NULL;
$delimiter = '::';
$name = 'Menus';
$root = 'root';
require_once('Ontology.php');
require_once('Collection.php');
require_once('Member.php');
$ini_file = 'individuals.ini';
// create instance of Ontology
$start = microtime(TRUE);
try {
	$om = Ontology::create($name,$root,$delimiter);
} catch (Exception $e) {
	$exceptions[] = $e->getMessage();
}
if ( ! $om instanceof Ontology ) {
	echo "---\n";
	echo var_dump($exceptions);
	die('There is no Ontology instance, cannot continue.');
}
// create and add Members to Ontology
if ( $arr = parse_ini_file($ini_file, TRUE) ) {
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
$end = microtime(TRUE);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<title>Ontology</title>
<link rel="stylesheet" type="text/css" href="main.css" media="screen" />
<link rel="stylesheet" type="text/css" href="dropdown.css" media="screen" />
</head>
<body>
<?php echo "<div>START: {$start} - END:{$end} = ".($end-$start)."</div>\n"; ?>
<h1><a href="http://en.wikipedia.org/wiki/Om#Hinduism">&#x0950;</a></h1>
<ul>
<li><a href="http://en.wikipedia.org/wiki/Ontology_%28information_science%29">Ontology</a> (information_science)</li>
<li><a href="http://www.w3.org/wiki/Lists_of_ontologies">List of ontologies</a></li>
<li><a href="http://en.wikipedia.org/wiki/Taxonomy_%28general%29">Taxonomy</a> (general)</li>
<li><a href="http://en.wikipedia.org/wiki/Hierarchy#Nomenclature">Hierarchy</a> (nomenclature)</li>
<li><a href="http://en.wikipedia.org/wiki/Domain_%28biology%29">Domain</a> (biology)</li>
<li><a href="http://en.wikipedia.org/wiki/Class_%28set_theory%29">Class</a> (set theory)</li>
<li><a href="http://en.wikipedia.org/wiki/Set_%28mathematics%29">Set </a>(mathematics)</li>
<li><a href="http://en.wikipedia.org/wiki/Six_degrees_of_separation">Six degrees of separation</a></li>
</ul>
<h2><?php echo $name; ?></h2>
<br style="clear:both;" />
<?php 
// use HTML display methods of Ontology instance
echo "<hr />\n";
$start = microtime(TRUE);
$html = $om->buildHTMLList();
#$z = $om->getColl()['Animals']->getColl();
#$html = $om->buildHTMLList($z);
$end = microtime(TRUE);
echo "<div>START: {$start} - END:{$end} = ".($end-$start)."</div>\n";
echo "<ul>\n";
echo $html;
echo "</ul>\n";
echo "<br style=\"clear:both;\" />\n";
echo "<hr />\n";
echo "<div style=\"width:100%;background-color:#1360B0\">\n";
echo "<ul class=\"dropdown dropdown-horizontal\">\n";
echo $html;
echo "</ul>\n";
echo "<br style=\"clear:both;\" />\n";
echo "</div>\n";
echo "<div>Hello, world!</div>\n";
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
echo $om->getMemberByKey('M2')."\n";
echo "---\n";
$path = 'Music::Classical::Mozart::Compositions';
echo "MEMBERS of path: ".$path."\n";
$members = $om->getBranchMembers($path);
echo var_dump($members);
echo "---\n";
$z = $om->getColl()['Z Collection'];
print_r($z);
print_r($om);
echo "---\n";
?>
</pre>
</body>
</html>
