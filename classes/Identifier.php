<?php 

class Identifier 
{
	public $label;
	public $delimiter;
	public $value;
	
	static public function build($label=NULL,$delimiter=NULL,$value=NULL){
		if ( is_string($label) && is_string($delimiter) && is_string($value) ) {
			$obj = new Identifier;
			$obj->label = $label;
			$obj->delimiter = $delimiter;
			$obj->value = $value;
			return $obj;
		} else {
			return FALSE;
		}
	}
}
?>
