<h1><?php echo $title;?></h1>
<?php $formid = $class . '_form_item_' . $id;?>
<form method="POST" id="form"  action="<?php echo BASE_URL . $class;?>/save?ajax=1">

<input type="hidden" name="id" id="id" value="<?php echo $id;?>">

	<?php
		echo form($data);
	?>
	<div class="btn btn-primary submit"><?php echo T('save');?></div>
	<div class="messages"></div>

</form>
