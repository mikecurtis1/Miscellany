<?php 

class MySql {

  public function __construct($host,$username,$passwd,$db){
    try {
      $this->link = mysql_connect($host,$username,$passwd);
    } catch (Exception $e) {
      return 'Caught exception: ' . $e->getMessage();
    }
    try {
	    mysql_select_db($db,$this->link);
    } catch (Exception $e) {
      return 'Caught exception: ' . $e->getMessage();
    }
    return TRUE;
  }
  
  public function closeDbConnection(){
    try {
      mysql_close($this->link);
	    return TRUE;
    } catch (Exception $e) {
      return 'Caught exception: ' . $e->getMessage();
    }
  }
  
  public function updateData($table,$field,$value,$id,$matchstring){
    $sql = "
      UPDATE `{$table}` SET `{$field}` = '{$value}' WHERE `{$id}` = '{$matchstring}'
    ";
    try {
      if(mysql_query($sql) === TRUE){
	      return TRUE;
      }
    } catch (Exception $e) {
      return 'Caught exception: ' . $e->getMessage();
    }
  }

  public function insertData($input,$table){
    if(is_array($input)){
      $input = array_map('trim', $input);
      $input = array_map('addslashes', $input);
      $keys = '`'.implode('`,`',array_keys($input)).'`';
      $values = '\''.implode('\',\'',$input).'\'';
	    $sql = "
		    INSERT INTO `{$table}` ({$keys}) 
		    VALUES ({$values})
	    ";
	    try {
	      if(mysql_query($sql) === TRUE){
		      return TRUE;
	      }
      } catch (Exception $e) {
        return 'Caught exception: ' . $e->getMessage();
      }
	  } else {
	    return FALSE;
	  }
  }

}

?>
