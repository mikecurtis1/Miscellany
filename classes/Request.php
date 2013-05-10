<?php

class Request 
{
  static public function value($key) {
		if ( isset($_GET[$key]) ) {
			return trim($_GET[$key]);
		} elseif ( isset($_POST[$key]) ) {
			return trim($_POST[$key]);
		} else {
			return '';
		}
	}
}
?>
