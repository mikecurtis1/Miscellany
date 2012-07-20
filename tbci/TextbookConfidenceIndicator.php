<?php 
class TextbookConfidenceIndicator
{
  private $weights = array();  
  private $item_score = 0;
  private $textbook_confidence_indicator = 0;
  
  //OPTIMIZE: too many parameter in one method?
  public function __construct(){
    //TODO: method for customizing values for each library
    $this->edition_number_threshold = 3;
    $this->year_range = 1;
    $this->publishers = array('MacMillan','McGraw-Hill','Wiley','Houghton Mifflin','W. W. Norton','Pearson','Addison-Wesley','Prentice Hall','Jones & Bartlett','Longman');
    $this->isbns = array();
    $this->isbn_match = FALSE;
    $this->isbn_match_is_perfect = FALSE;
    $this->weights['no_personal_author'] = 75;
    $this->weights['title_phrase_match'] = 200;
    $this->weights['overpriced'] = 25;
    $this->weights['form_textbook'] = 85;
    $this->weights['form_fiction'] = -50;
    $this->weights['topics_textbook'] = 70;
    $this->weights['topics_popular_works'] = -50;
    $this->weights['edition'] = 80;
    $this->weights['edition_over_threshold'] = 100;
    $this->weights['publisher_name_match'] = 50;
    $this->weights['pubdate_range_match'] = 100;
    #$this->weights['found_at_textbook_seller'] = 100;
    #$this->weights['isbn_match'] = 0;
    $this->weights_total = array_sum($this->weights);
  }

  // run all checks to score the item, then calculate a TBCI value
  public function getConfidenceIndicator($item){
    $this->_checkNoPersonalAuthor($item);
    $this->_checkTitlePhrase($item);
    $this->_checkOverpriced($item);
    $this->_checkFormTextbook($item);
    $this->_checkFormFiction($item);
    $this->_checkTopicsTextbook($item);
    $this->_checkTopicsPopularWorks($item);
    $this->_checkEdition($item);
    $this->_checkEditionOverThreshold($item);
    $this->_checkPublisherNameMatch($item);
    $this->_checkPubdateRangeMatch($item);
    $this->_calculateConfidenceIndicator();

    return $this->textbook_confidence_indicator;
  }
  
  // calculate confidence indicator
  // REVIEW: does it make sense to calculate a value that will always be 0 to 1?
  private function _calculateConfidenceIndicator(){
    if($this->weights_total != 0){
      $temp_float = ($this->item_score / $this->weights_total);
      $this->textbook_confidence_indicator = round($temp_float,2,PHP_ROUND_HALF_DOWN);
    }
  }

  // methods for scoring item
  private function _checkNoPersonalAuthor($item){
    #if($item['author'] == '' && (strpos($item['title'],'edited by') !== FALSE)){
    if($item['author'] == '' && preg_match('/edited by|editor|edited|\[et\.? al\.?\]/i',$item['title'],$match)){ //TODO: move regex string into construct
      if(isset($this->weights['no_personal_author'])){
        $this->item_score += $this->weights['no_personal_author'];
      }
    }
  }
  
  private function _checkTitlePhrase($item){
    if(preg_match('/textbook|introduction to |an introduction|foundations of |fundamentals of |essentials of |principles of /i',$item['title'],$match)){
      if(isset($this->weights['title_phrase_match'])){
        $this->item_score += $this->weights['title_phrase_match'];
      }
    }
  }
  
  private function _checkOverpriced($item){
    if(preg_match_all('/([\d\.]+)/',$item['price'],$matches)){
      rsort($matches[1]);
      if($matches[1][0] > 50){ //TODO: move this into construct method
        if(isset($this->weights['overpriced'])){
          $this->item_score += $this->weights['overpriced'];
        }
      }
    }
  }

  private function _checkFormTextbook($item){
    if(preg_match('/'.$this->topic_match_string.'/i',$item['form'],$match)){
      if(isset($this->weights['form_textbook'])){
        $this->item_score += $this->weights['form'];
      }
    }
  }

  private function _checkFormFiction($item){
    if(preg_match('/fiction/i',$item['form'],$match)){
      if(isset($this->weights['form_fiction'])){
        $this->item_score += $this->weights['form'];
      }
    }
  }

  private function _checkTopicsTextbook($item){
    if(preg_match('/textbook/i',$item['topics'],$match)){
      if(isset($this->weights['topics_textbook'])){
        $this->item_score += $this->weights['topics_textbook'];
      }
    }
  }

  private function _checkTopicsPopularWorks($item){
    if(preg_match('/popular works/i',$item['topics'],$match)){
      if(isset($this->weights['topics_popular_works'])){
        $this->item_score += $this->weights['topics_popular_works'];
      }
    }
  }

  private function _checkEdition($item){
    if($item['edition'] != ''){
      if(isset($this->weights['edition'])){
        $this->item_score += $this->weights['edition'];
      }
    }
  }

  private function _checkEditionOverThreshold($item){
    #if(preg_match('/(\d+)/',$item['edition'],$match)){ //TODO: move regex string into construct
    if(preg_match('/(\d+).*? ed/',$item['edition'],$match)){ // stricter match
      if(isset($match[1]) && $match[1] > $this->edition_number_threshold){
        if(isset($this->weights['edition_over_threshold'])){
          $this->item_score += $this->weights['edition_over_threshold'];
        }
      }
    }
  }

  private function _checkPublisherNameMatch($item){
    foreach($this->publishers as $publisher){
      if(strpos($item['publisher'], $publisher) !== FALSE){
        if(isset($this->weights['publisher_name_match'])){
          $this->item_score += $this->weights['publisher_name_match'];
        }
      }
    }
  }

  private function _checkPubdateRangeMatch($item){ //TODO: move regex string into construct
    if(preg_match('/(19|20\d\d)/',$item['pubdate'],$match)){ // match only numbers that look like 20th or 21st century years
      $start_year = date("Y")+1;
      $end_year = $start_year - $this->year_range;
      if(($match[1] <= $start_year) && ($match[1] >= $end_year)){
        if(isset($this->weights['pubdate_range_match'])){
        $this->item_score += $this->weights['pubdate_range_match'];
        }
      }
    }
  }

    /*
    //TODO: make a proper isbn list check method
    foreach($this->isbns as $isbn){
      if(strpos($item['isbns'], $isbn) !== FALSE){
      $this->isbn_match = TRUE;
        if(isset($this->weights['isbn_match'])){
          $item_score += $this->weights['isbn_match'];
        }
      }
    }

    $weights_total = 0;
    // optional perfect score for isbn match
    if($this->isbn_match_is_perfect === TRUE && $this->isbn_match === TRUE){
      #$weights_total = $weights_total - $this->weights['isbn_match'];
      #$item_score = $weights_total;
      $diff = $weights_total - $item_score;
      $this->weights['isbn_match'] = $diff;
      $item_score += $diff;
    }*/
    
}



?>
