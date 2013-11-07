<?php 

/*
http://www.loc.gov/marc/bibliographic/bdintro.html
http://www.loc.gov/marc/bibliographic/bdsummary.html
*/

require_once('MarcLeader.php');
require_once('MarcControlField.php');
require_once('MarcDataField.php');
require_once('MarcSubfield.php');
require_once('HSLItem.php');
require_once('HSLRecord.php');
require_once('HSLIdentifier.php');

class Marc
{
	protected $_leader = '';
	protected $_control_fields = array();
	protected $_data_fields = array();
	private $_hsl_record = NULL;
	private $_subfield_delimiter = ' ';
	private $_id_delimiter = '::'; //chr(31)
	
	protected function _addLeader($arg=NULL){
		if ( $arg instanceof MarcLeader ) {
			$this->_leader = $arg->getData();
		}
	}
	
	protected function _addControlField($arg=NULL){
		if ( $arg instanceof MarcControlField ) {
			$this->_control_fields[$arg->getTag()][] = $arg->getData();
		}
	}
	
	protected function _addDataField($arg=NULL){
		if ( $arg instanceof MarcDataField ) {
			$this->_data_fields[$arg->getTag()][] = $arg;
		}
	}
	
	protected function _buildHSLRecord(){
		$this->_hsl_record = new HSLRecord();
		$item = new HSLItem();
		$this->_hsl_record->setMetadataSourceId($this->_getFieldValue('control_fields','001',NULL));
		$this->_hsl_record->setTitle($this->_getFieldValue('data_fields','245',NULL));
		$this->_hsl_record->setCreationDate($this->_getFieldValue('data_fields','260','c'));
		$this->_hsl_record->setLanguage($this->_getFieldValue('data_fields','041','a'));
		$this->_hsl_record->setAuthor($this->_buildAuthor());
		$this->_hsl_record->setAuthor($this->_getFieldValue('data_fields','100',NULL));
		$this->_hsl_record->setDescription($this->_getFieldValue('data_fields','520','a'));
		$item->setPhysicalDescription($this->_getFieldValue('data_fields','300',NULL));
		foreach ( $this->_getFieldValues('data_fields','600','a') as $topic ) {
			$this->_hsl_record->setTopic($topic);
		}
		foreach ( $this->_getFieldValues('data_fields','648','a') as $topic ) {
			$this->_hsl_record->setTopic($topic);
		}
		foreach ( $this->_getFieldValues('data_fields','650','a') as $topic ) {
			$this->_hsl_record->setTopic($topic);
		}
		$item->setMediaType($this->_buildMediaType());
		$item->setIdentifier(HSLIdentifier::build('SYS',$this->_id_delimiter,$this->_getFieldValue('control_fields','001',NULL)));
		foreach ( $this->_getFieldValues('data_fields','010','a') as $id ) {
			$item->setIdentifier(HSLIdentifier::build('LCCN',$this->_id_delimiter,$id));
		}
		foreach ( $this->_getFieldValues('data_fields','020','a') as $id ) {
			$item->setIdentifier(HSLIdentifier::build('ISBN',$this->_id_delimiter,$id));
		}
		foreach ( $this->_getFieldValues('data_fields','022','a') as $id ) {
			$item->setIdentifier(HSLIdentifier::build('ISSN',$this->_id_delimiter,$id));
		}
		foreach ( $this->_getFieldValues('data_fields','035','a') as $id ) {
			$item->setIdentifier(HSLIdentifier::build('035',$this->_id_delimiter,$id));
		}
		foreach ( $this->_getFieldValues('data_fields','050',NULL) as $id ) {
			$item->setIdentifier(HSLIdentifier::build('LCCALL',$this->_id_delimiter,$id));
		}
		foreach ( $this->_getFieldValues('data_fields','082',NULL) as $id ) {
			$item->setIdentifier(HSLIdentifier::build('DDCALL',$this->_id_delimiter,$id));
		}
		$this->_hsl_record->setItem($item);
		
	}
	
	private function _buildAuthor(){
		$statement_of_responsibility = trim($this->_getFieldValue('data_fields','245','c'));
		if ( substr($statement_of_responsibility,-1,1) === '.' ) {
			return substr($statement_of_responsibility,0,-1);
		} else {
			return $statement_of_responsibility;
		}
	}
	
	private function _buildMediaType(){
		$medium = trim($this->_getFieldValue('data_fields','245','h'));
		if ( preg_match("/\[(.+?)\]/",$medium,$match) ) {
			return $match[1];
		} else {
			return $medium;
		}
	}
	
	private function _getLeader(){
		return $this->_leader;
	}
	
	private function _getFieldValue($type=NULL,$tag=NULL,$codes=NULL){
		$array = $this->_getFieldValues($type,$tag,$codes);
		if(isset($array[0])){
			return $array[0];
		} else {
			return '';
		}
	}
	
	private function _getFieldValues($type=NULL,$tag=NULL,$codes=NULL){
		$array = array();
		if ( $type === 'control_fields' ) {
			$array = $this->_getControlFieldValues($tag);
		} 
		if ( $type === 'data_fields' ) {
			$array = $this->_getDataFieldValues($tag,$codes);
		}
		
		return $array;
	}
	
	private function _getControlFieldValues($tag=NULL){
		$array = array();
		if ( isset($this->_control_fields[$tag]) ) {
			foreach ( $this->_control_fields[$tag] as $string ) {
				$array[] = $string;
			}
		}
		
		return $array;
	}
	
	private function _getDataFieldValues($tag=NULL,$codes=NULL){
		$array = array();
		$c = array();
		if ( is_string($codes) ) {
			$c = explode(',',$codes);
		}
		if ( isset($this->_data_fields[$tag]) ) {
			foreach ( $this->_data_fields[$tag] as $field ) {
				$subfield_data = array();
				if ( count($c) > 0 ) {
					foreach ( $c as $code ) {
						$sub_arr = $field->getSubfields();
						if ( isset($sub_arr[$code]) ) {
							
							foreach ( $sub_arr[$code] as $data ) {
								$subfield_data[] = trim($data);
							}
						}
					}
				} else {
					foreach ( $field->getSubfields() as $subfield ) {
						foreach ( $subfield as $data ) {
							$subfield_data[] = trim($data);
						}
					}
				}
				$string = implode($this->_subfield_delimiter,$subfield_data);
				$array[] = $string;
			}
		}
		
		return $array;
	}
	
	public function getHSLRecord(){
		return $this->_hsl_record;
	}
	
	public function buildPlainText(){
		$plaintext = '';
		$plaintext .= 'LDR '.$this->_leader."\n";
		foreach ( $this->_control_fields as $id => $arr ) {
			foreach ( $arr as $n => $data ) {
				$plaintext .= $id.' '.trim($data)."\n";
			}
		}
		foreach ( $this->_data_fields as $id => $arr ) {
			foreach ( $arr as $n => $data ) {
				$i1 = str_replace(' ','_',$data->getId1());
				$i2 = str_replace(' ','_',$data->getId2());
				$subfields_str = '';
				foreach ( $data->getSubfields() as $code => $sub_arr ) {
					foreach ( $sub_arr as $n => $text ) {
						$subfields_str .= $code.'|'.$text.' ';
					}
				}
				$plaintext .= $id.$i1.$i2.' '.trim($subfields_str)."\n";
			}
		}
		
		return $plaintext;
	}
}
?>
