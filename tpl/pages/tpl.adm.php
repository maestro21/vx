<li>
	<a href="<?php echo BASE_URL;?><?php echo $data['fullurl'];?>" target="_blank"><?php echo $data['name'];?></a> 
	<span style="font-weight:normal !important; font-size:10px">
	<a href="<?php echo BASE_URL;?>pages/edit/<?php echo $data['id'];?>" class="fa-pencil fa icon icon_sml" target="_blank"></a>
	<a href="javascript:void(0)" onclick="conf('<?php echo BASE_URL.$class;?>/del/<?php echo $data['id'];?>', '<?php echo T('del conf');?>')" class="fa-trash-o fa icon icon_sml"></a>	
	</span>
	<?php if(!empty($data['children'])) { ?>
	<ul>
		<?php echo $data['children'];?>
	</ul>
	<?php } ?>
</li>	