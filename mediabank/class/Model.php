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
		$this->query = '';
		$this->data = array();
	}
	
	public function requestData($first,$skip,$search,$modificamacchina){
	
		$db = ibase_connect($this->host, $this->user, $this->password);
		if(!$db){
			//HACK: need better error handling
			$data = 'Error connecting to the Database Server';
			return $data;
		}

		/* RVM000, VMn000
		   S=available, #=reserved, N=unavailable
		   AND "MEDIA"."DISPO" = \'S\' 
		   */
		
		$where_clauses = array();
		if($modificamacchina != ''){
			$temp = explode(',',$modificamacchina);
			$where_clauses[] ='("MEDIA"."MODIFICAMACCHINA" = \''.implode('\' OR "MEDIA"."MODIFICAMACCHINA" = \'',$temp).'\')';
		}
		if($search != ''){
			$where_clauses[] = "(UPPER(\"TITOLI\".\"TITOLO\") LIKE UPPER('%".$search."%') OR UPPER(SUBSTRING(\"TITOLI\".\"DESCRIZ\" FROM 1 FOR 16000)) LIKE UPPER('%".$search."%')) ";
		}
		if(count($where_clauses)>0){
			$where_clause = 'WHERE '.implode(' AND ',$where_clauses);
		}
		
		$this->query = '
			SELECT FIRST '.$first.' SKIP '.$skip.' * 
			FROM "MEDIA" 
			LEFT JOIN "TITOLI" ON "MEDIA"."IDTITOLO" = "TITOLI"."IDTITOLO"
			'.$where_clause.' 
			ORDER BY UPPER("TITOLI"."TITOLO")
			';

		$result = ibase_query($db,$this->query);
		if(!$result){
			//HACK: need better error handling
			$data = 'Error executing query';
			return $data;
		}

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
			$this->data[] = $this->item;
		}

		ibase_free_result($result);
		ibase_close($db);
		
		return $this->data;
	}
}
?>
