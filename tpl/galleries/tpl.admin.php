<h1><?php echo $title;?>
	<?php echo btns($buttons['admin'], $class);?></h1>

<div>
<?php foreach ($data as $item) {
	$id = $item['id'];
	?>
	<div data-id="<?php echo $id;?>" class="photothumb" style="background-image:url('<?php echo getThumbById($item['cover']);?>')">
		<div class="tlcorner">
			<a href="<?php echo BASE_URL.$class;?>/edit/<?php echo $id;?>" target="_blank" class="fa-pencil fa icon icon_sml"></a>
			<a href="javascript:void(0)" onclick="conf('<?php echo BASE_URL.$class;?>/del/<?php echo $id;?>', '<?php echo T('del conf');?>')" class="fa-trash-o fa icon icon_sml "></a>
		</div>
		<input name="name-<?php echo $id;?>" class="galedit" type="text" value="<?php echo $item['name']; ?>">

	</div>
<?php } ?>

</div>
<script>
$('.photothumb').each(function(index) {
	$(this).click(function() {
		window.location = '<?php echo BASE_URL.$class;?>/view/' + $(this).data("id");
	});
});
</script>
