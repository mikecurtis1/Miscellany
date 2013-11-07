<?php 

require_once('Marc.php');

class OAIMarc extends Marc 
{
	private $_resource = NULL;
	
	public function __construct($xml=''){
		try {
			$this->_resource = new SimpleXMLElement($xml);
		} catch (Exception $e) {
			throw new Exception('CLASS: '.get_class().'. FUNCTION: __construct(). EXCEPTION: '.$e->getMessage());
		}
		if ( $this->_resource->getName() === 'oai_marc' ) {
			$this->_setFields();
			$this->_buildHSLRecord();
		} else {
			throw new Exception('CLASS: '.get_class().'. FUNCTION: __construct(). EXCEPTION: XML document root is not \'oai_marc\'');
		}
	}
	
	private function _setFields(){
		foreach ( $this->_resource as $n => $obj ) {
			if ( $obj->getName() === 'fixfield' ) {
				$id = (string) $obj->attributes()->id;
				$text = (string) $obj;
				if ( $id === 'LDR' ) {
					$this->_addLeader(MarcLeader::build($text));
				} else {
					$this->_addControlField(MarcControlField::build($id,$text));
				}
			}
			if ( $obj->getName() === 'varfield' ) {
				$id = (string) $obj->attributes()->id;
				$i1 = (string) $obj->attributes()->i1;
				$i2 = (string) $obj->attributes()->i2;
				if ( $marc_data_field = MarcDataField::build($id,$i1,$i2) ) {
					foreach ( $obj->subfield as $sub ) {
						$label = (string) $sub->attributes()->label;
						$text = (string) $sub;
						$marc_data_field->addSubfield(MarcSubfield::build($label,$text));
					}
					$this->_addDataField($marc_data_field);
				}
			}
		}
	}
}
?>
