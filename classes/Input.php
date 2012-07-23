<?php 

class Input{

  public function __construct($g){
    $this->g = $g;
    $this->query = '';
    $this->start = 1;
    $this->sort = '';
    if(isset($g['query'])){
      $this->query = trim($g['query']);
    }
    if(isset($g['start'])){
      $this->start = $g['start'];
    }
    if(isset($g['sort'])){
      $this->sort = $g['sort'];
    }
  }
  
  public function getValue($v=NULL){
    if(isset($this->$v)){
      return $this->$v;
    }
  }
  
  public function getSortURL($sort=''){
    $url = '';
    if($sort === 'author'){
      $this->g['sort'] = 'author';
      $url = htmlspecialchars('index.php?'.http_build_query($this->g,'','&'));
    }
    if($sort === 'title'){
      $this->g['sort'] = 'title';
      $url = htmlspecialchars('index.php?'.http_build_query($this->g,'','&'));
    }
    
    return $url;
  }
}

?>
