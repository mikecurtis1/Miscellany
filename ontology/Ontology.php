<?php 

require_once('Collection.php');

class Ontology
{
	
	private $_name = NULL;
	private $_system = NULL;
	private $_delimiter = NULL;
	
	private function __construct($name,$delimiter){
		$this->_name = $name;
		$this->_system = Collection::create('ROOT');
		$this->_delimiter = $delimiter;
	}
	
	public static function create($name=NULL,$delimiter=NULL){
		if ( (is_string($name) && $name !== '') && (is_string($delimiter) && $delimiter !== '') ) {
			return new Ontology($name,$delimiter);
		} else {
			throw new Exception('Error: name and delimiter must be non-empty strings.');
		}
	}
	
	public function addMember($arg=NULL){
		if ( $arg instanceof Member ) {
			$nodes = explode($this->_delimiter,$arg->getPath());
			$this->_system = $this->_addMemberToSystem($nodes,$arg,$this->_system);
		} else {
			throw new Exception('Error: addMember() only accepts an instance of Member.');
		}
	}
	
	private function _addMemberToSystem($nodes=array(),$member,&$sys=NULL){
		if ( count($nodes) > 1 ) {
			$node = array_shift($nodes);
			if ( ! isset($sys->getColl()[$node]) && $sys->getName() !== $node ) {
				$sys->addColl(Collection::create($node),$node);
			}
			$this->_addMemberToSystem($nodes,$member,$sys->getColl()[$node]);
		} elseif ( count($nodes) === 1 ) {
			$node = array_shift($nodes);
			if ( isset($sys->getColl()[$node]) ) {
				$sys->getColl()[$node]->addMember($member,$node);
			} else {
				$sys->addColl(Collection::create($node),$node);
				$sys->getColl()[$node]->addMember($member,$node);
			}
		}
		return $sys;
	}
	
	public function getMemberByKey($key,$a=NULL,&$member=NULL) {
		if ( $a === NULL ) {
			$a = $this->_system->getColl();
		}
		if(is_array($a)) {
			foreach($a as $k => $c){
				$set = $c->getSet();
				if ( ! empty($set) ) {
					foreach ( $c->getSet() as $m ) {
						if ( $m->getKey() === $key ) {
							$member = $m;
						}
					}
				}
				$this->getMemberByKey($key,$c->getColl(),$member);
			}
		}
		return $member;
	}
	
	public function getBranchMembers($path,$a=NULL,&$members=array()) {
		if ( $a === NULL ) {
			$a = $this->_system->getColl();
		}
		if(is_array($a)) {
			foreach($a as $k => $c){
				$set = $c->getSet();
				if ( ! empty($set) ) {
					foreach ( $c->getSet() as $m ) {
						if ( $path === substr($m->getPath(),0,strlen($path)) ) {
							$members[$m->getKey()] = $m;
						}
					}
				}
				$this->getBranchMembers($path,$c->getColl(),$members); 
			}
		}
		return $members;
	}
	
	public function setMemberRelationByKey($key1,$key2,$a=NULL,&$success=FALSE) {
		if ( $a === NULL ) {
			$a = $this->_system->getColl();
		}
		if(is_array($a)) {
			foreach($a as $k => $c){
				$set = $c->getSet();
				if ( ! empty($set) ) {
					foreach ( $c->getSet() as $m ) {
						if ( $m->getKey() === $key1 ) {
							$m->setRelation($key2);
							$success = TRUE;
						}
					}
				}
				$this->setMemberRelationByKey($key1,$key2,$c->getColl(),$success); 
			}
		}
		return $success;
	}
	
	public function getMemberUriByKey($key,$a=NULL,&$uri=NULL) {
		if ( $a === NULL ) {
			$a = $this->_system->getColl();
		}
		if(is_array($a)) {
			foreach($a as $k => $c){
				$set = $c->getSet();
				if ( ! empty($set) ) {
					foreach ( $c->getSet() as $m ) {
						if ( $m->getKey() === $key ) {
							$uri = $m->getUri();
						}
					}
				}
				$this->getMemberUriByKey($key,$c->getColl(),$uri);
			}
		}
		return $uri;
	}
	
	public function buildHTMLList($a=NULL,&$html=NULL,$name=NULL,$counter=0) {
		if ( $a === NULL ) {
			$a = $this->_system->getColl();
		}
		$indent = "\t";
		$counter++;
		foreach($a as $k => $c){
			$set = $c->getSet();
			$html .= str_repeat($indent,$counter).'<li>'."\n";
			if ( ! empty($set) ) {
				$html .= str_repeat($indent,($counter)).'<a class="COLL:'.$k.'">'.str_replace('_',' ',$k).'</a>'."\n";
				$html .= str_repeat($indent,($counter)).'<ul>'."\n";
				foreach ( $c->getSet() as $m ) {
					if ( $m->getAlias() !== NULL ) {
						$uri = $this->getMemberUriByKey($m->getAlias(),NULL);
						$alias = ', ALIAS:'.$m->getAlias().'';
					} else {
						$uri = $m->getUri();
						$alias = '';
					}
					$html .= str_repeat($indent,($counter+1)).'<li class="member MEM:'.$m->getKey().'">'."\n";
					$html .= str_repeat($indent,($counter+2)).'<a href="'.$uri.'">'.$m->getName().'MEM:'.$m->getKey().$alias.'</a>'."\n";
					$rel = $m->getRelations();
					if ( ! empty($rel) ) {
						$html .= str_repeat($indent,($counter+2)).'<ul>'."\n";
						foreach ( $rel as $key ) {
							$m_rel = $this->getMemberByKey($key);
							$m_rel_name = $m_rel->getName();
							$m_rel_uri = $m_rel->getUri();
							if ( $m_rel_uri !== '' ) {
								$html .= str_repeat($indent,($counter+2)).'<li class="member_rel MEM:'.$key.'">'."\n";
								$html .= str_repeat($indent,($counter+3)).'<a href="'.$m_rel_uri.'">REL:'.$m_rel_name.'</a>'."\n";
								$html .= str_repeat($indent,($counter+2)).'</li>'."\n";
							}
						}
						$html .= str_repeat($indent,($counter+2)).'</ul>'."\n";
					}
					$html .= str_repeat($indent,($counter+1)).'</li>'."\n";
				}
				$this->buildHTMLList($c->getColl(),$html,$c->getName(),$counter);
			} else {
				$html .= str_repeat($indent,($counter)).'<a class="COLL:'.$k.'">'.str_replace('_',' ',$k).'</a>'."\n";
				$html .= str_repeat($indent,($counter)).'<ul>'."\n";
				$this->buildHTMLList($c->getColl(),$html,$c->getName(),$counter);
			}
		}
		$counter--;
		if ( $counter > 0 ) {
			$html .= str_repeat($indent,($counter)).'</ul>'."\n";
			$html .= str_repeat($indent,$counter).'</li>'."\n";
		}
		return $html;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function getSystem(){
		return $this->_system;
	}
	
	public function getColl(){
		return $this->_system->getColl();
	}
}
?>
