<h1>Un-named</h1>
<p>Go to <a href="#rank">RANK</a> section... </p>
<pre>
<?php 

error_reporting(E_ALL);

$tstopwords = explode("\n",file_get_contents('stopwords.txt'));
sort($tstopwords);
$stopwords = array_unique($tstopwords);
print_r($stopwords);
print implode("\n",$stopwords);
#exit;

function remove_stopwords($array){
	#$stopwords = array('thee','thy','thou','ye','o','yea','a','i','unto','shall','about', 'above', 'above', 'across', 'after', 'afterwards', 'again', 'against', 'all', 'almost', 'alone', 'along', 'already', 'also','although','always','am','among', 'amongst', 'amoungst', 'amount',  'an', 'and', 'another', 'any','anyhow','anyone','anything','anyway', 'anywhere', 'are', 'around', 'as',  'at', 'back','be','became', 'because','become','becomes', 'becoming', 'been', 'before', 'beforehand', 'behind', 'being', 'below', 'beside', 'besides', 'between', 'beyond', 'bill', 'both', 'bottom','but', 'by', 'call', 'can', 'cannot', 'cant', 'co', 'con', 'could', 'couldnt', 'cry', 'de', 'describe', 'detail', 'do', 'done', 'down', 'due', 'during', 'each', 'eg', 'eight', 'either', 'eleven','else', 'elsewhere', 'empty', 'enough', 'etc', 'even', 'ever', 'every', 'everyone', 'everything', 'everywhere', 'except', 'few', 'fifteen', 'fify', 'fill', 'find', 'fire', 'first', 'five', 'for', 'former', 'formerly', 'forty', 'found', 'four', 'from', 'front', 'full', 'further', 'get', 'give', 'go', 'had', 'has', 'hasnt', 'have', 'he', 'hence', 'her', 'here', 'hereafter', 'hereby', 'herein', 'hereupon', 'hers', 'herself', 'him', 'himself', 'his', 'how', 'however', 'hundred', 'ie', 'if', 'in', 'inc', 'indeed', 'interest', 'into', 'is', 'it', 'its', 'itself', 'keep', 'last', 'latter', 'latterly', 'least', 'less', 'ltd', 'made', 'many', 'may', 'me', 'meanwhile', 'might', 'mill', 'mine', 'more', 'moreover', 'most', 'mostly', 'move', 'much', 'must', 'my', 'myself', 'name', 'namely', 'neither', 'never', 'nevertheless', 'next', 'nine', 'no', 'nobody', 'none', 'noone', 'nor', 'not', 'nothing', 'now', 'nowhere', 'of', 'off', 'often', 'on', 'once', 'one', 'only', 'onto', 'or', 'other', 'others', 'otherwise', 'our', 'ours', 'ourselves', 'out', 'over', 'own','part', 'per', 'perhaps', 'please', 'put', 'rather', 're', 'same', 'see', 'seem', 'seemed', 'seeming', 'seems', 'serious', 'several', 'she', 'should', 'show', 'side', 'since', 'sincere', 'six', 'sixty', 'so', 'some', 'somehow', 'someone', 'something', 'sometime', 'sometimes', 'somewhere', 'still', 'such', 'system', 'take', 'ten', 'than', 'that', 'the', 'their', 'them', 'themselves', 'then', 'thence', 'there', 'thereafter', 'thereby', 'therefore', 'therein', 'thereupon', 'these', 'they', 'thickv', 'thin', 'third', 'this', 'those', 'though', 'three', 'through', 'throughout', 'thru', 'thus', 'to', 'together', 'too', 'top', 'toward', 'towards', 'twelve', 'twenty', 'two', 'un', 'under', 'until', 'up', 'upon', 'us', 'very', 'via', 'was', 'we', 'well', 'were', 'what', 'whatever', 'when', 'whence', 'whenever', 'where', 'whereafter', 'whereas', 'whereby', 'wherein', 'whereupon', 'wherever', 'whether', 'which', 'while', 'whither', 'who', 'whoever', 'whole', 'whom', 'whose', 'why', 'will', 'with', 'within', 'without', 'would', 'yet', 'you', 'your', 'yours', 'yourself', 'yourselves', 'the');
	$stopwords = array_unique(explode("\n",file_get_contents('stopwords.txt')));
	foreach($array as $i => $word){
		foreach($stopwords as $i2 => $stopword){
			if($word == $stopword){
				unset($array[$i]);
			}
		}
	}
	return $array;
}

// one row does not generate relations
/*$tagged_items = array(
	array('apartment','apartment','apartment','10','single','single','single','single','companionship','apartment','7','single','rescue stray','cat') 
);*/

// one row does not generate relations
/*$tagged_items = array(
	array('apartment'), 
	array('this'), 
	array('con artist'), 
	array('Vietnam'),
	array('radar'),
	array('combat'),
	array('leg'),
	array('limb'),
	array('father')
);*/

// diverse list of 'fielded' tags
/*$tagged_items = array(
	array('house','5','family','obedience','dog'),
	array('apartment','6','single','companionship','cat'),
	array('apartment','3','family','food','fish'),
	array('house','8','family','hobby','bird'),
	array('house','12','family','hunting','dog'),
	array('apartment','10','single','companionship','dog'),
	array('house','10','single','companionship','cat'),
	array('apartment','7','single','rescue stray','cat'),
	array('house','6','single','rescue stray','cat')
);*/

// nesting works with recursive function, but really... why would you want to do this?
/*$tagged_items = array(
	array('house','5','family','obedience','dog'),
	array('apartment','6',array('apartment','3','family','food','fish'),'companionship','cat'),
	array('house','8','family','hobby','bird'),
	array('house','12','family','hunting','dog'),
	array('apartment','10','single','companionship',array('apartment','7','single','rescue stray','cat')),
	array('house','10','single','companionship','cat'),
	array('house','6','single','rescue stray','cat')
);*/

// a sort of playlist w/topics or a literature search listing
/*$tagged_items = array(
	array('list1','a'),
	array('list1','b'),
	array('list1','c'),
	array('list1','d'),
	array('list1','e'),
	array('list2','a'),
	array('list2','f'),
	array('list2','g'),
	array('list2','h'),
	array('a','topic1'),
	array('b','topic2','like'),
	array('topic1','like'),
	array('c','topic2','topic3')
);*/


// Try a large, free text source... like the Book of Genesis.
// so are we going into concordnance or Bible-code territory?
$n = 1;
while($n < 50){
	$url = 'http://etext.virginia.edu/etcbin/toccer-new2?id=KjvGene.sgm&images=images/modeng&data=/texts/english/modeng/parsed&tag=public&part='.$n.'&division=div1';
	$text = file_get_contents($url);
	$text = strtolower($text);
	preg_match("/      \<p\>\&nbsp\;\&nbsp\;\&nbsp\;(.+)\<\/p\>/",$text,$matches);
	$text = $matches[1];
	$text = strip_tags($text);
	$text = ereg_replace("[^A-Za-z ]", '', $text);
	$text = ereg_replace("[ \t\n\r]+", ' ', $text);
	$tagged_item = split(' ',trim($text));
	$tagged_item = remove_stopwords($tagged_item);
	#$texts .= implode(' ',$tagged_item);
	$tagged_items[] = $tagged_item;
	$n++;
}

?>
<?php 

function myTags($array){
	global $tags;
	global $array_key;
	foreach($array as $key => $item){
		if(is_array($item)){
			$array_key = $key;
			myTags($item);
		} else {
			$tags[$item][$array_key] = $array_key;
		}
	}
	return $tags;
}

$tags = myTags($tagged_items);

function cmp($a, $b) {
	return (count($a) < count($b));
}
#uasort($tags,'cmp');

$rank = array();
foreach($tags as $i => $a){
	#$rank[count($a)][] = $i.':'.implode(',',$a);
	$rank[count($a)][$i] = implode(',',$a);
	#$rank[count($a)][] = array($i=>$a);
}
krsort($rank);

$tree = array();
/*foreach($tags as $i => $a){
	
}*/

foreach($tagged_items as $tagged_item){
	$tagged_items_string .= implode(' ',$tagged_item);
}

?>
--------------------TAGGED-ITEMS-------------------
<?php print_r($tagged_items); ?>
-----------------------TAGS------------------------
<?php print_r($tags); ?>
-----------------------<a name="rank">RANK</a>------------------------
<?php print_r($rank); ?>
-----------------------TREE------------------------
<?php print_r($tree); ?>
---------------------------------------------------
<?php print "\n\nDONE\n\n"; ?>
</pre>
<p>http://www.wordle.net/</p>
<form action="http://www.wordle.net/advanced" method="POST">
<textarea rows="25" cols="65" name="text">
<?php echo $tagged_items_string; ?>
</textarea>
<input type="submit">
</form>
