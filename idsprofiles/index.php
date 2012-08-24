<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
$path = dirname(__FILE__).'/../../member_configs';
require_once(dirname(__FILE__).'/../classes/Profiles.php');
$p = new Profiles($path);
$p->setGet($_GET);
$profiles = $p->applyFilters($_GET);
$options = $p->setFormOptions($_GET);
?>
<?php header('Content-Type: text/html; charset=utf-8'); ?>
<html>
<head>
<script type="text/javascript" src="toggle.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="main.css" >
</head>
<body onload="toggle_visibility('form');">
<h1>IDS Profile Filtration System - Pro!</h1>
<div id="form">
<form action="" method="get">
<div class="profiles">
<label>ID's: </label><input type="text" name="ids" value="<?php $p->echoGetHTML('ids'); ?>" /><br />
</div>
<div class="filters">
<label>FILTER: </label><input type="text" name="filters[]" value="<?php $p->echoGetHTML('filters',0); ?>" /><br />
<label>FILTER: </label><input type="text" name="filters[]" value="<?php $p->echoGetHTML('filters',1); ?>" /><br />
<label>FILTER: </label><input type="text" name="filters[]" value="<?php $p->echoGetHTML('filters',2); ?>" /><br />
</div>
<div class="values">
<label>VALUE: </label><input type="text" name="values[]" value="<?php $p->echoGetHTML('values',0); ?>" /><br />
<label>VALUE: </label><input type="text" name="values[]" value="<?php $p->echoGetHTML('values',1); ?>" /><br />
<label>VALUE: </label><input type="text" name="values[]" value="<?php $p->echoGetHTML('values',2); ?>" /><br />
<label>VALUE: </label><input type="text" name="values[]" value="<?php $p->echoGetHTML('values',3); ?>" /><br />
<label>VALUE: </label><input type="text" name="values[]" value="<?php $p->echoGetHTML('values',4); ?>" /><br />
<label>VALUE: </label><input type="text" name="values[]" value="<?php $p->echoGetHTML('values',5); ?>" /><br />
<label>VALUE: </label><input type="text" name="values[]" value="<?php $p->echoGetHTML('values',6); ?>" /><br />
<label>VALUE: </label><input type="text" name="values[]" value="<?php $p->echoGetHTML('values',7); ?>" /><br />
<label>VALUE: </label><input type="text" name="values[]" value="<?php $p->echoGetHTML('values',8); ?>" /><br />
<label>VALUE: </label><input type="text" name="values[]" value="<?php $p->echoGetHTML('values',9); ?>" /><br />
</div>
<input type="checkbox" name="options[]" value="showall" <?php if(isset($_GET['options'][0]) && $_GET['options'][0] === 'showall'){echo 'checked="checked" ';} ?>/> show all values for profile(s)<br />
<input type="checkbox" name="options[]" value="sendcash" checked="checked" /> send lawyers, guns, and money!<br />
<input type="submit" value="submit" /> <a href="help.html">WTF?!</a><br />
</div>
<div class="showhide"><a href="#" onclick="toggle_visibility('form');">hide/show form</a></div>
</form>
<div class="hits">HITS: <?php echo $p->getCount(); ?> . <?php echo $p->getHTMLReportName($_GET); ?></div>
<?php $n = 0;
//TODO: a display function would be helpful here
foreach ( $profiles as $i => $c ) {
	$n++;
	echo '<div class="profile">'."\n";
	echo '<div class="row"><div class="label number">#</div><div class="value"> :: '.$n.' :: </div></div>'."\n";
	echo '<div class="row"><div class="label">config file path</div><div class="value"> :: '.$p->getHTMLValue($i,'config_file_path').' :: </div></div>'."\n";
	if ( isset($options['showall']) ) {
		echo "<pre>\n";
		echo var_dump($c);
		echo "</pre>\n";
	} else {
		if ( isset($_GET['values']) ) {
			foreach ( $_GET['values'] as $value ) {
				if ( $value !== '' ) {
					echo '<div class="row"><div class="label">'.$value.'</div><div class="value"> :: '.$p->getHTMLValue($i,$value).' :: </div></div>'."\n";
				}
			}
		}
	}
	echo '</div>'."\n";
}
echo '<pre>';
print_r($_GET);
echo '</pre>';
?>
</body>
</html>
