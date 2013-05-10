<?php 

class Isbn 
{
  //TODO: is it bad to have two static, public functions in one class???
	static public function first($str='') {
		$temp = preg_replace("/(\d)\-(\d)/","$1$2", $str);
		preg_match("/\b([0-9]{9}[0-9xX]|[0-9]{12}[0-9xX])\b/", $temp, $isbn_match);
		if (isset($isbn_match[1])) {
			return $isbn_match[1]; 
		} else {
			return '';
		}
	}
	
	static public function all($str='') {
		$temp = preg_replace("/(\d)\-(\d)/","$1$2", $str);
		preg_match_all("/\b([0-9]{9}[0-9xX]|[0-9]{12}[0-9xX])\b/", $temp, $isbn_match);
		if (isset($isbn_match[1])) {
			return $isbn_match[1]; 
		} else {
			return array();
		}
	}
}
?>
