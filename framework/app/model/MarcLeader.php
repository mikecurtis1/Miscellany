<?php 

class MarcLeader
{
	private $_data = NULL;
	private $_record_length = ''; // calculated, equal to the length of the entire record, including itself and the record terminator
	private $_record_status = ''; // new, c=revised
	private $_type_of_record = ''; // language material, c=notated music, e=cartographic, g=projected, i=non-music sournd, j=musical sound
	private $_bibliographic_level = ''; // monograph/item, s=serial
	private $_type_of_control = '';
	private $_character_coding_scheme = '';
	private $_indicator_count = '';
	private $_subfield_code_count = ''; 
	private $_base_address_of_data = ''; // calculated, equal to the sum of the lengths of the leader and the directory, including the field terminator at the end of the directory
	private $_encoding_level = '';
	private $_descriptive_cataloging_form = '';
	private $_multipart_resource_record_level = '';
	private $_entry_map = '';
	
	private function __construct($data=NULL){
		$this->_data = $data;
		$this->_record_length = substr($data,0,5);
		$this->_record_status = substr($data,5,1);
		$this->_type_of_record = substr($data,6,1);
		$this->_bibliographic_level = substr($data,7,1);
		$this->_type_of_control = substr($data,8,1);
		$this->_character_coding_scheme = substr($data,9,1);
		$this->_indicator_count = substr($data,10,1);
		$this->_subfield_code_count = substr($data,11,1);
		$this->_base_address_of_data = substr($data,12,5);
		$this->_encoding_level = substr($data,17,1);
		$this->_descriptive_cataloging_form = substr($data,18,1);
		$this->_multipart_resource_record_level = substr($data,19,1);
		$this->_entry_map = substr($data,20,4);
	}

	static public function build($data=NULL){
		if ( is_string($data) && strlen($data) === 24 ) {	
			return new MarcLeader($data);
		} else {
			return FALSE;
	 	}
	}
	
	public function getRecordLength(){
		return $this->_record_length;
	}

	public function getRecordStatus(){
		return $this->_record_status;
	}

	public function getTypeORecord(){
		return $this->_type_of_record;
	}

	public function getBibliographicLevel(){
		return $this->_bibliographic_level;
	}

	public function getTypeOfControl(){
		return $this->_type_of_control;
	}

	public function getCharacterCodingScheme(){
		return $this->_character_coding_scheme;
	}

	public function getIndicatorCount(){
		return $this->_indicator_count;
	}

	public function getSubfieldCodeCount(){
		return $this->_subfield_code_count;
	}

	public function getBaseAddressOfData(){
		return $this->_base_address_of_data;
	}

	public function getEncodingLevel(){
		return $this->_encoding_level;
	}

	public function getDescriptiveCatalogingForm(){
		return $this->_descriptive_cataloging_form;
	}

	public function getMultipartResourceRecordLevel(){
		return $this->_multipart_resource_record_level;
	}

	public function getEntryMap(){
		return $this->_entry_map;
	}

	public function getData(){
		return $this->_data;
	}
}
?>
