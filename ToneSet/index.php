<pre>
<?php 

$tables = MusicTables::config();

echo var_dump($tables::getASPNValue('C4', 'piano_key'));
echo "\n";
echo "<hr />\n";

$chord = new Chord('C4', 'minor', $tables);
foreach ( $chord->getToneSet() as $tone ) {
	echo $tone->getASPN() . ' ';
}
echo "\n";
echo "<hr />\n";
echo var_dump($chord);

?>
</pre>
