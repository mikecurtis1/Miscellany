<pre>
<?php 
// PHP environment
error_reporting(E_ALL);
ini_set('display_errors', '1');
// input
$input = '';
if ( isset($_GET['input']) ) {
	$input = $_GET['input'];
}
// includes
require_once('DbConnection.php');
require_once('DbQuery.php');
// run
$count = 0;
$results = array();
if ( $conn = DbConnection::create('127.0.0.1', 'root', '') ) {
	/*
	$_input = mysql_real_escape_string($input,$conn->getConnection());
	echo var_dump($_input);
	*/
	$db = 'reserves';
	$sql = 'SELECT * FROM FILES';
	if ( $query = DbQuery::query($conn->getConnection(),$db,$sql) ) {
		$count = $query->getCount();
		$results = $query->getResults();
	}
	$conn->closeConnection();
}
echo var_dump($count);
echo var_dump($results);
?>
</pre>
