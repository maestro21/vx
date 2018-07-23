<h1><?php echo $title;?></h1>
<?php $formid = $class . '_form_item_' . $id;?>
<form method="POST" id="form"  action="<?php echo BASE_URL . $class;?>/save?ajax=1">

	<?php inspect($data['data']);
		echo form($fields['lang_widget'], $data['data']['lang_widget']);
	?>
	<div class="btn btn-primary submit"><?php echo T('save');?></div>
	<div class="messages"></div>

</form>
</v-card-text>
</v-card>
</v-tab-item>
<v-tab-item>
	<v-card flat class="page">
		<v-card-text>
		<form method="POST" id="form"  action="<?php echo BASE_URL . $class;?>/savelabels?ajax=1">

			<?php
				echo form($fields['i18n'], $data);
			?>
			<div class="btn btn-primary submit"><?php echo T('save');?></div>
			<div class="messages"></div>

		</form>
	</v-card-text>
	</v-card>
</v-tab-item>
<v-tab-item>
	<v-card flat class="page">
		<v-card-text>
