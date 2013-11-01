<?php 

if ( !function_exists('truncate_str') ) {
	function truncate_str($arg=NULL,$length=140,$middle_truncate=FALSE) {
		if ( is_string($arg) ) {
			$truncated = '';
			$arg_length = strlen($arg);
			if ( is_int($length) ) {
				// set min length to allow for an ellipse
				if ( $length < 5 && is_bool($middle_truncate) && $middle_truncate === TRUE ) {
					$length = 5;
				} elseif ($length < 4 ) {
					$length = 4;
				}
				if ( $arg_length > $length ) {
					if ( is_bool($middle_truncate) && $middle_truncate === TRUE ) {
						if ( ($length % 2) === 0 ) { // length is even number
							$begin_length = ($length+1-3)/2;
							$end_length = $begin_length-1;
						} else { // length is odd number
							$begin_length = ($length-3)/2;
							$end_length = $begin_length-1;
							if ( $end_length < 1 ) {
								$end_length = 1;
							}
						}
						$end_length = $end_length * -1; // change the sign
						return substr($arg,0,$begin_length).'...'.substr($arg,$end_length);
					} else {
						return substr($arg,0,($length-3)).'...';
					}
				} else {
					return $arg;
				}
			} else {
				return $arg;
			}
			return $truncated;
		} else {
			return FALSE;
		}
	}
}
?>
