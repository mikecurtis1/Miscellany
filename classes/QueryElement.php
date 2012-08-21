<?php 
class QueryElement
{
  public $operator;
  public $index;
  public $text;
  public $phrase;
  
  public function __construct($operator=NULL, $index=NULL, $text=NULL, $phrase=FALSE){
    $this->operator = $operator;
    $this->index = $index;
    $this->text = $text;
    $this->phrase = $phrase;
  }
}
?>
