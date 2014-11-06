<html>
<head>
<style type="text/css">
body{
	font-family: Verdana, sans-serif;
}
h1,h2,h3,h4,h5{
	font-weight: normal;
}
pre.usage {
	float:right;
	width:47%;
	background-color:#ddb;
	margin:0.5em;
	padding:0.5em;
}
pre.php{
	float:left;
	width:47%;
	background-color:#ede;
	margin:0.5em;
	padding:0.5em;
}
h1,h2,hr {
	clear:both;
}
</style>
</head>
<body>
<h1>Object-Oriented Programming - the three pillars: polymorphism, encapsulation, &amp; inheritance</h1>
<hr />
<h2>Polymorphism : requiring methods without specifying how they are implemented</h2>
<?php 

// Polymorphism : requiring methods without specifying how they are implemented

interface Basket 
{
	function addItem();
	function getTotalItems();
}

class Basket1 implements Basket
{
	public $items = array();
	public $total_items = 0;
	
	function addItem($item=''){
		$this->items[] = $item;
		$this->total_items++;
	}
	
	function getTotalItems(){
		return $this->total_items;
	}
}

class Basket2 implements Basket
{
	public $items = array();
	
	function addItem($item=''){
		$this->items[] = $item;
	}
	
	function getTotalItems(){
		return count($this->items);
	}
}
?>
<pre class="php">
PHP

interface Basket 
{
  function addItem();
  function getTotalItems();
}

class Basket1 implements Basket
{
  public $items = array();
  public $total_items = 0;
  
  function addItem($item=''){
    $this->items[] = $item;
    $this->total_items++;
  }
  
  function getTotalItems(){
    return $this->total_items;
  }
}

class Basket2 implements Basket
{
  public $items = array();
  
  function addItem($item=''){
    $this->items[] = $item;
  }
  
  function getTotalItems(){
    return count($this->items);
  }
}
</pre>
<pre class="usage">
USAGE: 

<?php 
$basket1 = new Basket1();
#$basket1->addItem('coin');
#$basket1->addItem('key');
$basket1->addItem('book');
echo "\$basket1 = new Basket1()\n";
#echo "\$basket1->addItem('coin')\n";
#echo "\$basket1->addItem('key')\n";
echo "\$basket1->addItem('book')\n";
echo "\n";
echo var_dump($basket1);
echo "\n";
echo "var_dump \$basket1->getTotalItems()\n";
echo var_dump($basket1->getTotalItems());
echo "\n";
$basket2 = new Basket2();
$basket2->addItem('milk');
$basket2->addItem('cheese');
$basket2->addItem('bread');
#$basket2->addItem('egg');
#$basket2->addItem('butter');
echo "\$basket2 = new Basket2()\n";
echo "\$basket2->addItem('milk')\n";
echo "\$basket2->addItem('cheese')\n";
echo "\$basket2->addItem('bread')\n";
#echo "\$basket2->addItem('egg')\n";
#echo "\$basket2->addItem('butter')\n";
echo "\n";
echo var_dump($basket2);
echo "\n";
echo "var_dump \$basket2->getTotalItems())\n";
echo var_dump($basket2->getTotalItems());
?>
</pre>
<hr />
<h2>Encapsulation : bundling methods and properties with an object and controlling their access</h2>
<?php 

// encapsulation : bundling methods and properties with an object and controlling their access

class Name 
{
	private $_first;
	private $_last;
	private $_initials;
	
	public function __construct($first='',$last='' ){
		$this->_first = ucfirst((string)$first);
		$this->_last = ucfirst((string)$last);
		$this->_setInitials();
	}
	
	private function _setInitials(){
		$this->_initials .= substr($this->_first,0,1) . '.';
		$this->_initials .= substr($this->_last,0,1) . '.';
	}
	
	public function getInitials(){
		return $this->_initials;
	}
}
?>
<pre class="php">
PHP

class Name 
{
  private $_first;
  private $_last;
  private $_initials;
  
  public function __construct($first='',$last='' ){
    $this->_first = ucfirst((string)$first);
    $this->_last = ucfirst((string)$last);
    $this->_setInitials();
  }
  
  private function _setInitials(){
    $this->_initials .= substr($this->_first,0,1) . '.';
    $this->_initials .= substr($this->_last,0,1) . '.';
  }
  
  public function getInitials(){
    return $this->_initials;
  }
}
</pre>
<pre class="usage">
USAGE: 

<?php 
$name = new Name('mike', 'curtis');
echo "\$name = new Name('mike', 'curtis')\n";
echo "\n";
echo var_dump($name);
echo "\n";
echo "var_dump \$name->getInitials()\n";
echo "\n";
echo var_dump($name->getInitials());
echo "\n";
echo "echo \$name->_initials\n";
echo "Fatal error:  Cannot access private property Name::\$_initials\n\n";
?>
</pre>
<hr />
<h2>Inheritance : hierarchy of shared methods and properties between objects</h2>
<?php 

// Inheritance : hierarchy of shared methods and properties between objects

abstract class Product 
{
	private $_sku;
	protected $type = null;
	
	public function __construct($sku){
		$this->_sku = $sku;
	}
}

class Product_Chair extends Product
{
	protected $type = 'chair';
}
?>
<pre class="php">
PHP

abstract class Product 
{
  private $_sku;
  protected $type = null;
  
  public function __construct($sku){
    $this->_sku = $sku;
  }
}

class Product_Chair extends Product
{
  protected $type = 'chair';
}
</pre>
<pre class="usage">
USAGE: 

<?php 
echo "new Product_Chair('0001')\n";
echo "\n";
echo var_dump(new Product_Chair('0001'));
echo "\n";
echo "var_dump new Product('0001')\n";
echo "Fatal error:  Cannot instantiate abstract class Product\n";
echo "\n";
?>
</pre>
<hr />
</body>
</html>
