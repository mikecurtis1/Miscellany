<?php 

require_once('Utilities.php');

class Input extends Utilities{

  public function __construct($g=array()){
    $this->query = '';
    $this->start = 1;
    $this->sort = '';
    foreach($g as $i => $v){
      $this->$i = trim($v);
    }
  }
  
  public function getSortKey($keys=array()){
    $value = '';
    if(isset($keys[$this->sort])){
      $value = $keys[$this->sort];
    }
    
    return $value;
  }
}

?>
