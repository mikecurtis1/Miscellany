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

class HSLItem 
{
	private $_location = '';
	private $_access = '';
	private $_copyright = '';
	private $_physical_description = '';
	private $_media_type = '';
	private $_uri = '';
	private $_mime_type = '';
	private $_publication_date = '';
	private $_publisher = '';
	private $_journal = '';
	private $_volume = '';
	private $_issue = '';
	private $_pagination = '';
	private $_start_page = '';
	private $_identifiers = array();
	private $_cost = ''; //NOTE: time sensitive, monetary unit required, vendor required
	
	public function __construct(){
	}
	
	public function setLocation($arg=''){
		$this->_location = $arg;
	}
	
	public function setAccess($arg=''){
		$this->_access = $arg;
	}
	
	public function setCopyright($arg=''){
		$this->_copyright = $arg;
	}
	
	public function setPhysicalDescription($arg=''){
		$this->_physical_description = $arg;
	}
	
	public function setMediaType($arg=''){
		//TODO: set a list of predefined(normalized) media types
		$this->_media_type = $arg;
	}
	
	public function setUri($arg=''){
		$this->_uri = $arg;
	}
	
	public function setMimeType($arg=''){
		//TODO: filter for valid MIME type
		$this->_mime_type = $arg;
	}
	
	public function setPublicationDate($arg=''){
		$this->_publication_date = $arg;
	}
	
	public function setPublisher($arg=''){
		$this->_publisher = $arg;
	}
	
	public function setJournal($arg=''){
		$this->_journal = $arg;
	}
	public function setVolume($arg=''){
		$this->_volume = $arg;
	}
	public function setIssue($arg=''){
		$this->_issue = $arg;
	}
	public function setPagination($arg=''){
		$this->_pagination = $arg;
	}
	public function setStartPage($arg=''){
		$this->_start_page = $arg;
	}
	
	public function setIdentifier($arg=''){
		if ( $arg instanceof HSLIdentifier ) {
			$this->_identifiers[] = $arg;
		}
	}
	
	public function setCost($arg=''){
		$this->_cost = $arg;
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
