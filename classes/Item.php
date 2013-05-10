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

class Item 
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
	
	public function __construct(){}
	
	public function setLocation($str=''){
		$this->_location = $str;
	}
	
	public function setAccess($str=''){
		$this->_access = $str;
	}
	
	public function setCopyright($str=''){
		$this->_copyright = $str;
	}
	
	public function setPhysicalDescription($str=''){
		$this->_physical_description = $str;
	}
	
	public function setMediaType($str=''){
		//TODO: set a list of predefined(normalized) media types
		$this->_media_type = $str;
	}
	
	public function setUri($str=''){
		$this->_uri = $str;
	}
	
	public function setMimeType($str=''){
		//TODO: filter for valid MIME type
		$this->_mime_type = $str;
	}
	
	public function setPublicationDate($str=''){
		$this->_publication_date = $str;
	}
	
	public function setPublisher($str=''){
		$this->_publisher = $str;
	}
	
	public function setJournal($str=''){
		$this->_journal = $str;
	}
	public function setVolume($str=''){
		$this->_volume = $str;
	}
	public function setIssue($str=''){
		$this->_issue = $str;
	}
	public function setPagination($str=''){
		$this->_pagination = $str;
	}
	public function setStartPage($str=''){
		$this->_start_page = $str;
	}
	
	public function setIdentifier($label='',$delimiter='',$str=''){
		if ( $label==='' || $delimiter==='' || $str==='' ) {
			return;
		} else {
			$this->_identifiers[] = $label.$delimiter.$str;
		}
	}
	
	public function setCost($str=''){
		$this->_cost = $str;
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
	
	public function isItem($item){
		if ( is_object($item) && get_class($item) === get_class($this) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
