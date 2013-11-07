<?php 

class AlephFindDoc 
{
	private $_resource = NULL;
	private $_oai_marc_records = array();
	
	public function __construct($xml=''){
		try {
			$this->_resource = new SimpleXMLElement($xml);
		} catch (Exception $e) {
			throw new Exception('CLASS: '.get_class().'. FUNCTION: __construct(). EXCEPTION: '.$e->getMessage());
		}
		$this->_setRecords();
		if ( $this->_resource->getName() === 'find-doc' ) {
			$this->_setRecords();
		} else {
			throw new Exception('CLASS: '.get_class().'. FUNCTION: __construct(). EXCEPTION: XML document root is not \'find-doc\'');
		}
	}
	
	private function _setRecords(){
		if ( isset($this->_resource->record->metadata->oai_marc) ) {
			$this->_oai_marc_records[1] = $this->_resource->record->metadata->oai_marc->asXML();
		}
	}
	
	public function getRecords(){
		return $this->_oai_marc_records;
	}
}
?>
