<?php 

class Input{

  public function __construct($g){
    $this->query = '';
    $this->start = 1;
    $this->c1 = '';
    $this->o1 = '';
    $this->c2 = '';
    $this->o2 = '';
    if(isset($g['query'])){
      $this->query = trim($g['query']);
    }
    if(isset($g['start'])){
      $this->start = $g['start'];
    }
    if(isset($g['c1'])){
      $this->c1 = $g['c1'];
    }
    if(isset($g['o1'])){
      $this->o1 = $g['o1'];
    }
    if(isset($g['c2'])){
      $this->c2 = $g['c2'];
    }
    if(isset($g['o2'])){
      $this->o2 = $g['o2'];
    }    
  }
  
  public function getValue($v=NULL){
    if(isset($this->$v)){
      return $this->$v;
    }
  }
}

?>
