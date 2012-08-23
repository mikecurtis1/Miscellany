<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once(dirname(__FILE__).'/../classes/QueryParser.php');
$q = new QueryParser();
$tokens = $q->parseQuery($_GET['q']);
?>
<?php header('Content-Type: text/html; charset=utf-8'); ?>
<html>
<head>
<link rel="stylesheet" type="text/css" media="screen" href="main.css" >
</head>
<body>
<form action="index.php" method="get">
<textarea name="q"><?php echo htmlspecialchars($_GET['q']); ?></textarea>
<div><input type="submit" value="submit" /></div>
</form>
<hr />
<pre>
TOKENS: 

<?php print_r($tokens); ?>
</pre>
<hr />
<pre>
QueryParser OBJECT: 

<?php echo var_dump($q); ?>
</pre>
</body>
</html>
