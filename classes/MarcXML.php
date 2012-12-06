<?php 
require_once('MySimpleXML.php');

class MarcXML extends MySimpleXML
{
	public function getFirstMarcTag($object=NULL,$xpath=''){
		$arr = parent::getXpathObjects($xpath);
		if ( isset($arr[0]) ) {
			return parent::getPlainText($arr[0])."\n";
		} else {
			return '';
		}
	}

	public function getAllMarcTags($object=NULL,$xpath=''){
		$temp = array();
		$arr = parent::getXpathObjects($xpath);
		foreach ( $arr as $cur ) {
			$temp[] = parent::getPlainText($cur);
		}
		
		return $temp;
	}

	public function getAllMarcTagsWithSubfields($object=NULL,$xpath='',$subfields=array(),$attr='code'){
		$temp = array();
		$arr = parent::getXpathObjects($xpath);
		foreach ( $arr as $cur ) {
			$temp[] = $this->_getMarcSubfieldValues($cur,$subfields,$attr);
		}
		
		return $temp;
	}

	private function _getMarcSubfieldValues($object=NULL,$subfields=array(),$attr='code'){
		$kev = array();
		$s = new MarcXML($object->asXML());
		foreach ( $subfields as $subfield ) {
			$arr = $s->getXpathObjects('subfield[@'.$attr.'="'.$subfield.'"]');
			if ( isset($arr[0]) ) {
				$text = $s->getPlainText($arr[0])." ";
			} else {
				$text = '';
			}
			$kev[$subfield] = $text;
		}
		
		return $kev;
	}
}
?>