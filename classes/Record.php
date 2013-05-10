<?php 

/* 
 * http://archive.ifla.org/VII/s13/frbr/frbr_current_toc.htm
 * http://dublincore.org/documents/dces/\
 * http://www.loc.gov/marc/bibliographic/bdsummary.html
 * http://www.loc.gov/marc/authority/adsummary.html
 * http://www.loc.gov/marc/holdings/hdsummary.html
 * http://ocoins.info/cobg.html
 * http://ocoins.info/cobgbook.html
 * 
 */

class Record 
{
  	private $_metadata_source = '';
	private $_metadata_source_id = '';
	private $_metadata_source_uri = '';
	private $_authors = array();
	private $_title = '';
	private $_subtitle = '';
	private $_series = '';
	private $_description = '';
	private $_language = '';
	private $_creation_date = '';
	private $_identifiers = array();
	private $_topics = array(); //TODO: subjects, persons, chronological terms (date ranges, named time periods, etc.), geographical terms (named places, postal addresses, geo-coordinates, etc.)
	private $_items = array();
	private $_edition = '';
	
	public function __construct(){}
	
	public function setMetadataSource($str=''){
		$this->_metadata_source = $str;
	}
	
	public function setMetadataSourceId($str=''){
		$this->_metadata_source_id = $str;
	}
	
	public function setMetadataSourceUri($str=''){
		//TODO: filter for valid URI
		$this->_metadata_source_uri = $str;
	}
	
	public function setAuthor($str=''){
		if ( $str !== '' ) {
			$this->_authors[] = $str;
		}
	}
	
	public function setTitle($str=''){
		$this->_title = $str;
	}
	
	public function setSubtitle($str=''){
		$this->_subtitle = $str;
	}
	
	public function setSeries($str=''){
		$this->_series = $str;
	}
	
	public function setDescription($str=''){
		$this->_description = $str;
	}
	
	public function setLanguage($str=''){
		$this->_language = $str;
	}
	
	public function setCreationDate($str=''){
		$this->_creation_date = $str;
	}
	
	public function setIdentifier($label='',$delimiter='',$str=''){
		if ( $label==='' || $delimiter==='' || $str==='' ) {
			return;
		} else {
			$this->_identifiers[] = $label.$delimiter.$str;
		}
	}
	
	public function setItem($item){
		if ( is_object($item) && get_class($item) === 'Item' ) {
			$this->_items[] = $item;
		}
	}
	
	public function setTopic($str=''){
		if ( $str !== '' ) {
			$this->_topics[] = $str;
		}
	}
	
	public function setEdition($str=''){
		$this->_edition = $str;
	}
	
	public function getValue($name='',$first_value=FALSE){
		$name = '_'.$name;
		if ( isset($this->$name) ) {
			if ( is_array($this->$name) ) {
				if ( $first_value === TRUE ) {
					if ( isset($this->{$name}[0]) ) {
						return $this->{$name}[0];
					} else {
						return '';
					}
				} else {
					return $this->$name;
				}
			} else {
				return $this->$name;
			}
		} else {
			return '';
		}
	}
	
	public function isRecord($record){
		if ( is_object($record) && get_class($record) === get_class($this) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
