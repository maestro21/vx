<?php $M = M('i18n');  ?>
<h1><?php echo $title;?></h1>

<form method="POST" id="form" class="content i18nform" action="<?php echo BASE_URL . $class;?>/save?ajax=1" novalidate="novalidate">
	
	<div class="messages"></div>
	<div class="btn addField" ><?php echo T('add field');?></div>
	<div class="btn submit"><?php echo T('save');?></div>

	<div class="table">
		<div class="tr thead">
		<?php foreach($fields as $field => $value) { ?>
			<div class="td">
				<?php echo T($field); ?>			
			</div>
			<?php } ?>
			<div class="td">
			<?php echo T('delete'); ?>			
			</div>
		</div>	
		<?php $i = 0;
		foreach ($data as $row) { 
			echo $M->addField($i,$row); $i++;
		} ?>
	</div>
	

	<div class="btn addField" ><?php echo T('add field');?></div>
	<div class="btn submit"><?php echo T('save');?></div>
			<div class="messages"></div>
</form>
<div class="field hidden">
<?php echo $M->addField();?>
</div>

<script src="<?php echo BASE_URL;?>external/savectrls.js" type="text/javascript"></script>
<script>
	//function saveFn(){ saveForm(); }		
	//$('.submit').click(function() { saveFn() });
	var key = <?php echo (int)@$i;?>;
	$('.addField').click(function(e) { 
		str = $('.field').html().replace(/{key}/g, key); key++; console.log(str);
		$('.thead').after(str); 
	});
	
</script>