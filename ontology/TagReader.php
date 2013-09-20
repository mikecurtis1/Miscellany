<pre>
<?php 
$exceptions = array();
$path_delimiter = '::';
$tag_sets[] = 'beef,animal,food';
$tag_sets[] = 'animal,fish,food';
$tag_sets[] = 'vegetable,lettuce,leaf,food';
$tag_sets[] = 'spinach,leaf,food,vegetable';
$tag_sets[] = 'food,legume,vegetable,peas';
$tag_sets[] = 'burger,grub,cow';
$tag_sets[] = 'burger,beef,food';
$tag_synonyms = array('grub'=>'food','cow'=>'beef');
#$tag_sets = 'kittens'; // force an exception for testing
#$tag_sets = array('pet'=>FALSE); // testing
#$tag_sets = array('pet'=>'kittens'); // testing
#$tag_sets = array('kittens'); // testing
try {
	$t = new TagReader($path_delimiter,$tag_sets,$tag_synonyms);
} catch (Exception $e ) {
	$exceptions[] = $e->getMessage();
}
if ( ! empty($exceptions) ) {
	print_r($exceptions);
	die('Exceptions must be resolved before the program can continue running.');
}
echo "\n------------------------------------------------------------\n";
echo "INPUT tag sets:\n";
print_r($tag_sets);
echo "\n------------------------------------------------------------\n";
echo "INPUT tag synonyms:\n";
print_r($tag_synonyms);
echo "\n------------------------------------------------------------\n";
echo "getTagSets:\n\n";
print_r($t->getTagSets());
echo "\n------------------------------------------------------------\n";
echo "getTagSynonyms:\n\n";
print_r($t->getTagSynonyms());
echo "\n------------------------------------------------------------\n";
echo "getTagList:\n\n";
print_r($t->getTagList());
echo "\n------------------------------------------------------------\n";
echo "getTagCounts:\n\n";
print_r($t->getTagCounts());
echo "\n------------------------------------------------------------\n";
echo "getTagIndex:\n\n";
print_r($t->getTagIndex());
echo "\n------------------------------------------------------------\n";
echo "getTagRanks:\n\n";
print_r($t->getTagRanks());
echo "\n------------------------------------------------------------\n";
echo "getTagPaths:\n\n";
print_r($t->getTagPaths());
echo "\n------------------------------------------------------------\n";
?>
</pre>
<?php 

class TagReader
{
	private $_tag_delimiter = ',';
	private $_path_delimiter = ',';
	private $_tag_sets = array();
	private $_tag_list = array();
	private $_tag_counts = array();
	private $_tag_index = array();
	private $_tag_ranks = array();
	private $_tag_paths = array();
	private $_tag_synonyms = array('grub'=>'food','cow'=>'beef');
	
	public function __construct($path_delimiter=NULL,$tag_sets=array(),$tag_synonyms=array()){
		if ( is_string($path_delimiter) ) {
			$this->_path_delimiter = $path_delimiter;
		} else {
			throw new Exception('Error: path delimiter must be a string.');
		}
		if ( is_array($tag_sets) ) { 
			foreach ( $tag_sets as $k => $v ) {
				if ( is_string($v) ) {
					$this->_tag_sets[] = $v;
				}
			}
		} else {
			throw new Exception('Error: tag_sets must be an array.');
		}
		if ( is_array($tag_synonyms) ) { 
			foreach ( $tag_sets as $k => $v ) {
				if ( is_string($k) && is_string($v) ) {
					$this->_tag_synonyms[$k] = $v;
				}
			}
		} else {
			throw new Exception('Error: tag_synonyms must be an array.');
		}
		$this->_setTagList();
		$this->_setTagCounts();
		$this->_setTagIndex();
		$this->_setTagRanks();
		$this->_setTagPaths();
	}
	
	private function _setTagList(){
		foreach ( $this->_tag_sets as $list ) {
			foreach ( explode($this->_tag_delimiter,$list) as $tag ) {
				$this->_tag_list[] = $tag;
			}
		}
		sort($this->_tag_list);
	}
	
	private function _setTagCounts(){
		foreach ( $this->_tag_list as $tag ) {
			if ( ! isset($this->_tag_counts[$tag]) ) {
				$this->_tag_counts[$tag] = 1;
			} else {
				$this->_tag_counts[$tag] = $this->_tag_counts[$tag] + 1;
			}
		}
	}
	
	private function _setTagIndex(){
		arsort($this->_tag_counts);
		foreach ( $this->_tag_counts as $tag => $count ) {
			$this->_tag_index[] = array('tag'=>$tag,'count'=>$count);
		}
	}
	
	private function _setTagRanks(){
		$rank = 1;
		$next_count = NULL;
		if ( isset($this->_tag_index[(0+1)]['count']) ) {
			$next_count = $this->_tag_index[(0+1)]['count'];
		} elseif( isset($this->_tag_index[0]['count']) ) {
			$next_count = $this->_tag_index[0]['count'];
		}
		if ( $next_count !== NULL ) {
			foreach ( $this->_tag_index as $n => $data ) {
				if ( isset($this->_tag_index[($n+1)]['count']) ) {
					$next_count = $this->_tag_index[($n+1)]['count'];
				}
				$this->_tag_ranks[$data['tag']] = $rank;
				if ( $data['count'] > $next_count  ) {
					$rank++;
				}
			}
		}
	}
	
	private function _setTagPaths(){
		foreach ( $this->_tag_sets as $set ) {
			$temp = array();
			foreach ( explode($this->_tag_delimiter,$set) as $tag ) {
				if ( isset($this->_tag_synonyms[$tag]) ) {
					$temp[$this->_tag_synonyms[$tag].'('.$tag.')'] = $this->_tag_ranks[$this->_tag_synonyms[$tag]];
				} else {
					$temp[$tag] = $this->_tag_ranks[$tag];
				}
			}
			asort($temp);
			$this->_tag_paths[] = implode($this->_path_delimiter,array_keys($temp));
		}
	}
	
	public function getTagSets(){
		return $this->_tag_sets;
	}
	
	public function getTagSynonyms(){
		return $this->_tag_synonyms;
	}
	
	public function getTagList(){
		return $this->_tag_list;
	}
	
	public function getTagCounts(){
		return $this->_tag_counts;
	}
	
	public function getTagIndex(){
		return $this->_tag_index;
	}
	
	public function getTagRanks(){
		return $this->_tag_ranks;
	}
	
	public function getTagPaths(){
		return $this->_tag_paths;
	}
}
?>
