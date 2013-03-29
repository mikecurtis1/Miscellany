<?php 
  
	function get($k='') {
		if ( isset($_GET[$k]) ) {
			return trim($_GET[$k]);
		} else {
			return '';
		}
	}
	
	function echoHTML($str='',$css=''){
		if($css!==''){
			echo '<span class="'.strtolower($css).'">'.htmlspecialchars($str).'</span>';
			return;
		} else {
			echo htmlspecialchars($str);
			return;
		}
	}
	
	function setURL($key='',$value='', $array=array(), $urlbase='?'){
		$url = '';
		if($key !== '' && $value !== ''){
			$array[$key] = $value;
		}
		if(!empty($array)){
			$url = $urlbase.http_build_query($array,'','&');
		}

		return $url;
	}
	
	function getXMLRecords($xml='',$tag=''){
		preg_match_all("/\<".$tag."\>(.*?)\<\/".$tag."\>/s",$xml,$matches);
		if ( isset($matches[0]) ) {
			return $matches[0];
		} else {
			return array();
		}
	}
	
	function getCoverImg($data=array()){
		$html = '';
		if ( !isset($data[0]['u']) ) {
			return $html;
		} 
		$url = parse_url($data[0]['u']);
		parse_str($url['query'],$query);
		if ( isset($query['GCHGMimg']) ) {
			#$src = 'http://139.127.225.96/gallery/image.php?file='.htmlspecialchars($query['GCHGMimg']);
			$src = 'http://lib.upstate.edu/curtismi/gchgm/marcxml/jpeg/'.htmlspecialchars($query['GCHGMimg']);
			$html = '<img height="220" width="150" src="'.$src.'" alt="'.htmlspecialchars($data[0]['3']).'" />';
		}
		
		return $html;
	}
	
	function getIMDBanchor($data=array(),$link_text=''){
		$html = '';
		if ( $link_text === 'U' && isset($data[0]['u']) ) {
			$text = $data[0]['u'];
		} elseif ( $link_text === '3' && isset($data[0]['3']) ) {
			$text = $data[0]['3'];
		} elseif ( $link_text === '' && isset($data[0]['u']) ) {
			$text = $data[0]['u'];
		} else {
			$text = $link_text;
		}
		if ( isset($data[0]['u']) ) {
			$html = '<a href="'.htmlspecialchars($data[0]['u']).'">'.htmlspecialchars($text).'</a>';
		}
		
		return $html;
	}
	
	function cleanSysNo($str=''){
		return substr($str,-9);
	}
	
	function truncateStr($str='',$len=140){
		if ( strlen($str) > $len ) {
			return substr($str,0,$len).'... ';
		} else {
			return $str;
		}
	}
	
	function getCircItems($host='',$port='',$base='',$bib=''){
		$sys_no = substr($bib,-9);
		$url = 'http://'.$host.':'.$port.'/X?op=circ_status&sys_no='.$sys_no.'&library='.$base;
		$xml = file_get_contents($url);
		preg_match_all("/\<item\-data\>(.*?)\<\/item\-data\>/s",$xml,$items);
		$circ_items = array();
		if ( isset($items[0]) ) {
			foreach ( $items[0] as $i => $item ) {
				preg_match("/\<due\-date\>(.*?)\<\/due\-date\>/s",$item,$due_date);
				if ( isset($due_date[1]) ) {
					$circ_items[$i]['available'] = $due_date[1];
					if ( $due_date[1] === 'Available' ) {
						$circ_items[$i]['is_available'] = TRUE;
					} else {
						$circ_items[$i]['is_available'] = FALSE;
					}
				}
				preg_match("/\<barcode\>(.*?)\<\/barcode\>/s",$item,$barcode);
				if ( isset($barcode[1]) ) {
					$circ_items[$i]['barcode'] = $barcode[1];
				}
				preg_match("/\<location\>(.*?)\<\/location\>/s",$item,$location);
				if ( isset($location[1]) ) {
					$circ_items[$i]['location'] = $location[1];
				}
				preg_match("/\<z30\-description\>(.*?)\<\/z30\-description\>/s",$item,$z30_description);
				if ( isset($z30_description[1]) ) {
					$circ_items[$i]['z30_description'] = $z30_description[1];
				}
			}
		}
		
		return $circ_items;
	}
	
	function getCircStatus($items=array()){
		$available = FALSE;
		foreach ( $items as $item ) {
			if ( $item['is_available'] === TRUE ) {
				$available = TRUE;
				break;
			}
		}
		
		return $available;
	}
?>
