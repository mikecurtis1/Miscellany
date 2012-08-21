<?php 

include_once('QueryElement.php');

class QueryParser 
{
  private $op_escape;
  private $op_prefix;
  private $op_index_separator;
  private $op_phrase_quote;
  private $op_element_delimiter;
  private $op_phrase_separator; 
  private $phrase_separator_token;
  public $query;
  public $elements;

  public function __construct(){
    $this->op_escape = '\\';
    $this->op_prefix = array('+','-','|');
    $this->op_index_separator = ':';
    $this->op_phrase_quote = '"';
    $this->op_element_delimiter = ' ';
    $this->op_phrase_separator = ' ';
    $this->phrase_separator_token = chr(31); // chr(31) non-print ascii "unit separator" will never be user input
    $this->query = '';
    $this->elements = array();
  }
  
  public function parseQuery($query=''){
    //TODO: add a cache function. store serialized elements array, cache id by md5 hash of query
    $this->query = $this->_normalizeWhiteSpace($query);
    $tokenized = $this->_tokenizeQuotedPhrases($this->query);
    $this->_setSearchElements($tokenized);
    $this->_parseSearchElements();
    $this->_cleanElementsIndex();
    $this->_cleanElementsText();
    
    return $this->elements;
  }
  
  private function _normalizeWhiteSpace($string=''){
    $string = preg_replace('/\s{2,}/', ' ', $string);
    $string = trim($string);
    
    return $string;
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
  
  private function _tokenizeQuotedPhrases($string){
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
      $this->elements[] = $elemObj = new QueryElement(NULL,NULL,$v,FALSE);
    }
    
    return;
  }
  
  private function _updateSearchElement($i=0,$prefix=NULL,$index=NULL,$text=NULL,$phrase=FALSE){
    if ( isset($this->elements{$i}) ) {
      $this->elements{$i}->prefix = $prefix;
      $this->elements{$i}->index = $index;
      $this->elements{$i}->text = $text;
      $this->elements{$i}->phrase = $phrase;
    }
    
    return;
  }
  
  private function _parseSearchElements(){
    foreach ( $this->elements as $i => $e ) {
      list($prefix,$index,$text,$phrase) = $this->_parseSearchElement($e);
      $this->_updateSearchElement($i,$prefix,$index,$text,$phrase);
    }
    
    return $this->elements;
  }
  
  private function _parseSearchElement($e){
    $string = $e->text;
    $prefix = $this->_getElementPrefix($string);
    $index = $this->_getElementIndex($string);
    $text = $this->_getElementText($string);
    $phrase = $this->_getElementPhrase($text);
    
    return array($prefix,$index,$text,$phrase);
  }
  
  private function _getElementPrefix($string){
    $prefix = substr($string,0,1);
    if ( $this->_isPrefixOperator($prefix) === FALSE ) {
      $prefix = NULL;
    }
    
    return $prefix;
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
  
  private function _isPrefixOperator($string=''){
    $prefix = FALSE;
    foreach ( $this->op_prefix as $i => $p ) {
      if ( $string === $p ) {
        $prefix = TRUE;
      }
    }
    
    return $prefix;
  }
  
  private function _isQuotedPhrase($string=''){
    $quoted = FALSE;
    if ( ( substr($string,0,1) === $this->op_phrase_quote ) && ( substr($string,-1) === $this->op_phrase_quote ) ) {
      $quoted = TRUE;
    }
    
    return $quoted;
  }
  
  private function _splitOnIndexOp($string=''){
    $index = '';
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
      $index = $this->_removePrefixOperators($index);
      $index = $this->_removeEscapeChars($index);
      $this->_updateSearchElement($i,$e->prefix,$index,$e->text,$e->phrase);
    }
    
    return;
  }
  
  private function _cleanElementsText(){
    foreach ( $this->elements as $i => $e ) {
      $text = $e->text;
      $text = $this->_removePrefixOperators($text);
      $text = $this->_removePhraseSeparatorTokens($text);
      $text = $this->_removePhraseQuotes($text);
      $text = $this->_removeEscapeChars($text);
      $this->_updateSearchElement($i,$e->prefix,$e->index,$text,$e->phrase);
    }
    
    return;
  }
  
  private function _removePhraseSeparatorTokens($string=''){
    $string = str_replace($this->phrase_separator_token, $this->op_phrase_separator, $string);
    
    return $string;
  }
  
  private function _removePrefixOperators($string=''){
    if ( $this->_isPrefixOperator(substr($string,0,1)) === TRUE ) {
      return substr($string,1);
    } else {
      return $string;
    }
  }
  
  private function _removePhraseQuotes($string=''){
    if ( $this->_isQuotedPhrase($string) === TRUE ) {
      $string = substr($string,1,-1);
    }
    
    return trim($string);
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
