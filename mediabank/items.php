<?php 

/**
 * A controller can send commands to its associated view 
 * to change the view's presentation of the model (for 
 * example, by scrolling through a document). It can 
 * send commands to the model to update the model's 
 * state (e.g. editing a document).
 */
 
class Items{

	public function __construct($mb_host='',$mb_user='',$mb_password=''){
		$this->host = $mb_host;
		$this->user = $mb_user;
		$this->password = $mb_password;
		$this->query = '';
		$this->items = array();
		$this->display = '';
	}
	
	public function setItems($state){
	
		$first = $state->first;
		$skip = $state->skip;
		$search = $state->search;
		$modificamacchina = $state->modificamacchina;

		//TODO: check for ibase
		$db = ibase_connect($this->host, $this->user, $this->password);
		if(!$db){
			//HACK: need better error handling
			$items = 'Error connecting to the Database Server';
			return $items;
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
			$items = 'Error executing query';
			return $items;
		}

		while($row = ibase_fetch_row($result, IBASE_TEXT)){
			$item = array('id'=>'','title'=>'','descrip'=>'','img'=>'','barcode'=>'','available'=>'');
			$item['id'] = trim($row[0]);
			$item['title'] = trim($row[34]);
			$item['descrip'] = trim(str_replace("\r\n", ' ', $row[50]));
			$item['img'] = trim($row[52]);
			$item['barcode'] = trim($row[22]);
			if($item['barcode'] == ''){
				$item['barcode'] = 'ID:'.$item['id'];
			}
			$item['available'] = trim($row[5]);
			$this->items[] = $this->_markupItem($item);
		}

		ibase_free_result($result);
		ibase_close($db);
		
		return $this->items;
	}

	private function _markupItem($item){
		$markup = '';
		if($item['available'] == 'S'){
			$available = '<div class="available">available</div>';
		} else {
			$available = '<div class="checkedout">checked out</div>';
		}
		$url = 'http://'.$this->mb_ip.'/items.php?id='.$item['id'];
		$src = 'image.php?file='.$item['img'].'.jpg';
		$markup = '<div class="item">';
		$markup .= '<div class="title">'.$item['title'].' ('.$item['barcode'].')'.'</div>';
		$markup .= $available;
		$markup .= '<div class="poster">';
		$markup .= '<img src="'.$src.'" height="220" width="150" alt="movie poster" />';
		$markup .= '</div>';
		$markup .= '<div class="descrip">'.$item['descrip'].'</div>';
		$markup .= '</div>';
		
		return $markup;
	}
	
	public function display(){
		echo implode('',$this->items)."\n";
		
		return;
	}
}

?>
