<?php 
$blocks = create_time_blocks($cfg);
foreach($blocks as $i => $block){
	$temp_start = $block['start']['sql'];
	$html_start = date("g:i a",$block['start']['timestamp']);
	$temp_stop = $block['stop']['sql'];
	$html_stop = date("g:i a",$block['stop']['timestamp']);
	$available_seats = $cfg->seats - find_schedule_conflicts($temp_start,$temp_stop,TRUE,NULL,TRUE,TRUE);
	$barometer = barometer($available_seats,$cfg->seats);
	$color = barometer($available_seats,$cfg->seats,38,'colors');
	$current_time_block = '';
	if((strtotime($temp_start) <= $cfg->now) && (strtotime($temp_stop) >= $cfg->now)){
		$current_time_block = '&nbsp;&#9658;&nbsp;';
	}
	$hyperlinks[] = "<!-- {$block['start']['timestamp']} {$block['stop']['timestamp']} --><div style=\"background-color:{$color};\" class=\"inline schedule_link\">{$current_time_block}<a href=\"schedule_manager.php?start={$temp_start}&amp;stop={$temp_stop}&amp;barcode={$cfg->barcode}\">{$html_start} - {$html_stop}</a>.</div><span class=\"seats\">SEATS: {$available_seats}.</span> <span class=\"barometer\">%{$barometer}.</span>";
}

?>
<?php include('template_form.php'); ?>
<?php #if($cfg->barcode != ''){ ?>
<br />
<div>SYSTEM</div>
<div>Now: <?php echo date("Y-m-d H:i:s",$cfg->now); ?></div>
<div>Block: <?php echo ($cfg->default_time_block / 60); ?>(min)</div>
<div>Max extenion time: <?php echo (($cfg->extension_max_time+1)/60); ?>(min)</div>
<div>Max time: <?php echo ($cfg->reservations_daily_total_time/(60*60)); ?>(hrs) or <?php echo ($cfg->reservations_daily_total_time/(60)); ?>(min) or <?php echo $cfg->reservations_daily_total_time; ?>(sec)</div>
<br />
<div>TIME BLOCKS for <?php echo $cfg->day_begin; ?> - <?php echo $cfg->day_end; ?></div>
<?php foreach($hyperlinks as $link){ ?>
<div class="schedule_row"><?php echo $link; ?></div>
<?php } ?>
<?php #} ?>

