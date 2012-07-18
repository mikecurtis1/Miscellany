<pre>
<?php 
$t1 = time();
echo 'START:'.date("H:i:s")."\n"; 
?>
<?php 

//settings
error_reporting(E_ALL);
set_time_limit(3600);

//make instance
require_once('Recommend.php');
$recommend = new Recommend;

#$csvfile = 'BX-CSV-Dump/BX-Book-Ratings.csv';
$csvfile = 'bx.csv';
$p = array();
$temp = file($csvfile);
foreach($temp as $row){
  list($person,$id,$rate) = explode(';',$row);
  $person = str_replace('"','',$person);
  $id = str_replace('"','',$id);
  $rate = str_replace('"','',$rate);
  if($rate >= 5){
    #$p[$person][$id] = trim($rate);
    #$url = 'http://search.idsproject.org/index.php?oclc_symbol=VYQ&view=ids&query_value[0]='.trim($id).'&query_index[0]=bn&search_scope=worldcat';
    $url = 'http://search.idsproject.org/index.php?view=ids_holdings&oclc_symbol=VYQ&number_type=isbn&number='.trim($id);
    $img = '<img src="http://covers.librarything.com/devkey/KEY/medium/isbn/'.trim($id).'">';
    $link = '<div style="border:1px solid #666;height:200px;width:150px;float:left;"><a href="'.$url.'">'.$img.'<br />'.trim($id).'</a></div>';
    #$p[$person][] = trim($id);
    $p[$person][] = $link;
  }
}

$profiles_size = count($p);

//output of object
echo var_dump($recommend);

//procedure
$cc = 0;
$r = 0;
$output = '';
$p_max = count($p);
$limit = 10000;
$p_start = mt_rand(1,$p_max)-$limit;
if($p_start < 1){
  $p_start = 1;
}
$p_userid = mt_rand(1,$p_max);
if($p_userid > $p_max){
  $p_userid = $p_max;
}
#$p_stop = ;
$p_user = array_slice($p,$p_userid,1); // run comparison only on a slice for save time in testing
$p_comp = array_slice($p,$p_start,$limit); // run comparison only on a slice for save time in testing
echo "|p_userid:{$p_userid}|p_max:{$p_max}|p_start:{$p_start}|limit:{$limit}|";
#exit;
foreach($p_user as $i1 => $p1){
  $output .= "<div style=\"background-color:#bbd;\">";
  $output .= "USER:\n<br style=\"clear:both\" />";
  $output .= implode('',$p1)."<br style=\"clear:both\" />";
  $output .= "</div>";
  foreach($p_comp as $i2 => $p2){
    if($i1 != $i2){ // don't compare with self
      $recommendations = $recommend->getRecommendations($p1,$p2);
      if($recommendations !== FALSE){
        $output .= "<div style=\"background-color:#9d9;\">";
        $output .= "<span style=\"font-size:200%;font-weight:bold;\">RECOMMENDATIONS = ".$i1.':'.$i2.':'."</span><br style=\"clear:both\" />";
        $output .= implode('',$recommendations)."<br style=\"clear:both\" />";
        $output .= "</div>";
        $output .= "<div>";
        $output .= "INTERSECT = \n<br style=\"clear:both\" />";
        $output .= implode('',array_intersect($p1,$p2))."<br style=\"clear:both\" />";
        $output .= "</div>";
        $output .= "<div style=\"background-color:#dd6;\">";
        $output .= "COMPARED = \n<br style=\"clear:both\" />";
        $output .= implode('',$p2)."<br style=\"clear:both\" />";
        $output .= "</div>";
        $output .= "<br style=\"clear:both\" />";
        $output .= "<hr style=\"clear:both\" />\n";
        $output .= "<br style=\"clear:both\" />";
        $r++;
      }
    }
    $cc++;
  }
}
?>
<?php 
$t2 = time();
echo 'STOP:'.date("H:i:s")."\n"; 
echo 'ELAPSED TIME (min) = '.(($t2 - $t1)/60).'. (sec) = '.($t2 - $t1)."\n";
echo 'PROFILES = '.$profiles_size."\n";
echo 'NUM OF COMPARISONS = '.$cc."\n";
echo 'NUM OF RECOMMENDATION SETS = '.$r."\n";
echo 'RECOMMENDATION TO COMPS. = '.($r/$cc)."\n";
echo 'RECOMMENDATION TO PROFILES = '.($r/$profiles_size)."\n";
echo $output;
#print_r($p);
?>
</pre>
