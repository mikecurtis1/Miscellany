<?php 

// system path to images
$path = 'C:/Mediabank/Posters/';

// image variables
$file = 'default.jpg';
$image = $path.$file;
$mime_type = 'image/jpeg';

// dynamically set image
if(isset($_GET['file'])){
  $file = $_GET['file'];
  if(file_exists($path.$file)){
    $image = $path.$file;
    //TODO: mime function not available
    #$mime_type = mime_content_type($image);
  }
} 

// serve image
header('Content-type: '.$mime_type);
echo file_get_contents($image);

?>
