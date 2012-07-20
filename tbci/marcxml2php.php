<?php
/*
 *
 * This class can parse both OAI MARC and MARC XML, and OpenRecord.
 *
 * It takes and XML URL or string, and an OpenURL rfr_id string
 *
 * It returns five PHP arrays.
 *
 * The five arrays contain the MARC records transformed 
 * into various data formats and WorldCat query data
 *
 * 1. PHP array
 * 2. Plain text
 * 3. Plain vanilla HTML
 * 4. OpenURL
 * 5. Query data
 *
 */
require_once("GetURL.php");
require_once("isbn.php");
require_once("oclc.php");
require_once("trailing_punctuation.php");

class marc {

  var $set_entry;  // Aleph specific
  var $doc_number; // Aleph specific
  var $metadata = array();
  var $query_data = array();
  var $html_array = array();
  var $plain_text_array = array();
  var $openurl_array = array();
  var $xml_parser;
  var $n = 0;
  var $c; // counter for repeating varfields/datafields

  function __construct() {
    $this->isbn_Object = new isbn();
    $this->oclc_Object = new oclc();
  }

  function parse($xml_url = null, $rfr_id = null, $xml = null, $use_001_for_oclc = false)
  {
    $this->rfr_id = $rfr_id;
    $this->use_001_for_oclc = $use_001_for_oclc;

    // if there is no XML URL and no XML string, return false
    if (($xml_url == null || $xml_url == "") && ($xml == null || $xml == "")) {
      return false;
    }

    $this->createXmlParser();

    // feed XML to parser
    if ($xml == null) {
      $xml = get_cached_url($xml_url);
    }


    // if the XML source is not well-formed, return an error
    // returning a link to the XML source can be useful for 
    // troubleshooting, but do not do this in production, because 
    // the URL will probably contain an API key
    if(!xml_parse($this->xml_parser, $xml)) {
      die("Error in XML source file on line " . xml_get_current_line_number($this->xml_parser));
    }

    $this->freeXmlParser();

    return array($this->metadata,$this->html_array,$this->plain_text_array,$this->openurl_array,$this->query_data);
  }

  function startHandler($parser, $name, $attrs){
    $this->temp_data = "";

    switch($name) {
    case 'fixfield':
      $this->tag_id = $attrs['id'];
      break;

    case 'controlfield':
      $this->tag_id = $attrs['tag'];
      break;

    case 'leader':
      $this->tag_id = "LEADER";
      break;

    case 'varfield':
      $this->tag_id = $attrs['id'];
      $this->i1 = $attrs['i1'];
      $this->i2 = $attrs['i2'];

      if ($this->i1 == " ") {
        $this->i1 = "_";
      }

      if ($this->i2 == " ") {
        $this->i2 = "_";
      }

      // this array push initially adds the tag to the metadata array
      // this must happen prior to any steps that add data to the 
      // metadata array at the varfield/datafield level or subfield level
      $this->metadata[$this->n]["MARC".$this->tag_id][] = array();
      break; 

    case 'datafield':
      $this->tag_id = $attrs['tag'];
      $this->i1 = $attrs['ind1'];
      $this->i2 = $attrs['ind2'];

      if ($this->i1 == " ") {
        $this->i1 = "_";
      }

      if ($this->i2 == " ") {
        $this->i2 = "_";
      }

      // this array push initially adds the tag to the metadata array
      // this must happen prior to any steps that add data to the 
      // metadata array at the varfield/datafield level or subfield level
      $this->metadata[$this->n]["MARC".$this->tag_id][] = array();
      break;

    // OAI MARC and MARCXML both use the XML element name 'subfield'
    // but they use 'label' and 'code' alternately in the subfield attribute
    case 'subfield':
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

  function dataHandler($parser, $data) {
    $data = trim($data,"\n,\r,\0");
    $this->temp_data .= $data;
  }

  function endHandler($parser, $name) {

    // WorldCat API specific, get numberofrecords
    switch($name) {
    case 'numberOfRecords':
      $this->number_of_records = $this->temp_data;
      $this->query_data['number_of_records'] = $this->number_of_records;
      $this->number_of_records = "";
      break;

    // WorldCat API specific, get nextRecordPosition
    case 'nextRecordPosition':
      $this->next_record_position = $this->temp_data;
      $this->query_data['next_record_position'] = $this->next_record_position;
      $this->next_record_position = "";
      break;

    // WorldCat API specific, get startRecord
    case 'startRecord':
      $this->start_record = $this->temp_data;
      $this->query_data['start_record'] = $this->start_record;
      $this->start_record = "";
      break;

    // WorldCat API specific, get query
    case 'query':
      $this->query = $this->temp_data;
      $this->query_data['query'] = $this->query;
      $this->query = "";
      break;

    // Aleph specific, add a document number
    // used for various hyperlinks
    case 'doc_number':
      $this->doc_number = $this->temp_data;
      $this->metadata[$this->n]['doc_number'] = $this->doc_number;
      $this->doc_number = "";
      break;
  
    // Aleph specific, add the search set entry number
    // used for numbering items on search results page
    case 'set_entry':
      $this->set_entry = $this->temp_data;
      $this->metadata[$this->n]['set_entry'] = $this->set_entry;
      $this->set_entry = "";
      break;
  
    // add fixfield/controlfield/leader
    // these don't have subfields so they can be 
    // added simply by push items to the metadata array
    case 'fixfield':
    case 'controlfield':
    case 'leader':
      $this->metadata[$this->n]["MARC".$this->tag_id][] = $this->temp_data;
      $this->temp_data = "";
      break;

    // data at the varfield/datafield level cannot be array pushed to metadata array
    // a counter "c" must be used to add them
    case 'varfield':
    case 'datafield':
      if (isset($this->metadata[$this->n]["MARC".$this->tag_id])) {
        $this->c = count($this->metadata[$this->n]["MARC".$this->tag_id])-1;
      }

      // the 'full' element is built by concatenation with white space
      // at the close of the varfield/datafield use trim to remove trailing white space
      $this->metadata[$this->n]["MARC".$this->tag_id][$this->c]['full'] = trim($this->metadata[$this->n]["MARC".$this->tag_id][$this->c]['full']);
      break;

    // add subfields
    // concatenate subfield data into 'full' element in metadata array
    case 'subfield':
      if ($this->temp_data != "") {
        if (isset($this->metadata[$this->n]["MARC".$this->tag_id])) {
          $this->c = count($this->metadata[$this->n]["MARC".$this->tag_id])-1;
        }
        // the following line adds the MARC indicators attrs array to 
        // the metadata array. Its placement here is only for the 
        // sake of human readability in the PHP array it could also occur
        // in the varfield/datafield closing
        $this->metadata[$this->n]["MARC".$this->tag_id][$this->c]['attrs'] = array("i1"=>$this->i1,"i2"=>$this->i2);
  
        // add subfield data
        $this->metadata[$this->n]["MARC".$this->tag_id][$this->c]['subfields'][$this->label][] = trim($this->temp_data);
        if (isset($this->metadata[$this->n]["MARC".$this->tag_id][$this->c]['full'])) {
          $this->metadata[$this->n]["MARC".$this->tag_id][$this->c]['full'] .= $this->temp_data." ";
        }
        else {
          $this->metadata[$this->n]["MARC".$this->tag_id][$this->c]['full'] = $this->temp_data." ";
        }
  
        // tag is closed, all processing finished, reset the temp_data variable
        $this->temp_data = "";
      }
      break;
  
    // this detects the end of a single MARC record
    // at this point the entire MARC record is in the PHP metadata array
    // more elaborate processing of the record is now possible, such as
    // creating an OpenURL based on the MARC data
    case 'oai_marc':
    case 'recordData':
    case 'opacRecord':
      $this->metadata[$this->n]['simple']['author'] = "";
      $this->metadata[$this->n]['simple']['title'] = "";
      $this->metadata[$this->n]['simple']['pubdate'] = "";
      $this->metadata[$this->n]['simple']['call_number'] = "";
      $this->metadata[$this->n]['simple']['lccn'] = "";
      $this->metadata[$this->n]['simple']['description'] = "";
      $this->metadata[$this->n]['simple']['issn'] = "";
      $this->metadata[$this->n]['simple']['isbn_first'] = "";
      $this->metadata[$this->n]['simple']['isbn10_first'] = "";
    
      // begin gathering OpenURL data
      unset($this->temp_openurl);
    
      // openurl type
      $this->temp_openurl[] = "ctx_ver=Z39.88-2004";
      $this->temp_openurl[] = "rft_val_fmt=".urlencode("info:ofi/fmt:kev:mtx:book");
      $this->temp_openurl[] = "rft.genre=book";
    
      // referrer id
      // origin description http://alcme.oclc.org/openurl/docs/pdf/openurl-01.pdf
      $this->temp_openurl[] = "rfr_id=info:sid/".$this->rfr_id;
    
      // add local identifiers to the openurl
      // local identifier zone http://alcme.oclc.org/openurl/docs/pdf/openurl-01.pdf
        if (isset($this->metadata[$this->n]['MARC001'][0]))
        {
        $pid_value[] = "MARC_001=".$this->metadata[$this->n]['MARC001'][0];
        }
        if (isset($this->metadata[$this->n]['MARC035'][0]['subfields']['a'][0]))
        {
        $pid_value[] = "MARC_035=".trim($this->metadata[$this->n]['MARC035'][0]['subfields']['a'][0]);
        // use oclc class to find OCLC numbers
        $oclc_number = "";
        $oclc_number = $this->oclc_Object->find($this->metadata[$this->n]['MARC035'],"first");
          if ($oclc_number != "")
          {
          $pid_value[] = "OCLC_NUMBER=".$oclc_number;
          $this->metadata[$this->n]['simple']['oclc_number'] = $oclc_number;
          $this->metadata[$this->n]['simple']['standard_numbers']["oclc:".$oclc_number] = "oclc:".$oclc_number;
          }
        }
        if (isset($this->metadata[$this->n]['doc_number']))
        {
        $pid_value[] = "ALEPH_SYS=".$this->metadata[$this->n]['doc_number'];
        }
        if (is_array($pid_value))
        {
        $this->temp_openurl[] = "pid=".urlencode(implode("&",$pid_value));
        }
    
        // check in both tag 100 and 700 for author data
        // add both OpenURL value and simple 'author' element to metadata array
        if (isset($this->metadata[$this->n]['MARC100'][0]['subfields']['a'][0]))
        {
        $temp_author = $this->metadata[$this->n]['MARC100'][0]['subfields']['a'][0];
        $this->metadata[$this->n]['simple']['author'] = trim($temp_author);
        $this->temp_openurl[] = "rft.au=".urlencode(trim($temp_author));
        }
        else
        {
          if (isset($this->metadata[$this->n]['MARC700'][0]['subfields']['a'][0]))
          {
          $this->metadata[$this->n]['simple']['author'] = trim($this->metadata[$this->n]['MARC700'][0]['subfields']['a'][0]);
          $this->temp_openurl[] = "rft.au=".urlencode(trim($this->metadata[$this->n]['MARC700'][0]['subfields']['a'][0]));
          }
          else
          {
          $this->metadata[$this->n]['simple']['author'] = "";
          $this->temp_openurl[] = "rft.au=unknown";
          }
        }
    
        // add standard numbers to simple array
        if ($this->use_001_for_oclc==true)
        {
          if (isset($this->metadata[$this->n]['MARC001'][0]))
          {
          $oclc_number = trim($this->metadata[$this->n]['MARC001'][0]);
          $this->metadata[$this->n]['simple']['standard_numbers']["oclc:".$oclc_number] = "oclc:".$oclc_number;
          $this->metadata[$this->n]['simple']['oclc_number'] = $oclc_number;
          }
        }
        if (isset($this->metadata[$this->n]['MARC010'][0]['subfields']['a']))
        {
          foreach($this->metadata[$this->n]['MARC010'][0]['subfields']['a'] as $i => $v)
          {
          $v = trim($v);
          $this->metadata[$this->n]['simple']['standard_numbers']["lccn:".$v] = "lccn:".$v;
          }
        }

        if (isset($this->metadata[$this->n]['MARC019'][0]['subfields']['a'])) {
          foreach ($this->metadata[$this->n]['MARC019'][0]['subfields']['a'] as $v) {
            $v = trim($v);
            $this->metadata[$this->n]['simple']['standard_numbers']["oclc:{$v}"] = "oclc:{$v}";
          }
        }

        // ISBN is found in MARC tag 020 subfield a
        // but it usually contains notes that make the data messy
        // use isbn class to grab only a clean ISBN value
        if (isset($this->metadata[$this->n]['MARC020'][0]['subfields']['a'][0]))
        {
        $this->isbn_value = $this->isbn_Object->find($this->metadata[$this->n]['MARC020'][0]['subfields']['a'][0]);
        $this->temp_openurl[] = "rft.isbn=".urlencode(trim($this->isbn_value));
        // also add simple 'isbn_first' and 'isbn10_first' elements to metadata array
        $this->metadata[$this->n]['simple']['isbn_first'] = $this->isbn_Object->find($this->metadata[$this->n]['MARC020']);
        $this->metadata[$this->n]['simple']['isbn10_first'] = $this->isbn_Object->find($this->metadata[$this->n]['MARC020'],"10_first");
          foreach($this->isbn_Object->find($this->metadata[$this->n]['MARC020'],"all") as $i => $v)
          {
          $v = trim($v);
          $this->metadata[$this->n]['simple']['standard_numbers']["isbn:".$v] = "isbn:".$v;
          }
        }
        
        // ADD ISSN to standard numbers
        if (isset($this->metadata[$this->n]['MARC022'][0]['subfields']['a'][0]))
        {
          $this->temp_issn = trim($this->metadata[$this->n]['MARC022'][0]['subfields']['a'][0]);
          $this->metadata[$this->n]['simple']['standard_numbers']["issn:".$this->temp_issn] = "issn:".$this->temp_issn;
        }
        
    
        // add other OpenURL data
        // also add 'simple' metadata elements
        if (isset($this->metadata[$this->n]['MARC245'][0]['full']))
        {
        $this->temp_openurl[] = "rft.title=".urlencode(trim($this->metadata[$this->n]['MARC245'][0]['full']));
        $this->metadata[$this->n]['simple']['title'] = trim($this->metadata[$this->n]['MARC245'][0]['full']);
        }
        if (isset($this->metadata[$this->n]['MARC260'][0]['subfields']['c'][0]))
        {
        $this->temp_openurl[] = "rft.date=".urlencode(trim($this->metadata[$this->n]['MARC260'][0]['subfields']['c'][0]));
        $this->temp_pubdate = remove_trailing_punctuation($this->metadata[$this->n]['MARC260'][0]['subfields']['c'][0]);
        $this->metadata[$this->n]['simple']['pubdate'] = trim($this->temp_pubdate);
        }
        if (isset($this->metadata[$this->n]['MARC260'][0]['subfields']['b'][0]))
        {
        $this->temp_openurl[] = "rft.pub=".urlencode(trim($this->metadata[$this->n]['MARC260'][0]['subfields']['b'][0]));
        }
    
        // add call number element to the 'simple' metadata array
        if (isset($this->metadata[$this->n]['MARC050'][0]['full']))
        {
        $this->metadata[$this->n]['simple']['call_number'] = $this->metadata[$this->n]['MARC050'][0]['full'];
        }
        if (isset($this->metadata[$this->n]['MARC090'][0]['full']))
        {
        $this->metadata[$this->n]['simple']['call_number'] = $this->metadata[$this->n]['MARC090'][0]['full'];
        }
        if (isset($this->metadata[$this->n]['MARC099'][0]['full']))
        {
        $this->metadata[$this->n]['simple']['call_number'] = $this->metadata[$this->n]['MARC099'][0]['full'];
        }
    
        // add ISSN to 'simple'
        if (isset($this->metadata[$this->n]['MARC022'][0]['subfields']['a'][0]))
        {
        $this->temp_issn = trim($this->metadata[$this->n]['MARC022'][0]['subfields']['a'][0]);
        $this->metadata[$this->n]['simple']['issn'] = $this->temp_issn;
        }
        
        // add Library of Congress Control Number (LCCN) to 'simple'
        if (isset($this->metadata[$this->n]['MARC010'][0]['subfields']['a'][0]))
        {
        $this->temp_lccn = trim($this->metadata[$this->n]['MARC010'][0]['subfields']['a'][0]);
        $this->metadata[$this->n]['simple']['lccn'] = $this->temp_lccn;
        }
    
        // add title/subtitle to 'simple'
		$marc_245_subfields = array('a','b','n','p');
		foreach($marc_245_subfields as $marc_245_subfield){
			if(isset($this->metadata[$this->n]['MARC245'][0]['subfields'][$marc_245_subfield][0]))
			{
			$this->temp_title .= " ".trim($this->metadata[$this->n]['MARC245'][0]['subfields'][$marc_245_subfield][0]);
			}
		}
      $this->temp_title = remove_trailing_punctuation($this->temp_title);
      $this->metadata[$this->n]['simple']['title'] = $this->temp_title;
      $this->temp_title = "";

        // add description to 'simple'
        if (isset($this->metadata[$this->n]['MARC520'][0]['full']))
        {
        $this->temp_description .= trim($this->metadata[$this->n]['MARC520'][0]['full'])." ";
        }
        if (isset($this->metadata[$this->n]['MARC505'][0]['full']))
        {
        $this->temp_description .= trim($this->metadata[$this->n]['MARC505'][0]['full'])." ";
        }
        if (isset($this->metadata[$this->n]['MARC650']))
        {
          foreach($this->metadata[$this->n]['MARC650'] as $index => $value)
          {
          $this->temp_description .= $value['subfields']['a'][0]." ";
          }
        }
        if (isset($this->temp_description))
        {
        $this->metadata[$this->n]['simple']['description'] = trim($this->temp_description);
        $this->temp_description = "";
        }
        
        if (isset($this->metadata[$this->n]['MARC856']))
        {
          foreach($this->metadata[$this->n]['MARC856'] as $index => $value)
          {
            if (isset($this->metadata[$this->n]['MARC856'][$index]['subfields']['u'][0]))
            {
            $url = $this->metadata[$this->n]['MARC856'][$index]['subfields']['u'][0];
            $link_text = $url;
            $class = "marc_856";
              if (isset($this->metadata[$this->n]['MARC856'][$index]['subfields']['3'][0]))
              {
              $link_text = $this->metadata[$this->n]['MARC856'][$index]['subfields']['3'][0];
              $class = "marc_856 marc_856_".strtolower(strtr($this->metadata[$this->n]['MARC856'][$index]['subfields']['3'][0]," ","_"));
              }
            $this->metadata[$this->n]['MARC856'][$index]['anchor'] = "<li class=\"".htmlspecialchars($class)."\"><a href=\"".htmlspecialchars($url)."\">".htmlspecialchars($link_text)."</a></li>";
            }
          }
        }

      // add OpenURL to array
      $this->openurl_array[$this->n] = implode("&amp;",$this->temp_openurl);

      // end of all processing for the MARC record
      // autoincrement the metadata array counter
      $this->n++;
      break;

    }

  }
  private function createXmlParser() {
    $this->xml_parser = xml_parser_create();
    xml_set_object($this->xml_parser,$this);
    xml_set_element_handler($this->xml_parser, "startHandler", "endHandler");
    xml_set_character_data_handler($this->xml_parser, "dataHandler");
    xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, false);
    xml_parser_set_option($this->xml_parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
  }

  private function freeXmlParser() {
    xml_parser_free($this->xml_parser);
  }
}
?>

