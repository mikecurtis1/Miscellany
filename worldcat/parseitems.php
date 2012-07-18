<pre>
<?php 

// PHP settings
set_time_limit(300);
error_reporting(E_ALL);

// create objects
require_once(dirname(__FILE__).'/../ConfigPrivate.php');
$cfg_private = new ConfigPrivate();
require_once('MySql.php');
require_once('IdsMySql.php');
$idsmysql = new IdsMySql($cfg_private->sql_host,$cfg_private->sql_user,$cfg_private->sql_passwd,'illiad');
require_once('Items.php');
$items = new Items();

// procedure
echo 'START:'.date("H:i:s")."\n";
echo var_dump($idsmysql);

$path = '/media/sf_Documents/xml';
$files = scandir($path);
foreach($files as $filename){
  if(is_file($path.'/'.$filename)){
    if($tempxml = file($path.'/'.$filename)){
      $xml = implode('',array_map('trim', $tempxml));
      $item = $items->parseItem($xml);
      if(is_array($item) && !empty($item)){
        $array = array(
          'item_id' => substr($filename,0,-4), 
          'message' => $item['message'], 
          'marc100a' => $item['author'], 
          'marc245' => $item['title'], 
          'marc938c' => $item['price'], 
          'marc650v' => $item['form'], 
          'marc650' => $item['topics'], 
          'marc250a' => $item['edition'], 
          'marc020a' => $item['isbns'], 
          'marc260b' => $item['publisher'], 
          'marc260c' => $item['pubdate'], 
          'xml' => ''
        );
        $idsmysql->insertData($array,'items');
      } else {
        echo "ITEM EMPTY: {$i} : {$file}\n";
      }
    } else {
      echo "FAILED TO OPEN FILE: {$i} : {$file}\n";
    }
  }
}

$idsmysql->closeDbConnection();

echo 'END:'.date("H:i:s")."\n";

?>
</pre>
