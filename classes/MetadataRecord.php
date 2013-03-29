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

class MetadataRecord
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
	private $_date = '';
	private $_identifiers = array();
	private $_topics = array(); //TODO: subjects, persons, chronological terms (date ranges, named time periods, etc.), geographical terms (named places, postal addresses, geo-coordinates, etc.)
	private $_items = array();
	private $_edition = '';
	private $_ = '';
	private $_ = '';
	private $_ = '';
	private $_ = '';
	
	//TODO: break out FRBR item elements
	private $_location = '';
	private $_physical_description = '';
	private $_media_type = '';
	private $_uri = '';
	private $_mime_type = '';
	private $_pubdate = '';
	private $_journal = '';
	private $_volume = '';
	private $_issue = '';
	private $_pagination = '';
	private $_start_page = '';
	private $_publisher = '';
	private $_identifiers = array();
	private $_cost = ''; //NOTE: time sensitive, monetary unit required, vendor required
	private $_ = '';
	private $_ = '';
	private $_ = '';
	private $_ = '';
	private $_ = '';
	
	public function __construct(){
		/*$this->setMetadataSource();
		$this->setMetadataSourceId();
		$this->setMetadataSourceUri();
		$this->setTitle();
		$this->setDescription();
		$this->setLanguage();
		$this->setMediaType();
		$this->setMimeType();*/
	}
	
	public function setMetadataSource($str=''){
		$this->_metadata_source = trim($str);
	}
	
	public function setMetadataSourceId($str=''){
		$this->_metadata_source_id = trim($str);
	}
	
	public function setMetadataSourceUri($str=''){
		//TODO: filter for valid URI
		$this->_metadata_source_uri = trim($str);
	}
	
	public function setTitle($str=''){
		$this->_title = trim($str);
	}
	
	public function setSubtitle($str=''){
		$this->_subtitle = trim($str);
	}
	
	public function setDescription($str=''){
		$this->_description = trim($str);
	}
	
	public function setLanguage($str=''){
		$this->_language = trim($str);
	}
	
	public function setMediaType($str=''){
		//TODO: set a list of predefined(normalized) media types
		$this->_media_type = trim($str);
	}
	
	public function setMimeType($str=''){
		//TODO: filter for valid MIME type
		$this->_mime_type = trim($str);
	}
	
	public function setAuthor($str=''){
		$this->_authors[] = trim($str);
	}
	
	public function getValue($name='',$first_value=FALSE){
		if ( isset($this->$name) ) {
			if ( is_array($this->$name) ) {
				if ( $first_value === TRUE ) {
					if ( isset($this->$name[0]) ) {
						return $this->$name[0];
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
