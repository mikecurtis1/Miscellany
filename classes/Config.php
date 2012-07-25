<?php 

class Config {

  public function __construct($path=''){
    if(is_file($path)){
      $yaml = yaml_parse(file_get_contents($path));
      foreach($yaml as $property => $value){
        $this->$property = $value;
      }
    }
  }
  
  public function getValue($property=NULL){
    if(isset($this->$property)){
      return $this->$property;
    } else {
      return FALSE;
    }
  }
}

?>
