<?php 
$data = parse_ini_file('purl.ini', TRUE);
$proxy='http://ezproxy.example.com/login?url=';
$error_page_path = 'error.php';
if ( isset($_GET['id']) ) {
	$id = $_GET['id'];
	if ( isset($data[$id]['link_text']) && isset($data[$id]['url']) ) {
		$link_text = $data[$id]['link_text'];
		$url = $data[$id]['url'];
		$proxy_required = $data[$id]['proxy'];
		if ( $proxy_required === '0' ) {
			$location = $proxy.$url;
		} elseif ( $proxy_required === '1' ) {
			$location = $url;
		} else { 
			$location = $url;
		}
		header('Location: '.$location);
	} else {
		header("HTTP/1.0 404 Not Found");
		include($error_page_path);
		exit;
	}
}
?>
