<?php 

/*

https://developers.google.com/custom-search/v1/using_rest
https://developers.google.com/custom-search/v1/using_rest#key
https://code.google.com/apis/console

*/

if ( isset($_GET['q']) ) {
  $q = $_GET['q'];
} else {
	$q = '';
}

$key = 'INSERT-YOUR-KEY';
$cx = '013036536707430787589:_pqjad5hr1a';
$url = 'https://www.googleapis.com/customsearch/v1?key='.$key.'&cx='.$cx.'&q='.urlencode($q).'&alt=json';

$json = file_get_contents($url);
#$json = json_encode(array('url'=>$url));
#header('Content-Type: application/json; charset=utf-8'); 
#echo $json;

$data = json_decode($json, TRUE);

ob_start();
foreach ( $data['items'] as $i => $item ) {
	$title = $item['htmlTitle'];
	$uri = $item['link'];
	$description = $item['htmlSnippet'];
	$description = str_replace('<br>','',$description);
?>
<div class="record">
<div class="title"><a href="<?php echo $uri;?>"><?php echo $title;?></a></div>
<div class="description"><?php echo $description;?></div>
</div>
<?php 
}
$content = ob_get_contents();
ob_end_clean();
?>
<!DOCTYPE html>
<html>
<head>
<title>Google Custom Search</title>
<link rel="stylesheet" type="text/css" href="cse.css" media="screen" />
</head>
<body>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
<div class="records">
<form action="" method="get">
<input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" />
<!--<input type="submit" value="&raquo;search&raquo;" />-->
<input type="submit" value="&raquo;" />
<div class="powered_by_google">
<img src="http://www.google.com/images/poweredby_transparent/poweredby_FFFFFF.gif" alt="Powered by Google" />
</div>
</form>
<?php echo $content; ?>
</div>
<p>But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?</p>
</body>
</html>
