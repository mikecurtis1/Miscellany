<?php 
class Profiles
{
	private $_cfg;
	private $_path;
	private $_dir;
	private $_get;
	
	public function __construct($path=''){
		$this->_cfg = array();
		$this->_path = $path;
		$this->_dir = scandir($this->_path);
		$this->_loadConfigs();
	}
	
	// model type methods
	private function _loadConfigs(){
		foreach ( $this->_dir as $f ) {
			if ( substr($f,-4) === '.yml') {
				$fullpath = $this->_path.'/'.$f;
				$yaml = yaml_parse(file_get_contents($fullpath));
				$yaml['config_file_path'] = $fullpath;
				$this->_cfg[] = $yaml;
			}
		}
		
		return;
	}
	
	public function getValue($i=NULL,$keys=NULL){
		$temp = $this->_cfg[$i];
		$key_array = explode('|',$keys);
		$value = NULL;
		foreach ( $key_array as $k ) {
			$k = trim($k);
			if ( isset($temp[$k]) ) {
				if ( is_array($temp[$k]) ) {
					$temp = $temp[$k];
				} else {
					$value = $temp[$k];
					break;
				}
			}
		}
		if ( is_string($value) ) {
			$value = trim($value);
		} 
		
		return $value;
	}
	
	private function _checkArrayIsset($i=NULL,$keys=NULL){
		$temp = $this->_cfg[$i];
		$key_array = explode('|',$keys);
		$set = FALSE;
		foreach ( $key_array as $k ) {
			$k = trim($k);
			if ( isset($temp[$k]) ) {
				$set = TRUE;
				if ( is_array($temp[$k]) ) {
					$temp = $temp[$k];
				}
			} else {
				$set = FALSE;
				break;
			}
		}
		
		return $set;
	}
	
	public function filterCfg($keys=NULL,$v1=NULL,$include=TRUE){
		$match = FALSE;
		foreach ( $this->_cfg as $i => $v ) {
			if ( $v1 === 'ISSET' ) {
				$match = $this->_checkArrayIsset($i,$keys);
			} else {
				$v2 = $this->getValue($i,$keys);
				$match = $this->_isMatch($v1,$v2);
			}
			if ( $match === TRUE && $include === FALSE ) {
				unset($this->_cfg[$i]);
			} elseif ( $match === FALSE && $include === TRUE ) {
				unset($this->_cfg[$i]);
			} 
		}
		
		return;
	}
	
	private function _isMatch($v1=NULL,$v2=NULL){
		$match = FALSE;
		if ( is_string($v1) && is_string($v2) ) {
			if ( preg_match("/".$v1."/i",$v2) ) {
				$match = TRUE;
			}
		} else {
			if ( $v1 === $v2 ) {
				$match = TRUE;
			}
		}
		
		return $match;
	}
	
	private function _getCfgById($id=NULL){
		$array = array();
		foreach ( $this->_cfg as $c ) {
			if ( isset($c['oclc_symbol']) && strtoupper($c['oclc_symbol']) === strtoupper($id) ) {
				$array = $c;
				break;
			}
		}
		
		return $array;
	}
	
	public function filterCfgById($ids=NULL,$include=TRUE){
		$id_array = explode(',',$ids);
		if ( $include === TRUE ) {
			$temp_cfg = array();
			foreach ( $id_array as $id ) {
				$temp_cfg[] = $this->_getCfgById($id);
			}
			$this->_cfg = $temp_cfg;
		}
		if ( $include === FALSE ) {
			foreach ( $id_array as $id ) {
				foreach ( $this->_cfg as $i => $c ) {
					if ( isset($c['oclc_symbol']) && strtoupper($c['oclc_symbol']) === strtoupper($id) ) {
						unset($this->_cfg[$i]);
					}
				}
			}
		}
		
		return;
	}
	
	private function _recastString($string=''){
		if ( $string === 'NULL' ) { 
			return NULL;
		} elseif ( $string === 'TRUE' ) {
			return TRUE;
		} elseif ( $string === 'FALSE' ) {
			return FALSE;
		} elseif ( is_numeric($string) ) {
			return intval($string);
		} else {
			return $string;
		}
	}
	
	// controller type methods
	public function applyFilters($g=array()){
		if ( isset($g['ids']) && $g['ids'] !== '') {
		$temp = explode(';',$g['ids']);
		$id_list = trim($temp[0]);
		if ( isset($temp[1]) ) {
			$include = $this->_recastString(trim($temp[1]));
		} else {
			$include = TRUE;
		}
		$this->filterCfgById($id_list,$include);
		} elseif ( isset($g['filters']) ) {
			foreach ( $g['filters'] as $i => $f ) {
				if ( $f !== '' ) {
					$temp = explode(';',$f);
					if ( count($temp) === 3 ) {
						$keys = trim($temp[0]);
						$value = $this->_recastString(trim($temp[1]));
						$include = $this->_recastString(trim($temp[2]));
					}
					$this->filterCfg($keys,$value,$include);
				}
			}
		}
		
		return $this->_cfg;
	}
	
	public function setFormOptions($g=array()){
		$options = array();
		if ( isset($g['options']) ) {
			foreach ( $g['options'] as $option ) {
				$options[$option] = $option;
			}
		}
		
		return $options;
	}
	
	public function getCount(){
		return count($this->_cfg);
	}
	
	//TODO: some function to get form values from GET array
	/*public function getFormValue($key=NULL,$index=NULL){
		$value = '';
		
		return $value;
	}*/
	
	public function getHTMLValue($i=NULL,$keys=NULL){
		$string = '';
		$temp = $this->getValue($i,$keys);
		if ( $temp === TRUE ) {
			$string = 'TRUE';
		} elseif ( $temp === FALSE ) {
			$string = 'FALSE';
		} elseif ( $temp === NULL ) {
			$string = 'NULL';
		} else {
			$string = $temp;
		}
		
		return htmlspecialchars($string);
	}
	
	public function getHTMLReportName($g){
		$report = '';
		if ( isset($g['ids']) && $g['ids'] !== '' ) {
			$report .= 'ID\'s= '.$g['ids'].'. ';
		}
		if ( isset($g['filters']) && is_array($g['filters']) ) {
			foreach ( $g['filters'] as $filter ) {
				if ( $filter !== '' ) {
					$report .= 'FILTER='.$filter.'. ';
				}
			}
		}
		if ( isset($g['values']) && is_array($g['values']) ) {
			foreach ( $g['values'] as $value ) {
				if ( $value !== '' ) {
					$report .= 'VALUE='.$value.'. ';
				}
			}
		}
		
		return $report;
	}
	
	public function setGet($get=array()){
		$this->_get = $get;
		
		return;
	}
	
	public function echoGetHTML($key='',$index=NULL){
		$value = '';
		if ( isset($this->_get[$key]) ) {
			if ( $index !== NULL ) {
				if ( isset($this->_get[$key][$index]) ) {
					$value = htmlspecialchars($this->_get[$key][$index]);
				}
			} else {
				$value = htmlspecialchars($this->_get[$key]);
			}
		}
		echo $value;
		
		return;
	}
}
?>
