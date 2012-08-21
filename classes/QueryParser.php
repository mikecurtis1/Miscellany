<?php 

include_once('QueryElement.php');

class QueryParser 
{
  private $op_escape;
  private $op_index_separator;
  private $op_optional;
  private $op_excluded;
  private $op_required;
  private $op_phrase_quote;
  private $op_element_delimiter;
  private $op_phrase_separator; 
  private $phrase_separator_token;
  private $default_index;
  public $query;
  public $elements;

  public function __construct($query=''){
    $this->op_escape = '\\';
    $this->op_index_separator = ':';
    $this->op_optional = '|';
    $this->op_excluded = '-';
    $this->op_required = '+';
    $this->op_phrase_quote = '"';
    $this->op_element_delimiter = ' ';
    $this->op_phrase_separator = ' ';
    $this->phrase_separator_token = chr(31); // chr(31) unit separator, non-print ascii, will never be user input ; custom token <:SPACE:> ; quick and dirty... and error prone '_'
    $this->default_index = 'kw';
    $this->query = $this->_normalizeWhiteSpace($query);
    $this->elements = array();
  }
  
  private function _normalizeWhiteSpace($string=''){
    $string = trim($string);
    $string = str_replace("\r\n", ' ', $string);
    $string = str_replace('  ', ' ', $string);
    
    return $string;
  }
  
  public function parseQuery(){
    $cache = $this->_checkCache($this->query);
    if ( $cache === FALSE ) {
      $converted = $this->_convertSpacesInQuotedPhrases($this->query);
      $this->_setSearchElements($converted);
      $this->_parseSearchElements();
      $this->_cleanElementsIndex();
      $this->_cleanElementsText();
    } else {
      $this->elements = $cache;
    }
    
    return $this->elements;
  }
  
  private function _checkCache($query){
    //TODO: some function that checks cache by query, return unserialized elements object
    $unserialized_object = FALSE;
    // $unserialized_object = $this->_getUnserializedObjectFromCache(md5($query));
    
    return $unserialized_object;
  }
  
  private function _isEscaped($i=0,$array=array()){
    $escaped = FALSE;
    $pi = $i-1;
    $prev = NULL;
    $c = 0;
    while ( $pi >= 0 ) {
      if ( isset($array[$pi]) ) {
        $prev = $array[$pi];
      }
      if ( $prev !== $this->op_escape ) {
        break;
      }
      $pi--;
      $c++;
    }
    if ( $c % 2 !== 0 ) {
      $escaped = TRUE;
    }
    
    return $escaped;
  }
  
  private function _convertSpacesInQuotedPhrases($string){
    $array = str_split($string);
    $quoted = FALSE;
    foreach ( $array as $i => $char ) {
      if ( ( $char === $this->op_phrase_quote ) && ( $quoted === FALSE ) && ( $this->_isEscaped($i,$array) === FALSE ) ) {
        $quoted = TRUE;
      } elseif ( ( $char === $this->op_phrase_quote ) && ( $quoted === TRUE ) && ( $this->_isEscaped($i,$array) === FALSE ) ) {
        $quoted = FALSE;
      }
      if ( ( $quoted === TRUE ) && ( $char == $this->op_phrase_separator ) ) {
        $array[$i] = $this->phrase_separator_token;
      }
    }
    
    return implode('',$array);
  }
  
  private function _setSearchElements($string){
    $temp = explode($this->op_element_delimiter,$string);
    foreach ( $temp as $i => $v ) {
      $this->elements[] = $elemObj = new QueryElement(NULL,NULL,NULL,$v,FALSE);
    }
    
    return;
  }
  
  private function _updateSearchElement($i=0,$boole=NULL,$operator=NULL,$index=NULL,$text=NULL,$phrase=FALSE){
    if ( isset($this->elements{$i}) ) {
      $this->elements{$i}->boole = $boole;
      $this->elements{$i}->operator = $operator;
      $this->elements{$i}->index = $index;
      $this->elements{$i}->text = $text;
      $this->elements{$i}->phrase = $phrase;
    }
    
    return;
  }
  
  private function _parseSearchElements(){
    foreach ( $this->elements as $i => $e ) {
      list($op,$boole,$index,$text,$phrase) = $this->_parseSearchElement($e);
      $this->_updateSearchElement($i,$boole,$op,$index,$text,$phrase);
    }
    
    return $this->elements;
  }
  
  private function _parseSearchElement($e){
    $string = $e->text;
    $op = $this->_getElementOperator($string);
    $boole = $this->_getElementBoole($op);
    $index = $this->_getElementIndex($string);
    $text = $this->_getElementText($string);
    $phrase = $this->_getElementPhrase($text);
    
    return array($op,$boole,$index,$text,$phrase);
  }
  
  private function _getElementOperator($string){
    $op = substr($string,0,1);
    if ( $this->_isOperator($op) === FALSE ) {
      $op = NULL;
    }
    
    return $op;
  }
  
  private function _getElementBoole($op){
    if ( $op === $this->op_optional ) {
      $boole = 'optional';
    } elseif ( $op === $this->op_excluded ) {
      $boole = 'excluded';
    } elseif ( $op === $this->op_required ) {
      $boole = 'required';
    } else {
      $boole = 'included';
    }
    
    return $boole;
  }
  
  private function _getElementIndex($string){
    list($index,$text) = $this->_splitOnIndexOp($string);
    return $index;
  }
  
  private function _getElementText($string){
    list($index,$text) = $this->_splitOnIndexOp($string);
    return $text;
  }
  
  private function _getElementPhrase($text){
    $phrase = $this->_isQuotedPhrase($text);
    return $phrase;
  }
  
  private function _isOperator($string=''){
    $operator = FALSE;
    if ( ( $string === $this->op_optional ) || ( $string === $this->op_excluded ) || ( $string === $this->op_required ) ) {
      $operator = TRUE;
    }
    
    return $operator;
  }
  
  private function _isQuotedPhrase($string=''){
    $quoted = FALSE;
    if ( ( substr($string,0,1) === $this->op_phrase_quote ) && ( substr($string,-1) === $this->op_phrase_quote ) ) {
      $quoted = TRUE;
    }
    
    return $quoted;
  }
  
  private function _splitOnIndexOp($string=''){
    $index = $this->default_index;
    $text = $string;
    $array = str_split($string);
    foreach ( $array as $i => $char ) {
      if ( $char === $this->op_index_separator && $this->_isEscaped($i,$array) === FALSE ) {
        $index = substr($string,0,$i);
        $text = substr($string,$i+1);
        break;
      }
    }
    
    return array($index,$text);
  }
  
  private function _cleanElementsIndex(){
    foreach ( $this->elements as $i => $e ) {
      $index = $e->index;
      $index = $this->_removeBooleanOperators($index);
      $index = $this->_removeEscapeChars($index);
      $this->_updateSearchElement($i,$e->boole,$e->operator,$index,$e->text,$e->phrase);
    }
    
    return;
  }
  
  private function _cleanElementsText(){
    foreach ( $this->elements as $i => $e ) {
      $text = $e->text;
      $text = $this->_removePhraseSeparatorTokens($text);
      $text = $this->_removePhraseQuotes($text);
      $text = $this->_removeEscapeChars($text);
      $this->_updateSearchElement($i,$e->boole,$e->operator,$e->index,$text,$e->phrase);
    }
    
    return;
  }
  
  private function _removePhraseSeparatorTokens($string=''){
    $string = str_replace($this->phrase_separator_token, $this->op_phrase_separator, $string);
    
    return $string;
  }
  
  private function _removeBooleanOperators($string=''){
    if ( $this->_isOperator(substr($string,0,1)) === TRUE ) {
      return substr($string,1);
    } else {
      return $string;
    }
  }
  
  private function _removePhraseQuotes($string=''){
    if ( $this->_isQuotedPhrase($string) === TRUE ) {
      return substr($string,1,-1);
    } else {
      return $string;
    }
  }
  
  private function _removeEscapeChars($string=''){
    $array = str_split($string);
    $escape_pos = array();
    foreach ($array as $i => $char) {
      if ( ( $char === $this->op_escape ) && ( $this->_isEscaped($i,$array) === FALSE ) ) {
        $escape_pos[] = $i;
      }
    }
    foreach ($escape_pos as $pos) {
      $array[$pos] = '';
    }
    
    return implode('',$array);
  }
}
?>
