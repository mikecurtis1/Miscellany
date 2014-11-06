<pre>
<?php 

$tables = MusicTables::config();

echo "<hr />\n";
echo var_dump($tables::getASPNValue('C4', 'piano_key'));
echo "\n";

echo "<hr />\n";
$chord = new Chord('C4', 'minor', $tables);
foreach ( $chord->getToneSet() as $tone ) {
	echo $tone->getASPN() . ' ';
}
echo "\n\n";
echo var_dump($chord);
echo "\n";

echo "<hr />\n";
$scale = new Scale('C4', 'harmonic_minor', $tables);
foreach ( $scale->getToneSet() as $tone ) {
	echo $tone->getASPN() . ' ';
}
echo "\n\n";
echo var_dump($scale);
echo "\n";

?>
</pre>
