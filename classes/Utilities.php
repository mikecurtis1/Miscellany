<?php 

abstract class Utilities {

  public function get_url_contents($url, $timeout=5, &$errorString=NULL){
    if(filter_var($url, FILTER_VALIDATE_URL) === FALSE){
      $errorString = 'URL argument is not a valid URL: ' . $url;
      return FALSE;
    }
    if(filter_var($timeout, FILTER_VALIDATE_INT) === FALSE){
      $errorString = 'Timeout argument is not an integer: ' . $timeout;
      return FALSE;
    }
    $ctx = stream_context_create(array('http' => array('timeout' => $timeout))); // timeout in seconds
    $response = file_get_contents($url, 0, $ctx);
    if($response === FALSE){
      $errorString = 'PHP file_get_content timed out: ' . $url;
      return FALSE;
    }

    return $response;
  }
  
  public function setThisURLBase(){
    if(isset($_SERVER['HTTPS'])){
      $protocol = 'https://';
    } else {
      $protocol = 'http://';
    }
    if(isset($_SERVER['HTTP_HOST'])){
      $host = $_SERVER['HTTP_HOST'];
    } else {
      $host = '';
    }
    if(isset($_SERVER['REQUEST_URI'])){
      $uri = $_SERVER['REQUEST_URI'];
    } else {
      $uri = '';
    }
    $urlbase = $protocol.$host.$uri;
    
    return $urlbase;
  }
  
  public function setURL($key='',$value=''){
    $url = '';
    $array = (array) $this;
    if($key !== '' && $value !== ''){
      $array[$key] = $value;
    }
    if(!empty($array)){
      $urlbase = $this->setThisURLBase();
      $url = $urlbase.http_build_query($array,'','&');
    }
    
    return $url;
  }
  
  public function getValue($name=NULL){
    if(isset($this->$name)){
      return $this->$name;
    } else {
      return FALSE;
    }
  }
  
  public function echoHTML($name=NULL,$css=TRUE){
    $value = $this->getValue($name);
    if($value !== ''){
      if(preg_match("/^\d+$/",$value,$match)){
        $value = number_format($value);
      }
      if(filter_var($value, FILTER_VALIDATE_URL) !== FALSE){
        echo htmlspecialchars($value);
        return;
      }
      if($css===TRUE){
        echo '<span class="'.strtolower(get_class($this).'_'.$name).'">'.htmlspecialchars($value).'</span>';
        return;
      } else {
        echo htmlspecialchars($value);
        return;
      }
    }
    
    return;
  }
}
?>
