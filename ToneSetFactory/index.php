<pre>
<?php 

include_once('Tone.php');
include_once('ToneSetFactory.php');
include_once('Note.php');
include_once('Scale.php');
include_once('Chord.php');

$tsf = new ToneSetFactory();

echo var_dump(new Tone('C4',TRUE));

try {
	$tone = $tsf->getNoteByIntervalAsc('Eb5','m3');
} catch (Exception $e) {
	echo 'EXCEPTION! ' . $e->getMessage() . "\n";
}
try {
	$scale = $tsf->getScaleAsc('Eb5','harmonic_minor');
} catch (Exception $e) {
	echo 'EXCEPTION! ' . $e->getMessage() . "\n";
}
try {
	$chord = $tsf->getChord('Eb5','diminished_seventh');
} catch (Exception $e) {
	echo 'EXCEPTION! ' . $e->getMessage() . "\n";
}

echo var_dump($tone);
echo var_dump($tone->getToneSet()[0]->getASPN('unicode',TRUE));
echo var_dump($tone->getToneSet()[0]->getHelmholtzPitchNotation('html',TRUE));
echo var_dump($scale);
echo var_dump($chord);

echo "<hr />\n";
echo var_dump($tsf->getChord('C4','dominant_minor_ninth'));

echo "<hr />\n";
echo $scale->getTonic() . ' ' . $scale->getType() . "\n";
foreach ( $scale->getToneSet() as $i => $v ) {
	echo $v->getASPN('unicode') . ' ';
}
echo "\n";

echo "<hr />\n";
#echo var_dump($tsf);
?>
</pre>
