<pre>
<?php 

MusicTables::config();

echo var_dump(MusicTables::getASPNValue('C4','key'));
echo "\n";
echo "<hr />\n";

$chord = new Chord();
$chord->build();
foreach ( $chord->getToneSet() as $tone ) {
	echo $tone->getASPN() . ' ';
}
echo "\n";
echo "<hr />\n";
echo var_dump($chord);

?>
</pre>
