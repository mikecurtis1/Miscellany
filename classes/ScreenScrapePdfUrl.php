<?php 

class ScreenScrapePdfUrl
{
	private $_source_url = '';
	private $_source_html = '';
	private $_source_host = '';
	private $_pdf_url = FALSE;
	private $_regex = '';
	private $_regex_ebscohost = "\<a id\=\"downloadLink\".*?href\=\"(.*?)\".*?\>Download PDF\<\/a\>";
	private $_regex_sciencedirect = "\<a id\=\"pdfLink\".*?href\=\"(.*?)\".*?title\=\"Download PDF\".*?\>PDF.*?\<\/a\>";
	private $_regex_ovid = "\<iframe.*?\>\<a href\=\"(.*?)\"\>.*?\<\/a\>\<\/iframe\>";
	
	private function __construct($arg=NULL){
		if ( $this->_isUrl($arg) ) {
			$this->_source_url = $arg;
			$this->_source_html = file_get_contents($arg);
			$this->_source_host = parse_url($arg, PHP_URL_HOST);
			$this->_setRegex();
			$this->_pdf_url = $this->_screenScrape();
			return $this;
		} else {
			return FALSE;
		}
	}

	static public function build($arg=NULL){
		return new ScreenScrapePdfUrl($arg);
		/*if ( self::_isUrl($arg) ) {
			$obj = new ScreenScrapePdfUrl;
			$obj->_source_url = $arg;
			$obj->_source_html = file_get_contents($arg);
			$obj->_source_host = parse_url($arg, PHP_URL_HOST);
			$obj->_setRegex();
			$obj->_pdf_url = $obj->_screenScrape();
			return $obj;
		} else {
			return FALSE;
		}*/
	}
	
	static private function _isUrl($arg=NULL){
		if ( filter_var($arg, FILTER_VALIDATE_URL) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function _setRegex(){
		if ( strpos($this->_source_host, 'ebscohost.com') !== FALSE ) {
			$this->_regex = $this->_regex_ebscohost;
		} elseif ( strpos($this->_source_host, 'sciencedirect.com') !== FALSE ) {
			$this->_regex = $this->_regex_sciencedirect;
		} elseif ( strpos($this->_source_host, 'ovid.com') !== FALSE ) {
			$this->_regex = $this->_regex_ovid;
		} else {
			return FALSE;
		}
	}
	
	private function _screenScrape(){
		if ( $this->_regex !== '' ) {
			if ( preg_match("/".$this->_regex."/si",$this->_source_html,$match) === 1 ) {
				return trim(htmlspecialchars_decode($match[1]));
			} else {
				return FALSE;
			}
		}
	}
	
	public function getUrl(){
		return $this->_pdf_url;
	}
}
?>
