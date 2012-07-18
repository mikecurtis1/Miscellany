<!--
<form action="schedule_manager.php" method="get">
<input type="text" name="start" value="<?php echo $cfg->start; ?>" />
<input type="text" name="stop" value="<?php echo $cfg->stop; ?>" />
-->
<form action="index.php" method="get">
<label for="barcode">Barcode:</label><input id="barcode" type="text" name="barcode" value="<?php echo $cfg->barcode; ?>" />
<!--<input type="hidden" name="operation" value="Schedule" />-->
<input type="hidden" name="operation" value="Request" />
<input type="submit" value="submit" />
</form>

