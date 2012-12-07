<?php 
require_once('MySimpleXML.php');

class MarcXML extends MySimpleXML
{
	public function getFirstMarcTag($xpath=''){
		$arr = parent::getXpathObjects($xpath);
		if ( isset($arr[0]) ) {
			return parent::getPlainText($arr[0]);
		} else {
			return '';
		}
	}

	public function getAllMarcTags($xpath=''){
		$temp = array();
		$arr = parent::getXpathObjects($xpath);
		foreach ( $arr as $cur ) {
			$temp[] = parent::getPlainText($cur);
		}
		
		return $temp;
	}

	public function getAllMarcTagsWithSubfields($xpath='',$subfields=array(),$attr='code'){
		$temp = array();
		$arr = parent::getXpathObjects($xpath);
		foreach ( $arr as $cur ) {
			$temp[] = $this->_getMarcSubfieldValues($cur,$subfields,$attr);
		}
		
		return $temp;
	}

	private function _getMarcSubfieldValues($object=NULL,$subfields=array(),$attr='code'){
		$kev = array();
		if ( parent::_isSimpleXMLElement($object) ) {
			$x = new MarcXML($object->asXML());
			foreach ( $subfields as $subfield ) {
				//NOTE: subfield seems to be the common tag name among various MaRCXML types, but the attribute name changes
				//NOTE: when the simplexmlelement is changed to XML and a new instance is created, it no longer seems to have namespace qualities
				$arr = $x->getXpathObjects('subfield[@'.$attr.'="'.$subfield.'"]');
				if ( isset($arr[0]) ) {
					$text = $x->getPlainText($arr[0])." ";
				} else {
					$text = '';
				}
				$kev[$subfield] = $text;
			}
		}
		
		return $kev;
	}
}
?>