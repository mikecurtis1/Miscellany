<?php 

require_once(dirname(__FILE__).'/../php/classes/HttpRequest.php');
$isbn = HttpRequest::getValue('isbn');
$app = HttpRequest::getValue('app');
$ini_file = dirname(__FILE__).'/ini/'.$app.'.ini';
$ini = parse_ini_file($ini_file);
$cover_zoom = $ini['cover_zoom'];

$isbn = preg_replace("/(\d)\-(\d)/","$1$2", $isbn);
preg_match("/\b(\d{9}[xX\d]|\d{12}[xX\d])\b/",$isbn,$match);
if ( isset($match[1]) ) {
	$isbn = $match[1];
}

$url = 'http://books.google.com/books?vid=ISBN:'.$isbn.'&printsec=frontcover&img=1&zoom='.$cover_zoom;
if ( $content = file_get_contents($url) ) {
	if (strlen($content) > 1269 ) { // Google's 'no image' icon seems to always be this size
		header('Content-type: image/jpeg');
		echo $content;
		exit;
	}
}

// if we don't get a cover from Google, show generic blank cover
$default_url = 'img/cover.png';
header('Content-type: image/png');
echo file_get_contents($default_url);

/*
$id = '';
$thumbnail = '';
$url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:9780143113102';
if ( $content = file_get_contents($url) ) {
	if ( $json = json_decode($content, TRUE) ) {
		if ( isset($json['items'][0]['id']) ) {
			$id = var_dump($json['items'][0]['id']);
		}
		if ( isset($json['items'][0]['volumeInfo']['imageLinks']['thumbnail']) ) {
			$thumbnail = var_dump($json['items'][0]['volumeInfo']['imageLinks']['thumbnail']);
		}
	}
}
*/
?>
