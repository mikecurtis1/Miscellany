<?php 

include_once('QueryToken.php');

class QueryParser 
{
  private $op_escape;
  private $op_prefix;
  private $op_index_separator;
  private $op_phrase_quote;
  private $op_token_delimiter;
  private $token_delimiter_replacement;
  private $tokens;

  public function __construct(){
    $this->op_escape = '\\';
    $this->op_prefix = array('+','-','|');
    $this->op_index_separator = ':';
    $this->op_phrase_quote = '"';
    $this->op_token_delimiter = ' ';
    $this->token_delimiter_replacement = chr(31); // chr(31) non-print ascii "unit separator" will never be user input
    $this->tokens = array(); // see http://en.wikipedia.org/wiki/Tokenization, http://nlp.stanford.edu/IR-book/html/htmledition/tokenization-1.html
  }
  
  public function parseQuery($query=''){
    //TODO: add a cache function. store serialized tokens array, cache id by md5 hash of query
    $normalized = $this->_normalizeWhiteSpace($query);
    $tokenized = $this->_tokenizeQuotedPhrases($normalized);
    $this->_setTokens($tokenized);
    $this->_parseTokens();
    $this->_cleanTokensIndex();
    $this->_cleanTokensText();
    
    return $this->tokens;
  }
  
  private function _normalizeWhiteSpace($string=''){
    $string = preg_replace('/\s{2,}/', ' ', $string);
    $string = trim($string);
    
    return $string;
  }
  
  private function _isEscaped($i=0,$array=array()){
    $escaped = FALSE;
    $c = $this->_countEscapeChars($i,$array);
    if ( $c % 2 !== 0 ) {
      $escaped = TRUE;
    }
    
    return $escaped;
  }
  
  private function _countEscapeChars($i=0,$array=array()){
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
    
    return $c;
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
      if ( ( $quoted === TRUE ) && ( $char == $this->op_token_delimiter ) ) {
        $array[$i] = $this->token_delimiter_replacement;
      }
    }
    
    return implode('',$array);
  }
  
  private function _setTokens($string){
    $temp = explode($this->op_token_delimiter,$string);
    foreach ( $temp as $i => $v ) {
      $this->tokens[] = new QueryToken(NULL,NULL,$v,FALSE);
    }
    
    return;
  }
  
  private function _updateToken($i=0,$prefix=NULL,$index=NULL,$text=NULL,$phrase=FALSE){
    if ( isset($this->tokens{$i}) ) {
      $this->tokens{$i}->prefix = $prefix;
      $this->tokens{$i}->index = $index;
      $this->tokens{$i}->text = $text;
      $this->tokens{$i}->phrase = $phrase;
    }
    
    return;
  }
  
  private function _parseTokens(){
    foreach ( $this->tokens as $i => $e ) {
      list($prefix,$index,$text,$phrase) = $this->_parseToken($e);
      $this->_updateToken($i,$prefix,$index,$text,$phrase);
    }
    
    return $this->tokens;
  }
  
  private function _parseToken($e){
    $string = $e->text;
    $prefix = $this->_getTokenPrefix($string);
    $index = $this->_getTokenIndex($string);
    $text = $this->_getTokenText($string);
    $phrase = $this->_getTokenPhrase($text);
    
    return array($prefix,$index,$text,$phrase);
  }
  
  private function _getTokenPrefix($string){
    $prefix = substr($string,0,1);
    if ( $this->_isPrefixOperator($prefix) === FALSE ) {
      $prefix = NULL;
    }
    
    return $prefix;
  }
  
  private function _getTokenIndex($string){
    list($index,$text) = $this->_splitOnIndexOp($string);
    return $index;
  }
  
  private function _getTokenText($string){
    list($index,$text) = $this->_splitOnIndexOp($string);
    return $text;
  }
  
  private function _getTokenPhrase($text){
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
  
  private function _cleanTokensIndex(){
    foreach ( $this->tokens as $i => $e ) {
      $index = $e->index;
      $index = $this->_removePrefixOperators($index);
      $index = $this->_removeEscapeChars($index);
      $this->_updateToken($i,$e->prefix,$index,$e->text,$e->phrase);
    }
    
    return;
  }
  
  private function _cleanTokensText(){
    foreach ( $this->tokens as $i => $e ) {
      $text = $e->text;
      $text = $this->_removePrefixOperators($text);
      $text = $this->_removeTokenDelimiterReplacement($text);
      $text = $this->_removePhraseQuotes($text);
      $text = $this->_removeEscapeChars($text);
      $this->_updateToken($i,$e->prefix,$e->index,$text,$e->phrase);
    }
    
    return;
  }
  
  private function _removeTokenDelimiterReplacement($string=''){
    $string = str_replace($this->token_delimiter_replacement, $this->op_token_delimiter, $string);
    
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
