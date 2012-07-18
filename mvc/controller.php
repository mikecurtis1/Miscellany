<?php 

class Input{

	private static $_instance;
	private $inputs = array();

	private function __construct(){
	}

	public static function singleton(){
		if(!isset(self::$_instance)){
			$className = __CLASS__;
			self::$_instance = new $className;
		}
		
		return self::$_instance;
	}
	
	public function setValues($array=array()){
		foreach($array as $k => $v){
			if(!is_array($v)){
				$this->inputs[$k] = trim($v);
			}
		}
	}
	
	public function getValue($key){
		if(isset($this->inputs[$key])){
			return $this->inputs[$key];
		} else {
			return '';
		}
	}

}

?>
