<?php 

require_once('MySql.php');

class IdsMySql extends MySql {

  public function __construct($host,$username,$passwd,$db){
    parent::__construct($host,$username,$passwd,$db);
  }

  public function closeDbConnection(){
    parent::closeDbConnection();
  }
  
  public function updateData($table,$field,$value,$id,$matchstring){
    parent::updateData($table,$field,$value,$id,$matchstring);
  }

  public function insertData($input,$table){
    parent::insertData($input,$table);
  }
  
  private function _normalizeYear($id,$matchstring,$string){
    $normalized = NULL;
    preg_match("/(\b|\D)(\d{4})(\b|\D)/",$string,$date_match);
    if(isset($date_match[2])){
      $normalized = trim($date_match[2]);
      $this->updateData('transactions','NormalizedYear',$normalized,$id,$matchstring);
    } else {
      $this->updateData('transactions','NormalizedYear','',$id,$matchstring);
    }
    
    return $normalized;
  }

  private function _normalizeNumber($id,$matchstring,$string){
    $normalized = NULL;
    $temp_string = preg_replace('/(?<=[0-9])[\-\s]+(?=[0-9xX])/',"", $string); // remove dashes and spaces between numbers and trailing x's
    preg_match("/\b([0-9]{9}[0-9xX]|[0-9]{12}[0-9xX])\b/",$temp_string,$isbn_match);
    preg_match("/\b([0-9]{7}[0-9xX])\b/",$temp_string,$issn_match);
    if(isset($isbn_match[1])){
      $normalized = trim($isbn_match[1]);
      $this->updateData('transactions','NormalizedISBN',$normalized,$id,$matchstring);
    }
    if(isset($issn_match[1])){
      $normalized = trim($issn_match[1]);
      $this->updateData('transactions','NormalizedISSN',$normalized,$id,$matchstring);
    }
    
    return $normalized;
  }

  public function normalizeYears($sql){
    if(($result = mysql_query($sql)) !== FALSE){
	    if(is_array(mysql_fetch_assoc($result))){
		    while($row = mysql_fetch_assoc($result)){
		      if($row['NormalizedYear'] == ''){
		        if($row['LoanDate'] != '' && $row['PhotoJournalYear'] == ''){
		          $this->_normalizeYear('id',$row['id'],$row['LoanDate']);
		        }
		        elseif($row['LoanDate'] == '' && $row['PhotoJournalYear'] != ''){
		          $this->_normalizeYear('id',$row['id'],$row['PhotoJournalYear']);
		        }
		        elseif($row['LoanDate'] != '' && $row['PhotoJournalYear'] != ''){
		          if($row['RequestType'] == 'Loan'){
		            $this->_normalizeYear('id',$row['id'],$row['LoanDate']);
		          }
		          elseif($row['RequestType'] == 'Article'){
		            $this->_normalizeYear('id',$row['id'],$row['PhotoJournalYear']);
		          }
		        }
		      } else {
		        $this->_normalizeYear('id',$row['id'],$row['NormalizedYear']);
		      }
		    }
	    } else {
		    return FALSE;
	    }
    } else {
	    return FALSE;
    }
    
    return FALSE;
  }
  
  public function normalizeNumbers($sql){
    if(($result = mysql_query($sql)) !== FALSE){
	    if(is_array(mysql_fetch_assoc($result))){
		    while($row = mysql_fetch_assoc($result)){
		      $this->_normalizeNumber('id',$row['id'],$row['ISSN']);
		    }
	    } else {
		    return FALSE;
	    }
    } else {
	    return FALSE;
    }
    
    return FALSE;
  }
  
  public function titleWords($sql,$title,$source,$source_id,$status){
    if(($result = mysql_query($sql)) !== FALSE){
	    if(is_array(mysql_fetch_assoc($result))){
		    while($row = mysql_fetch_assoc($result)){
		      $id = $row[$source_id];
		      if($status !== NULL && isset($row[$status])){
		        $transaction_status = $row[$status];
		      } else {
		        $transaction_status = 'no ILLiad status';
		      }      
			    $row[$title] = preg_replace('/\./', '', $row[$title]);
			    $row[$title] = preg_replace('/[\,\;\:\?\!\[\]\(\)\/]/', ' ', $row[$title]);
			    $row[$title] = preg_replace('/[\"\']/', '', $row[$title]);
			    $row[$title] = strtolower($row[$title]);
          $tempwords = explode(' ',$row[$title]);
			    foreach($tempwords as $word){
			      if($word != ''){
			        $input = array('titleword'=>$word, 'source'=>$source, 'source_id'=>$id, 'status'=>$transaction_status);
			        $this->insertData($input,'titlewords');
			      }
			    }
		    }
	    } else {
		    return FALSE;
	    }
    } else {
	    return FALSE;
    }
  }
  
}

?>
