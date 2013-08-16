<?php 
$data = parse_ini_file('purl.ini', TRUE);
$proxy='http://ezproxy.example.com/login?url=';
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
?>
<!DOCTYPE html>
<html>
<head>
<title>Permanent URL (PURL) Service</title>
<body>
No permanent URL (PURL) resource found for ID = <em><?php echo $id; ?></em><br />
Contact the <a href="mailto:library@example.com">Library</a> for help<br />
</body>
</html>
<?php 
	}
}
?>
