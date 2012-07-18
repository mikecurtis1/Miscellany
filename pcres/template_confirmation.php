<?php 
$response = "Created reservation for &quot;{$_GET['pw']}&quot;<br />STARTING {$_GET['start']}<br />STOPPING {$_GET['stop']}<br />";
?>
<div>RESPONSE: </div>
<div>
<?php print_r($response); ?>
</div>
<br />
<div>NOW: </div>
<div>
<?php print date("Y-m-d H:i:s",$config_now); ?>
</div>
<br />
<div>RETURN: </div>
<div>Return to the <a href="https://localhost/pcres/index.php?operation=Request&amp;barcode=<?php echo $cfg->barcode; ?>">request</a> page.</div>
<br />

