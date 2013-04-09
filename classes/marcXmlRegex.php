<?php 

class marcXmlRegex
{
  public $xml = '';
	private $_controlfield_element_name = '';
	private $_controlfield_attr_name = '';
	private $_datafield_element_name = '';
	private $_datafield_attr_name = '';
	private $_subfield_element_name = '';
	private $_subfield_attr_name = '';
	
	public function __construct($xml='',$mode=1){
		$this->xml = $xml;
		if ( $mode === 1 ) {
			$this->_controlfield_element_name = 'controlfield';
			$this->_controlfield_attr_name = 'tag';
			$this->_datafield_element_name = 'datafield';
			$this->_datafield_attr_name = 'tag';
			$this->_subfield_element_name = 'subfield';
			$this->_subfield_attr_name = 'code';
		} elseif ( $mode === 2 ) {
			$this->_controlfield_element_name = 'fixfield';
			$this->_controlfield_attr_name = 'id';
			$this->_datafield_element_name = 'varfield';
			$this->_datafield_attr_name = 'id';
			$this->_subfield_element_name = 'subfield';
			$this->_subfield_attr_name = 'label';
		}
	}

	public function getMarcControlField($tag=''){
		preg_match("/\<".$this->_controlfield_element_name." ".$this->_controlfield_attr_name."\=\"".$tag."\"\>(.*?)\<\/".$this->_controlfield_element_name."\>/s",$this->xml,$match);
		if ( isset($match[1]) ) {
			return trim($match[1]);
		} else {
			return '';
		}
	}

	private function _getMarcDatafields($tag=''){
		preg_match_all("/\<".$this->_datafield_element_name." ".$this->_datafield_attr_name."\=\"".$tag."\"(.*?)\<\/".$this->_datafield_element_name."\>/s",$this->xml,$matches);
		if ( isset($matches[0]) ) {
			return $matches[0];
		} else {
			return array();
		}
	}

	public function getMarcSubfieldData($tag='',$attrs=''){
		$arr = $this->_getMarcDatafields($tag);
		$str = '';
		if ( isset($arr[0]) ) {
			$str = $arr[0];
		}
		if ( $attrs === '' ) {
			preg_match_all("/\<".$this->_subfield_element_name.".*?\>(.*?)\<\/".$this->_subfield_element_name.".*?\>/s",$str,$matches);
		} elseif ( $attrs !== '' ) {
			preg_match_all("/\<".$this->_subfield_element_name." ".$this->_subfield_attr_name."\=\"[".$attrs."]\"\>(.*?)\<\/".$this->_subfield_element_name."\>/s",$str,$matches);
		}
		if ( isset($matches[1]) ) {
			return trim(implode(' ',$matches[1]));
		} else {
			return '';
		}
	}
	
	public function getMarcSubfieldDataAll($tag='',$attrs='',$haystack='',$needle=''){
		$arr = $this->_getMarcDatafields($tag);
		$data = array();
		$attr_arr = explode('|',$attrs);
		foreach( $arr as $i => $cur ) {
			$temp = array();
			$add = FALSE;
			foreach ( $attr_arr as $attr ) {
				preg_match("/\<".$this->_subfield_element_name." ".$this->_subfield_attr_name."\=\"".$attr."\"\>(.*?)\<\/".$this->_subfield_element_name."\>/s",$cur,$match);
				if ( isset($match[1]) ) {
					if ( ($haystack !== '' && $needle !== '') && ($attr === $haystack) ) {
						if ( preg_match("/".$needle."/i",$match[1],$m) ) {
							$add = TRUE;
						}
					} elseif ($haystack === '' && $needle === '') {
						$add = TRUE;
					}
					$temp[$attr] = trim($match[1]);
				}
			}
			if ( $add === TRUE ) {
				$data[] = $temp;
			}
		}
		return $data;
	}
	
	public function getElementByName($name='',$content_only=TRUE){
		preg_match_all("/\<".$name.".*?\>(.*?)\<\/".$name."\>/s",$this->xml,$matches);
		if ( $content_only === FALSE ) {
			if ( isset($matches[0]) ) {
				return $matches[0];
			}
		} elseif ( $content_only === TRUE ) {
			if ( isset($matches[1]) ) {
				return $matches[1];
			}
		} else {
			return array('');
		}
	}
	
	public function getFirstValue($arr=array()){
		if ( is_array($arr) and !empty($arr) ) {
			return array_shift($arr);
		} else {
			return '';
		}
	}
}
?>
