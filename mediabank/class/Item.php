<?php 

class Item 
{
	
	public $id;
	public $title;
	public $description;
	public $img;
	public $barcode;
	public $available;
	
	public function __construct($id='', $title='', $description='', $img='', $barcode='', $available=''){
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->img = $img;
		$this->barcode = $barcode;
		$this->available = $available;
	}
}
?>
