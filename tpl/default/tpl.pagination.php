<div class="pagination">
<?php for($i = 0; $i < $pages; $i++) { ?>
	<a href="<?php echo $url . '/' . $i . ($posturl != '' ? '/' . $posturl : '');?>"<?php if($i == $page) echo ' class="active"';?>><?php echo $i + 1; ?></a>
<?php } ?>
</div>