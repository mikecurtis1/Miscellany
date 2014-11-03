<?php 

/*

http://www.loc.gov/marc/bibliographic/bdintro.html
http://www.loc.gov/marc/specifications/specrecstruc.html
http://www.loc.gov/marc/bibliographic/bdleader.html

usage 

$mrc = new Marc21Maker();
$mrc->addControlField('008','141030s2014^^^^^^^^^^^^^^^^^^000^^^eng^d');
$mrc->addDataField('100',' ',' ','$aauthor');
$mrc->addDataField('245',' ',' ','$aTitle With Escaped \$ Dollar Sign');
$mrc->emitMRC();

*/

require_once('EscapeString.php');

class Marc21Maker 
{
	private $_marc21_mime_type = 'application/marc';
	
	private $_field_terminator = ''; // ends directory and fields
	private $_record_terminator = ''; // end of a complete MARC21 record
	private $_subfield_indicator = ''; // prefix to subfield codes
	private $_subfield_indicator_graphic = '$';
	
	private $_control_field_tags = array('FMT','001','003','005','006','007','008','009');
	
	private $_leader = '';
	private $_data = array();
	private $_directory = '';
	private $_content = '';
	private $_mrc = '';
	
	private $_leader_record_length = '00000'; // calculated, equal to the length of the entire record, including itself and the record terminator
	private $_leader_record_status = 'n'; // new, c=revised
	private $_leader_type_of_record = 'a'; // language material, c=notated music, e=cartographic, g=projected, i=non-music sournd, j=musical sound
	private $_leader_bibliographic_level = 'm'; // monograph/item, s=serial
	private $_leader_type_of_control = ' ';
	private $_leader_character_coding_scheme = ' ';
	private $_leader_indicator_count = '2';
	private $_leader_subfield_code_count = '2'; 
	private $_leader_base_address_of_data = '00000'; // calculated, equal to the sum of the lengths of the leader and the directory, including the field terminator at the end of the directory
	private $_leader_encoding_level = ' ';
	private $_leader_descriptive_cataloging_form = ' ';
	private $_leader_multipart_resource_record_level = ' ';
	private $_leader_entry_map = '4500';
	
	public function __construct($leader_record_status='n',$leader_type_of_record='a',$leader_bibliographic_level='m'){
		$this->_field_terminator = chr(30); // record separator
		$this->_record_terminator = chr(29); // group separator
		$this->_subfield_indicator = chr(31); // unit separator
		if ( is_string($leader_record_status) && strlen($leader_record_status) === 1 ) {
			$this->_leader_record_status = $leader_record_status;
		} else {
			throw new Exception('Leader record status must be one character!');
		}
		if ( is_string($leader_type_of_record) && strlen($leader_type_of_record) === 1 ) {
			$this->_leader_type_of_record = $leader_type_of_record;
		} else {
			throw new Exception('Leader type of record must be one character!');
		}
		if ( is_string($leader_bibliographic_level) && strlen($leader_bibliographic_level) === 1 ) {
			$this->_leader_bibliographic_level = $leader_bibliographic_level;
		} else {
			throw new Exception('Leader bibliographic level must be one character!');
		}
	}
	
	public function addControlField($tag=NULL,$content=''){
		$field = '';
		if ( ! in_array($tag,$this->_control_field_tags,TRUE) ) {
			throw new Exception($tag . ' is not in the control field list: ' . implode(', ',$this->_control_field_tags) . '!');
		}
		if ( is_string($content) ) {
			if ( strlen($content . $this->_field_terminator) < 9999 ) {
				$field .= $content;
			} else {
				throw new Exception('Field content exceeds length of 9999!');
			}
		}
		try {
			$this->_addField($tag,$field);
		} catch (Exception $e) {
			throw new Exception($e->GetMessage());
		}
	}
	
	public function addDataField($tag=NULL,$i1=NULL,$i2=NULL,$content=''){
		$field = '';
		if ( ! (is_string($tag) && strlen($tag) === 3) ) {
			throw new Exception('Tag must be 3 characters!');
		}
		if ( is_string($i1) && strlen($i1) === 1 ) {
			$field .= $i1;
		} else {
			throw new Exception('Indicator 1 must be one character!');
		}
		if ( is_string($i2) && strlen($i2) === 1 ) {
			$field .= $i2;
		} else {
			throw new Exception('Indicator 2 must be one character!');
		}
		if ( is_string($content) ) {
			$content = $this->_getEscapedContent($content);
			if ( strlen($content . $this->_field_terminator) < 9999 ) {
				$field .= $content;
			} else {
				throw new Exception('Field content exceeds length of 9999!');
			}
		} else {
			throw new Exception('Field content must be a string!');
		}
		try {
			$this->_addField($tag,$field);
		} catch (Exception $e) {
			throw new Exception($e->GetMessage());
		}
	}
	
	private function _addField($tag,$field){
		$field .= $this->_field_terminator;
		$length = $this->_getFieldLength($field);
		$start_pos = $this->_getNextStartPos();
		$this->_data[$tag.$length.$start_pos] = $field;
		try {
			$this->_buildMRC();
		} catch (Exception $e) {
			throw new Exception($e->GetMessage());
		}
	}
	
	private function _getEscapedContent($content=''){
		try {
			$e = new EscapeString($content);
			$str = '';
			foreach ( $e->getEscaped() as $arr ) {
				if ( $arr['is_escaped'] === TRUE ) {
					$str .= $arr['chr'];
				} else {
					if ( $arr['chr'] === $this->_subfield_indicator_graphic ) {
						$str .= $this->_subfield_indicator;
					} else {
						$str .= $arr['chr'];
					}
				}
			}
			return $str;
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	private function _getFieldLength($field=''){
		return str_pad(strval(strlen($field)), 4, '0', STR_PAD_LEFT);
	}
	
	private function _getNextStartPos(){
		return str_pad(strval(strlen(implode('',$this->_data))), 5, '0', STR_PAD_LEFT);
	}
	
	private function _buildMRC(){
		$this->_mrc = '';
		if ( ! empty($this->_data) ) {
			$this->_buildDirectory();
			$this->_buildContents();
			if ( $this->_calculateRecordLength() <= 99999 ) {
				$this->_leader_record_length = $this->_calculateRecordLength();
				$this->_leader_base_address_of_data = $this->_calculateBaseAddressOfData();
				$this->_buildLeader();
				$this->_mrc .= $this->_leader;
				$this->_mrc .= $this->_directory;
				$this->_mrc .= $this->_content;
			} else {
				throw new Exception('calculateRecordLength exceeds length of 99999!');
			}
		} else {
			throw new Exception('buildMRC found an empty data array!');
		}
	}
	
	private function _buildDirectory(){
		$this->_directory = implode('',array_keys($this->_data)) . $this->_field_terminator;
	}
	
	private function _buildContents(){
		$this->_content = implode('',$this->_data) . $this->_record_terminator;
	}
	
	private function _calculateRecordLength(){
		$int = 0;
		$int += 24; // the length of all leaders
		$int += strlen($this->_directory);
		$int += strlen($this->_content);
		
		return str_pad(strval($int), 5, '0', STR_PAD_LEFT);
	}
	
	private function _calculateBaseAddressOfData(){
		$int = 0;
		$int += 24; // the length of all leaders
		$int += strlen($this->_directory);
		
		return str_pad(strval($int), 5, '0', STR_PAD_LEFT);
	}
	
	private function _buildLeader(){
		$this->_leader = $this->_leader_record_length;
		$this->_leader .= $this->_leader_record_status;
		$this->_leader .= $this->_leader_type_of_record;
		$this->_leader .= $this->_leader_bibliographic_level;
		$this->_leader .= $this->_leader_type_of_control;
		$this->_leader .= $this->_leader_character_coding_scheme;
		$this->_leader .= $this->_leader_indicator_count;
		$this->_leader .= $this->_leader_subfield_code_count;
		$this->_leader .= $this->_leader_base_address_of_data;
		$this->_leader .= $this->_leader_encoding_level;
		$this->_leader .= $this->_leader_descriptive_cataloging_form;
		$this->_leader .= $this->_leader_multipart_resource_record_level;
		$this->_leader .= $this->_leader_entry_map;
	}
	
	public function emitMRC($charset='utf-8',$data='',$filename='records'){
		if ( empty($data) ) {
			$data = $this->_mrc;
		}
		if ( is_string($data) && $data !== '' ) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $this->_marc21_mime_type . '; charset=' . $charset );
			header('Content-Disposition: attachment; filename=' . $filename . '.mrc');
			echo $data;
			exit;
		} else {
			throw new Exception('The MARC21 string is empty!');
		}
	}
	
	public function getLeader(){
		return $this->_leader;
	}
	
	public function getMRC(){
		return $this->_mrc;
	}
}
?>
