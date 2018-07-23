<?php
$messages = $data['messages'];
$forms = $data['forms']; 
$name = $data['name'];
?>

<h1><?php echo (!empty($name) ? T('form_messages') . ' `' . $name . '`' : T('forms_messages'));?></h1>

<?php 
foreach($messages as $message) { 
	$msgdata = unserialize($message['data']);
	$msgdata = moveToBottom($msgdata, 'message');
	$formid = $message['form_id'];
	$fields = $forms[$formid];
	 ?>
	<div class="message"> 
		<b><?php echo T('sent on') . ' ' . fDateTime($message['sent']);?></b> 
        <a href="javascript:void(0)" onclick="conf('<?php echo BASE_URL.$class;?>/delmsg/<?php echo $message['id'];?>', '<?php echo T('del conf');?>')" class="fa-trash-o fa icon icon_sml"></a>	<br>
		<?php 
		foreach($msgdata as $k=> $v) { 
		$t = getFieldType($k, $fields); 
        $o = getFieldOptions($k, $fields);
		?>
		<?=T($k);?>: <b><?php echo fType($v, $t,$o);?></b><br>
		<?php } ?>
	</div> 
<?php } ?>