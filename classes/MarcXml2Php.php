<?php
/**
 * This class can parse both OAI MARC and MARC XML, and OpenRecord in single or multiple record files
 */

class MarcXml2Php {

  public function __construct($ns=NULL) {
    // set namespace prefix
    if($ns != NULL){
      $this->ns = $ns.':';
    } else {
      $this->ns = $ns;
    }
    $this->metadata = array();
    $this->n = 0;
    $this->c = 0;
    $this->xml_parser = xml_parser_create();
    xml_set_object($this->xml_parser,$this);
    xml_set_element_handler($this->xml_parser, "_startHandler", "_endHandler");
    xml_set_character_data_handler($this->xml_parser, "_dataHandler");
    xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, false);
    xml_parser_set_option($this->xml_parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
  }

  public function parse($xml){
    if(!xml_parse($this->xml_parser, $xml)) {
      $this->metadata[] = "Error in XML source file on line " . xml_get_current_line_number($this->xml_parser);
    }
    xml_parser_free($this->xml_parser);

    return $this->metadata;
  }

  private function _startHandler($parser, $name, $attrs){
    $this->temp_data = "";
    switch($name) {
    case $this->ns.'fixfield':
      $this->tag_id = $attrs['id'];
      break;
    case $this->ns.'controlfield':
      $this->tag_id = $attrs['tag'];
      break;
    case $this->ns.'leader':
      $this->tag_id = "LEADER";
      break;
    case $this->ns.'varfield':
      $this->tag_id = $attrs['id'];
      $this->i1 = $attrs['i1'];
      $this->i2 = $attrs['i2'];
      if ($this->i1 == " ") {
        $this->i1 = "_";
      }
      if ($this->i2 == " ") {
        $this->i2 = "_";
      }
      // this array push initially adds the tag to the metadata array this must happen prior to any steps that add data to the metadata array at the varfield/datafield level or subfield level
      $this->metadata[$this->n]['MARC'.$this->tag_id][] = array();
      break; 
    case $this->ns.'datafield':
      $this->tag_id = $attrs['tag'];
      $this->i1 = $attrs['ind1'];
      $this->i2 = $attrs['ind2'];
      if ($this->i1 == " ") {
        $this->i1 = "_";
      }
      if ($this->i2 == " ") {
        $this->i2 = "_";
      }
      // add marc tag array to metadata array at the varfield/datafield level or subfield level
      $this->metadata[$this->n]['MARC'.$this->tag_id][] = array();
      break;
    // OAI MARC and MARCXML both use the XML element name 'subfield' but they use 'label' and 'code' alternately in the subfield attribute
    case $this->ns.'subfield':
      $this->tagtype = "subfield";
      if (isset($attrs['label'])) {
        $this->label = $attrs['label'];
      }
      if (isset($attrs['code'])) {
        $this->label = $attrs['code'];
      }
      break;
    }
  }

  private function _dataHandler($parser, $data) {
    $data = trim($data,"\n,\r,\0");
    $this->temp_data .= $data;
  }

  private function _endHandler($parser, $name) {
    switch($name) {
    // add fixfield/controlfield/leader these don't have subfields so simply push to the metadata array
    case $this->ns.'fixfield':
    case $this->ns.'controlfield':
    case $this->ns.'leader':
      $this->metadata[$this->n]['MARC'.$this->tag_id][] = $this->temp_data;
      $this->temp_data = "";
      break;
    // use 'c' counter to push data at the varfield/datafield level
    case $this->ns.'varfield':
    case $this->ns.'datafield':
      if (isset($this->metadata[$this->n]['MARC'.$this->tag_id])) {
        $this->c = count($this->metadata[$this->n]['MARC'.$this->tag_id])-1;
      }
      // the 'full' element is built by concatenation with white space at the close of the varfield/datafield
      $this->metadata[$this->n]['MARC'.$this->tag_id][$this->c]['full'] = trim($this->metadata[$this->n]['MARC'.$this->tag_id][$this->c]['full']);
      break;
    // add subfields
    case $this->ns.'subfield':
      if ($this->temp_data != "") {
        if (isset($this->metadata[$this->n]['MARC'.$this->tag_id])) {
          $this->c = count($this->metadata[$this->n]['MARC'.$this->tag_id])-1;
        }
        // the following line adds the MARC indicators attrs array to the metadata array. 
        $this->metadata[$this->n]['MARC'.$this->tag_id][$this->c]['attrs'] = array("i1"=>$this->i1,"i2"=>$this->i2);
        // add subfield data
        $this->metadata[$this->n]['MARC'.$this->tag_id][$this->c]['subfields'][$this->label][] = trim($this->temp_data);
        // concatenate subfield data into 'full' element in metadata array
        if (isset($this->metadata[$this->n]['MARC'.$this->tag_id][$this->c]['full'])) {
          $this->metadata[$this->n]['MARC'.$this->tag_id][$this->c]['full'] .= $this->temp_data." ";
        } else {
          $this->metadata[$this->n]['MARC'.$this->tag_id][$this->c]['full'] = $this->temp_data." ";
        }
        // tag is closed, all processing finished, reset the temp_data variable
        $this->temp_data = "";
      }
      break;
    // this detects the end of a single MARC record, at this point the entire MARC record is in the PHP metadata array
    case $this->ns.'oai_marc':
    case $this->ns.'recordData':
    case $this->ns.'opacRecord':
    case $this->ns.'record':
      // end of all processing for the MARC record autoincrement the metadata array counter
      $this->n++;
      break;
    }
  }
  
  public function getFieldValue($n=0,$field=NULL,$subfields=NULL){
    $temp = $this->getFieldValues($n,$field,$subfields);
    if(isset($temp[0])){
      return $temp[0];
    } else {
      return FALSE;
    }
  }
  
  public function getFieldValues($n=0,$field=NULL,$subfields=NULL){
    $data = array();
    $f = 'MARC'.$field;
    if(isset($this->metadata[$n][$f][0]['subfields']) && is_array($this->metadata[$n][$f][0]['subfields'])){ 
      // varfields
      if($subfields !== NULL){
        $s = explode(',',$subfields);
        foreach($this->metadata[$n][$f] as $varfield){
          $temp = '';
          foreach($s as $subfield){
            if(isset($varfield['subfields'][$subfield][0])){
              $temp .= $varfield['subfields'][$subfield][0].' ';
            }
          }
          $data[] = trim($temp);
        }
      } else {
        foreach($this->metadata[$n][$f] as $varfield){
          if(isset($varfield['full'])){
            $data[] = trim($varfield['full']);
          }
        }
      }
    } else { 
      // fixfields
      if(isset($this->metadata[$n][$f]) && is_array($this->metadata[$n][$f])){
        foreach($this->metadata[$n][$f] as $fixfield){
          $data[] = trim($fixfield);
        }
      }
    }
    if(!empty($data)){
      return $data;
    } else {
      return;
    }
  }
}
?>
