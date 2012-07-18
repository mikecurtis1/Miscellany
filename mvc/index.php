<pre>
<?php 

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

include_once('controller.php');
$singleton = Input::singleton();
$singleton->setValues($_GET);

echo var_dump($singleton);
echo var_dump($singleton->getValue('this'));
echo var_dump($singleton->getValue('anyoldstringyouwanttosendme'));

include_once('model.php');
$rooms = new Rooms();

echo var_dump($rooms);

?>
</pre>
