<?php 

class Input{

  public function __construct($g){
    $this->search = '';
    $this->start = 1;
    if(isset($g['search'])){
      $this->search = trim($g['search']);
    }
    if(isset($g['start'])){
      $this->start = $g['start'];
    }
  }
}

?>
