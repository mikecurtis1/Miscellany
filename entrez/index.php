<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('Entrez.php');
$e = new Entrez('pubmed');
$term = '';
if ( isset($_GET['term']) ) {
  $term = $_GET['term'];
}
$hits = $e->search($term);
$terms = $e->getTranslationStack();
#print_r($e->getResults());
?>
<html>
<head>
<link rel="stylesheet" href="entrez.css" media="screen">
</head>
<body>
<form action="" method="get">
<div>
<label>PubMed:</label><input type="text" name="term" value="<?php echo htmlspecialchars($term); ?>" /></div>
<input type="submit" value="Search" />
</div>
</form>
<div class="search_data">
<div>HITS: <?php echo number_format($hits); ?></div>
<div>
<?php 
foreach ( $terms as $term => $count ) {
	echo "<li><a href=\"?term=".urlencode($term)."\">".$term."</a> [".$count." hits]</li>\n";
}
?>
</div>
</div>
<?php foreach ( $e->getResults() as $i => $r ) {
	echo "<div class=\"result\">\n";
	displayHeading($r,'title');
	displayAuthors($r);
	displayField($r,'description');
	displayField($r,'journal');
	displayISSN($r);
	displayField($r,'volume');
	displayField($r,'issue');
	displayField($r,'pubyear');
	displayField($r,'month');
	displayField($r,'pagination');
	displayPMID($r);
	displayField($r,'doi');
	echo "</div>\n";
}
?>
</body>
</html>
<?php 
function displayArray($r=array(),$key=''){
	if ( isset($r[$key]) & !empty($r[$key]) ) {
		echo "<div class=\"field\">".strtoupper($key).": </div>\n";
		foreach ( $r[$key] as $i => $value ) {
			echo "<div class=\"value\">".$value."</div>\n";
		}
	}
}

function displayField($r=array(),$key=''){
	if ( isset($r[$key]) & !empty($r[$key]) ) {
		echo "<div class=\"field\">".strtoupper($key).": </div><div class=\"value\">".$r[$key]."</div>\n";
	}
}

function displayHeading($r=array(),$key='',$h='1'){
	if ( isset($r[$key]) & !empty($r[$key]) ) {
		echo "<h".$h." class=\"result\">".$r[$key]."</h".$h.">\n";
	}
}

function displayISSN($r=array()){
	if ( isset($r['issn']) & !empty($r['issn']) ) {
		$anchor = "<a href=\"?term=".urlencode($r['issn'])."[ISSN]\">".$r['issn']."</a>";
		echo "<div class=\"field\">ISSN: </div><div class=\"value\">".$anchor."</div>\n";
	}
}

function displayAuthors($r=array()){
	if ( isset($r['authors']) & !empty($r['authors']) ) {
		echo "<div class=\"field\">AUTHORS: </div>\n";
		foreach ( $r['authors'] as $i => $author ) {
			$anchor = "<a href=\"?term=".urlencode($author)."[author]\">".$author."</a>";
			echo "<div class=\"value\">".$anchor."</div>\n";
		}
	}
}

function displayPMID($r=array()){
	if ( isset($r['pmid']) & !empty($r['pmid']) ) {
		#$pmidurl = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id='.$r['pmid'].'&retmode=xml';
		$pmidurl = 'http://www.ncbi.nlm.nih.gov/pubmed/?term='.$r['pmid'].'[pmid]&otool=sunyumlib';
		$anchor = '<a href="'.$pmidurl.'" target=\"_blank\">'.$r['pmid'].'</a>';
		echo "<div class=\"field\">PMID: </div><div class=\"value\">".$anchor."</div>\n";
	}
}
?>
