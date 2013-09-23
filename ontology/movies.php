<?php 
// PHP environment and init vars
error_reporting(E_ALL);
ini_set('display_errors', '1');
// init error reporting array
$exceptions = array();
// init vars for tag reading
$path_delimiter = '::';
require_once('TagReader.php');
$rank_limit = 6; // rank_limit -1 is also the max levels of nesting
$uncategorized_label = 'Un-Categorized';
$tag_sets = array();
$tag_synonyms = array();
$rows = file('movies.tsv');
$header_row = array_shift($rows);
$films = array();
$film_actors = array();
// get film data and make tag sets from actor names
$start = microtime(TRUE);
foreach ( $rows as $row ) {
	$fields = explode("\t",trim($row));
	$actors = explode('|',$fields[6]);
	$actor_set = '';
	$temp_actor_set = array();
	foreach ( $actors as $actor ) {
		$actor_data = explode('::',$actor);
		$actor_code = $actor_data[0];
		$actor_name = $actor_data[1];
		$temp_actor_set[] = $actor_name;
		$film_actors[$actor_name] = array('code'=>$actor_code,'name'=>$actor_name);
	}
	$actor_set = implode(',',$temp_actor_set);
	$films[$fields[0]] = array('title'=>$fields[1],'actor_set'=>$actor_set);
	$tag_sets[$fields[0]] = $actor_set;
}
// create TagReader instance and convert tags to paths
try {
	$t = new TagReader($path_delimiter,$tag_sets,$tag_synonyms,$rank_limit,$uncategorized_label);
} catch (Exception $e ) {
	$exceptions[] = $e->getMessage();
}
if ( ! empty($exceptions) ) {
	print_r($exceptions);
	die('Exceptions must be resolved before the program can continue running.');
}
// init Ontology vars
$om = NULL;
$m = NULL;
$delimiter = '::';
$sys_name = 'Movies';
$root = 'root';
require_once('Ontology.php');
require_once('Collection.php');
require_once('Member.php');
// create instance of Ontology
try {
	$om = Ontology::create($sys_name,$root,$delimiter);
} catch (Exception $e) {
	$exceptions[] = $e->getMessage();
}
if ( ! $om instanceof Ontology ) {
	echo "---\n";
	echo var_dump($exceptions);
	die('There is no Ontology instance, cannot continue.');
}
// create and add Members to Ontology
foreach ( $t->getTagPaths() as $k => $path ) {
	try {
		$key = 'id'.$k;
		$name = $films[$k]['title'];
		$uri = 'http://www.imdb.com/find?q='.urlencode($films[$k]['title']).'&s=tt';
		$m = Member::create($path,$key,$name,$uri);
	} catch (Exception $e) {
		$exceptions[] = $e->getMessage();
	}
	try {
		$om->addMember($m);
	} catch (Exception $e) {
		$exceptions[] = $e->getMessage();
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
<h1><?php echo $sys_name; ?></h1>
<br style="clear:both;" />
<?php 
// use HTML display methods of Ontology instance
echo "<hr />\n";
$start = microtime(TRUE);
$html = $om->buildHTMLList();
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
echo "<hr />\n";
?>
<pre>
<?php 
// dump out object data, exceptions, etc.
echo "---\n";
echo "EXCEPTIONS: \n";
echo var_dump($exceptions);
echo "---\n";
print_r($om);
echo "---\n";
?>
</pre>
</body>
</html>
