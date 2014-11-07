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
	private $_language = ''; //TODO: ISO 639-2, http://www.loc.gov/standards/iso639-2/php/code_list.php
	private $_creation_date = ''; //TODO: RFC 3339 Internet Date & Time
	private $_identifiers = array(); //TODO: RFC 3986 URI, rfc3187 & ISO 2108:2005 ISBN, rfc4452 for ID URN's etc.
	private $_topics = array(); //TODO: subjects, persons, chronological terms (date ranges, named time periods, etc.), geographical terms (named places, postal addresses, geo-coordinates, etc.)
	private $_items = array();
	private $_edition = ''; //NOTE: because edition may mean a revised - therefore different work - this a record element not an item element
	
	public function __construct(){
	}
	
	public function setMetadataSource($arg=''){
		$this->_metadata_source = $arg;
	}
	
	public function setMetadataSourceId($arg=''){
		$this->_metadata_source_id = $arg;
	}
	
	public function setMetadataSourceUri($arg=''){
		//TODO: filter for valid URI
		$this->_metadata_source_uri = $arg;
	}
	
	public function setAuthor($arg=''){
		if ( $arg !== '' ) {
			$this->_authors[] = $arg;
		}
	}
	
	public function setTitle($arg=''){
		$this->_title = $arg;
	}
	
	public function setSubtitle($arg=''){
		$this->_subtitle = $arg;
	}
	
	public function setSeries($arg=''){
		$this->_series = $arg;
	}
	
	public function setDescription($arg=''){
		$this->_description = $arg;
	}
	
	public function setLanguage($arg=''){
		$this->_language = $arg;
	}
	
	public function setCreationDate($arg=''){
		$this->_creation_date = $arg;
	}
	
	public function setIdentifier($arg=''){
		if ( $arg instanceof Identifier ) {
			$this->_identifiers[] = $arg;
		}
	}
	
	public function setItem($arg){
		if ( $arg instanceof Item ) {
			$this->_items[] = $arg;
		}
	}
	
	public function setTopic($arg=''){
		if ( $arg !== '' ) {
			$this->_topics[] = $arg;
		}
	}
	
	public function setEdition($arg=''){
		$this->_edition = $arg;
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
}
?>
