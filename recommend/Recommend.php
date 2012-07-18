<?php 

class Recommend {

  public function __construct(){
    $this->similarity_threshold = 0.33;
    $this->number_to_recommend = 5;
  }

  private function _profilesSimilar($p1,$p2){
    $i = array_intersect($p1,$p2);
    $s1 = count($i)/count($p1);
    $s2 = count($i)/count($p2);
    $c1 = count($p1);
    $c2 = count($p2);
    $ci = count($i);
    if($c1 >= $c2){
      $rp = number_format(1-($c2/$c1),2);
      $ri = number_format(($ci/$c2),2);
    } else {
      $rp = number_format(1-($c1/$c2),2);
      $ri = number_format(($ci/$c1),2);
    }
    if($ci > 0){ // at least one intersection
      if($ri >= $rp){
        $f = $ri;
      } else {
        $f = 0;
      }
      $mark = '';
      if($f >= $this->similarity_threshold){
        $mark = ' ! ';
        return TRUE;
      } else {
        return FALSE;
      }
      #echo '|'.sprintf("%04d",$c1).'|'.sprintf("%04d",$c2).'|'.$rp.'|:|'.sprintf("%04d",$ci).'|'.$ri.'|:|'.$mark.$f."\n"; //sprintf("%04d",$c1)
    }
    
    return FALSE;
  }
  
  private function _getArrayDiff($p1,$p2){
    if(is_array($p1) && is_array($p2)){
      return array_slice(array_diff($p2,$p1),0,$this->number_to_recommend); // truncate array diff to the limit
    } else {
      return FALSE;
    }
  }
  
  public function getRecommendations($p1,$p2){
    if(!is_array($p1) || !is_array($p2)){
      return FALSE;
    }
    $similar = $this->_profilesSimilar($p1,$p2);
    if($similar === TRUE){
      $diff = $this->_getArrayDiff($p1,$p2);
      if(count($diff)>0){
        return $diff;
      } else {
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }

}

?>
