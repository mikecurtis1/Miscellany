<?php 
class QueryElement
{
  public $prefix;
  public $index;
  public $text;
  public $phrase;
  
  public function __construct($prefix=NULL, $index=NULL, $text=NULL, $phrase=FALSE){
    $this->prefix = $prefix;
    $this->index = $index;
    $this->text = $text;
    $this->phrase = $phrase;
  }
}
?>
