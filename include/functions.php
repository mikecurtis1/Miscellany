<?php 

  function get_url_contents($url, $timeout=2, &$errorString=NULL){
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
  
?>
