<?php 

class Member
{
	private $_path = NULL;
	private $_key = NULL;
	private $_name = NULL;
	private $_uri = NULL;
	private $_alias = NULL;
	private $_relations = array();
	private $_tags = array();
	
	private function __construct($path,$key,$name,$uri,$alias){
		$this->_path = $path;
		$this->_key = $key;
		$this->_name = $name;
		$this->_uri = $uri;
		$this->_alias = $alias;
	}
	
	static public function create($path=NULL,$key=NULL,$name=NULL,$uri=NULL){
		if ( is_string($path) && is_string($key) && is_string($name) && filter_var($uri,FILTER_VALIDATE_URL) ) {	
			return new Member($path,$key,$name,$uri,NULL);
		} else {
			throw new Exception('Error: data validation failed (path, key, and name must be strings, uri must be a URL, instance of Member not created.');
		}
	}
	
	static public function createAlias($path=NULL,$key=NULL,$name=NULL,$alias=NULL){
		if ( is_string($path) && is_string($key) && is_string($name) && is_string($alias) ) {	
			return new Member($path,$key,$name,NULL,$alias);
		} else {
			throw new Exception('Error: data validation failed (path, key, name, and alias must be strings, instance of Member not created.');
		}
	}
	
	public function setRelation($key=NULL){
		if ( is_string($key) ) {
			if ( ! in_array($key,$this->_relations) ) {
				$this->_relations[] = $key;
			} else {
				throw new Exception('Notice: relation key already set.');
			}
		} else {
			throw new Exception('Error: relation key must be a string.');
		}
	}
	
	public function setTags($arg=NULL){
		if ( is_string($arg) ) {
			if ( $tags = explode('|',$arg) ) {
				foreach ( $tags as $tag ) {
					$this->_tags[$tag] = $tag;
				}
			} else {
				throw new Exception('Error: unable to split the tags string.');
			}
		} else {
			throw new Exception('Error: tags must be a string.');
		}
	}
	
	public function getPath(){
		return $this->_path;
	}
	
	public function getKey(){
		return $this->_key;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function getUri(){
		return $this->_uri;
	}
	
	public function getAlias(){
		return $this->_alias;
	}
	
	public function getRelations(){
		return $this->_relations;
	}
	
	public function getTags(){
		return $this->_tags;
	}
	
	public function __toString(){
		return '<a href="'.htmlspecialchars($this->_uri).'">'.htmlspecialchars($this->_name).'</a> KEY:'.htmlspecialchars($this->_key);
	}
}
?>
