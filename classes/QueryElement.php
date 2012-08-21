<?php 
class QueryElement
{
  public $boole;
  public $operator;
  public $index;
  public $text;
  public $phrase;
  
  public function __construct($boole=NULL, $operator=NULL, $index=NULL, $text=NULL, $phrase=FALSE){
    $this->boole = $boole;
    $this->operator = $operator;
    $this->index = $index;
    $this->text = $text;
    $this->phrase = $phrase;
  }
}
?>
