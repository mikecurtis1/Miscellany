<?php 

/**
 * A model notifies its associated views and controllers when 
 * there has been a change in its state. This notification allows 
 * the views to produce updated output, and the controllers to 
 * change the available set of commands. A passive implementation 
 * of MVC omits these notifications, because the application does 
 * not require them or the software platform does not support them.
 */
 
include_once('Item.php');
 
class Model{
	
	public function __construct($host,$username,$password){
		$this->host = $host;
		$this->user = $username;
		$this->password = $password;
		$this->search = '';
		$this->modificamacchina = '';
		$this->first = 1;
		$this->skip = 0;
		$this->sql = '';
	}
	
	public function requestData($first,$skip,$search,$modificamacchina){
		$this->search = $search;
		$this->modificamacchina = $modificamacchina;
		$this->first = $first;
		$this->skip = $skip;
		$db = ibase_connect($this->host, $this->user, $this->password);
		if(!$db){
			//HACK: need better error handling
			$error = 'Error connecting to the Database Server';
			return $error;
		}
		$this->_buildSQL();
		$result = ibase_query($db,$this->sql);
		if(!$result){
			//HACK: need better error handling
			$error = 'Error executing query';
			return $error;
		}
		$data = $this->_setItems($result);
		ibase_free_result($result);
		ibase_close($db);
		
		return $data;
	}
	
	private function _buildSQL(){
		/** modificamacchina = RVM000, VMn000 
		  * S=available, #=reserved, N=unavailable
		  * AND "MEDIA"."DISPO" = \'S\' 
		  */
		$where_clauses = array();
		if($this->modificamacchina != ''){
			$temp = explode(',',$this->modificamacchina);
			$where_clauses[] ='("MEDIA"."MODIFICAMACCHINA" = \''.implode('\' OR "MEDIA"."MODIFICAMACCHINA" = \'',$temp).'\')';
		}
		if($this->search != ''){
			$where_clauses[] = "(UPPER(\"TITOLI\".\"TITOLO\") LIKE UPPER('%".$this->search."%') OR UPPER(SUBSTRING(\"TITOLI\".\"DESCRIZ\" FROM 1 FOR 16000)) LIKE UPPER('%".$this->search."%')) ";
		}
		if(count($where_clauses)>0){
			$where_clause = 'WHERE '.implode(' AND ',$where_clauses);
		}
		$this->sql = '
			SELECT FIRST '.$this->first.' SKIP '.$this->skip.' * 
			FROM "MEDIA" 
			LEFT JOIN "TITOLI" ON "MEDIA"."IDTITOLO" = "TITOLI"."IDTITOLO"
			'.$where_clause.' 
			ORDER BY UPPER("TITOLI"."TITOLO")';
		
		return;
	}
	
	private function _setItems($result=NULL){
		$temp = array();
		while($row = ibase_fetch_row($result, IBASE_TEXT)){
			$this->item = new Item();
			$this->item->id = trim($row[0]);
			$this->item->title = trim($row[34]);
			$this->item->description = trim(str_replace("\r\n", ' ', $row[50]));
			$this->item->img = trim($row[52]);
			$this->item->barcode = trim($row[22]);
			if($this->item->barcode == ''){
				$this->item->barcode = 'ID:'.$item['id'];
			}
			$this->item->available = trim($row[5]);
			$temp[] = $this->item;
		}
		
		return $temp;
	}
}
?>
