<br>
<h1><?php echo $title;?>
<?php  echo btns($buttons['items'], $class);?></h1>
<?php
$statuses = ['danger','warning','success'];
$statustexts = ['not installed','installed','activated'];
 foreach (@$data as $module){
	$statusClass = $statuses[$module['status']];?>
	<div class="alert alert-<?php echo $statusClass;?> table">
		<div class="info td">
			<h2><a href="<?php echo BASE_URL . $module['name'];?>" class="alert-link"><?php echo T($module['name']);?></a></h2>
			<?php echo $module['description']; ?>
		</div>
		<div class="status td">
			<?php echo T($statustexts[$module['status']]);?>
		</div>
		<div class="btns td">
			<?php switch($module['status']) { 
					//Not installed
					case 0:?>
						<a class="btn btn-primary" href="javascript:changestatus('<?php echo $module['name'];?>', 1);"><?php echo T('Install');?></a>
			<?php  	break;
			
					//Installed, not activated
					case 1:?>						
						<a class="btn btn-success" href="javascript:changestatus('<?php echo $module['name'];?>', 2);"><?php echo T('Activate');?></a>
						<a class="btn btn-danger" href="javascript:changestatus('<?php echo $module['name'];?>', 0);"><?php echo T('Uninstall');?></a>
			<?php	break;

					case 2: ?>						
						<a class="btn btn-secondary" href="javascript:changestatus('<?php echo $module['name'];?>', 1);"><?php echo T('Deactivate');?></a>
						<a class="btn btn-danger" href="javascript:changestatus('<?php echo $module['name'];?>', 0);"><?php echo T('Uninstall');?></a>
			<?php  } ?>
		</div>
	</div>
<?php } ?>

<script>

function changestatus(module, status_id) {
	$.get('<?php echo BASE_URL;?>modules/changestatus/' + module + '/' + status_id)
		.done(function() {
			window.location.reload(0);
	});
}

</script>