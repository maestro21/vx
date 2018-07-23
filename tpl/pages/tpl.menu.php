<li>
	<a href="<?php echo BASE_URL;?><?php echo $data['fullurl'];?>"<?php if (isset($data['class'])) echo ' class="' . $data['class'] . '"';?>><?php echo $data['name'];?></a> 
	<?php if(isset($data['children']) && !empty($data['children'])) { ?>
	<ul>
		<?php echo $data['children'];?>
	</ul>
	<?php } ?>
</li>