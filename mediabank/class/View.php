<?php 

/**
 * A view requests from the model the information that it needs to generate an output representation.
 */
 
class View 
{
	
	public function __construct(){
		$this->items = array();
		$this->new_url = '';
		$this->start_url = '';
		$this->back_url = '';
		$this->next_url = '';
		$this->last_url = '';
	}

	public function markupItems($data){
		foreach ($data as $item) {
			$this->items[] = $this->_markupItem($item)."\n";
		}
		
		return $this->items;
	}
	
	public function setNavURLs($search,$modificamacchina,$back,$next){
		$searchurl = '&search='.urlencode($search);
		$modificamacchinaurl = '&modificamacchina='.urlencode($modificamacchina);
		$this->new_url = 'index.php?skip=0'.$modificamacchinaurl;
		$this->start_url = 'index.php?skip=0'.$modificamacchinaurl.$searchurl;
		$this->back_url = 'index.php?skip='.$back.$modificamacchinaurl.$searchurl;
		$this->next_url = 'index.php?skip='.$next.$modificamacchinaurl.$searchurl;
		$this->last_url = 'index.php?skip='.$back.$modificamacchinaurl.$searchurl;
	}

	private function _markupItem($item){
		$markup = '';
		if ($item->available == 'S') {
			$available = '<div class="available">available</div>';
		} else {
			$available = '<div class="checkedout">checked out</div>';
		}
		$url = 'http://139.127.225.96/items.php?id='.$item->id;
		$src = 'image.php?file='.$item->img.'.jpg';
		$markup = '<div class="item">';
		$markup .= '<div class="title">'.htmlspecialchars($item->title).' ('.htmlspecialchars($item->barcode).')'.'</div>';
		$markup .= $available;
		$markup .= '<div class="poster">';
		$markup .= '<img src="'.$src.'" height="220" width="150" alt="movie poster" />';
		$markup .= '</div>';
		$markup .= '<div class="descrip">'.htmlspecialchars($item->description).'</div>';
		$markup .= '</div>';
		
		return $markup;
	}
}
?>
