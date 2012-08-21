<?php 
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../classes/QueryParser.php');
$q = new QueryParser($_GET['q']);
$elements = $q->parseQuery();
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
ELEMENTS: 

<?php print_r($elements); ?>
</pre>
<hr />
<pre>
QueryParser OBJECT: 

<?php echo var_dump($q); ?>
</pre>
</body>
</html>
