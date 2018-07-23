	<?php $langs = getLangs();
?>
<div class="dropdownmenu">
<ul  class="topmenu">
<li><a href="#" class="curr_lang"></a>
	<ul>
	<?php $langs = getLangs(); 
	foreach ($langs as $lang) { 
		$url = (empty($lang['website']) ?  BASE_URL . $lang['abbr'] : $lang['website'] . HOST_FOLDER);?>
		<li><a href="<?php echo $url;?>"><img src="<?php echo IMG_URL . 'langs/' .$lang['abbr'] . '.png';?>"> <?php echo $lang['name'];?></a> 
		<?php if($lang['abbr'] == lang()) { ?>
			<script>$('.curr_lang').html('<img src="<?php echo IMG_URL . 'langs/' .$lang['abbr'] . '.png';?>"> <?php echo $lang['name'];?>&#9207;');</script>
		<?php } ?>
	<?php } ?>
	</ul>
</ul>	
</div>