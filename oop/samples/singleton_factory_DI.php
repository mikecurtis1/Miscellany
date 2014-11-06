<?php 

class DataTable
{
	// singleton pattern
	
	private static $_data = array();
	
	protected function __construct(){}
	private function __clone(){}
	private function __wakeup(){}
	
	public static function config($json_path=''){
		self::$_data = json_decode(file_get_contents($json_path),TRUE);
		static $instance = null;
		if ( NULL === $instance ) {
			$instance = new static();
		}
		return $instance;
	}
	
	public static function getDataValue($key){
		if ( isset(self::$_data[$key]) ) {
			return self::$_data[$key];
		} else {
			return FALSE;
		}
	}
	
	public static function isValidKey($key){
		if ( isset(self::$_data[$key]) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

class KEV
{
	// factory pattern, type-hinted dependency injection
	
	private $_key = '';
	private $_value = '';
	
	private function __construct($key,$table){
		$this->_key = $key;
		$this->_value = $table::getDataValue($key);
	}

	public static function create($key='',DataTable $table){
		if ( $table::isValidKey($key) ) {
			return new KEV($key,$table);
		} else {
			return FALSE;
		}
	}
	
	public function getKey(){
		return $this->_key;
	}
	public function getValue(){
		return $this->_value;
	}
}
?>
<pre>
<?php 

$table = DataTable::config('file.json');
echo var_dump($table);
echo var_dump(KEV::create('D',$table));
echo var_dump(KEV::create('3',$table));
echo var_dump(KEV::create('foobar',$table));

$table2 = DataTable::config('file.json'); // does NOT return a second instance
echo var_dump($table2);

/*

JSON

{
    "1": "A",
    "2": "B",
    "3": "C",
    "4": "D",
    "5": "E",
    "6": "F",
    "7": "G",
    "A": 1,
    "B": 2,
    "C": 3,
    "D": 4,
    "E": 5,
    "F": 6,
    "G": 7
}

*/
?>
</pre>
