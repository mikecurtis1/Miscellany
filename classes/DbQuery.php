<?php 

class DbQuery
{
	private $_count = NULL;
	private $_results = array();
	private $_fields = array();
	private $_affected_rows = NULL;
	private $_insert_id = NULL;
	
	private function __construct($results){
		if ( is_resource($results) ) {
			$this->_count = mysql_num_rows($results);
			while($row = mysql_fetch_assoc($results)){
				$this->_results[] = $row;
			}
			for ($i = 0; $i < mysql_num_fields($results); ++$i) {
				$this->_fields[] = mysql_fetch_field($results, $i);
			}
		}
		$this->_affected_rows = mysql_affected_rows();
		if ( mysql_insert_id() !== 0 && mysql_insert_id() !== FALSE ) {
			$this->_insert_id = mysql_insert_id();
		}
	}
	
	static public function query($link=NULL,$db_name=NULL,$sql=NULL,$type='mysql'){
		if ( is_resource($link) && is_string($db_name) ) {
			mysql_select_db($db_name,$link);
		} else {
			return FALSE;
		}
		if ( is_string($sql) && is_resource($link)  ) {
			return new DbQuery(mysql_query($sql,$link));
		} else {
			return FALSE;
		}
	}
	
	public function getCount(){
		return $this->_count;
	}
	
	public function getResults(){
		return $this->_results;
	}
	
	public function getFields(){
		return $this->_fields;
	}
	
	public function getAffectedRows(){
		return $this->_affected_rows;
	}
	
	public function getInsertId(){
		return $this->_insert_id;
	}
	
	private function _htmlDatum($key=NULL,$link_key=NULL,$link_base=NULL,$datum=NULL){
		if ( $key === $link_key ) {
			$href = htmlspecialchars($link_base.urlencode($link_key)."=".urlencode($datum));
			return "<a href=\"".$href."\">".htmlspecialchars($datum)."</a>";
		} else {
			return htmlspecialchars($datum);
		}
	}
	
	private function _zebraStripClass($z=NULL){
		if ( $z % 2 === 0 ) {
			return 'even';
		} else {
			return 'odd';
		}
	}
	
	public function htmlResultsTable($link_key=NULL,$link_base=NULL){
		ob_start();
		echo "<table class=\"results\" cellspacing=\"0\" cellpadding=\"0\" border=\"1\">\n";
		echo "\t<thead>\n";
		echo "\t\t<tr>\n";
		foreach ( $this->_fields as $field ) {
			echo "\t\t\t<th>".htmlspecialchars($field->name)."</th>\n";
		}
		echo "\t\t</tr>\n";
		echo "\t</thead>\n";
		$z = 0;
		foreach ( $this->_results as $result ) {
			echo "\t<tr class=\"".$this->_zebraStripClass($z)."\">\n";
				foreach ( $result as $key => $datum ) {
					echo "\t\t<td class=\"".htmlspecialchars($key)."\">".$this->_htmlDatum($key,$link_key,$link_base,$datum)."</td>\n";
				}
			echo "\t</tr>\n";
			$z++;
		}
		echo "</table>\n";
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	public function htmlResultsList($link_key=NULL,$link_base=NULL){
		ob_start();
		$z = 0;
		foreach ( $this->_results as $result ) {
			echo "<ul class=\"".$this->_zebraStripClass($z)." row\">\n";
				$z2=0;
				foreach ( $result as $key => $datum ) {
					echo "\t<li class=\"".$this->_zebraStripClass($z2)." ".htmlspecialchars($key)."\">\n";
					echo "\t\t<span class=\"key ".htmlspecialchars($key)."\">".htmlspecialchars($key)."</span>\n";
					echo "\t\t<div class=\"datum\">".$this->_htmlDatum($key,$link_key,$link_base,$datum)."</div>\n";
					echo "\t</li>\n";
					$z2++;
				}
			echo "</ul>\n";
			$z++;
		}
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	public function echoResultsJSON(){
		$json = json_encode($this->_results);
		header('Content-Type: application/json; charset=utf-8');
		echo $json;
		
		return;
	}
}
?>
