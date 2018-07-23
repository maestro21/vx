<h2 class="path">
	<?php
	$arr = false;
	$__path = ''; 
	foreach($path as $_p) {
		$__path .=  $_p . '/';
		if(!$arr) $arr = true; else echo "&rarr;";
		echo "<a href='" . BASE_URL . $__path . "'>$_p</a>";
	}
	$curr_path = $path; 
	unset($curr_path[0]); 
	$curr_path = implode('/', $curr_path);
	$curr_dir = BASEFMDIR . implode('/', $path);
	$curr_url = tslash(BASE_URL . 'fileviewer/' . $curr_path);
	?>
</h2>

<h1 class="buttons">
	<div class="btn-all">		
		<i class="fa fa-upload icon hand upfile" title="<?php echo T('upload file');?>"></i>
		<i class="fa fa-folder icon hand mkdir" title="<?php echo T('create folder');?>"></i>
		<i class="fa fa-file icon hand mkfile" title="<?php echo T('create file');?>"></i>
	</div>
	<div class="btn-files">
		<i class="fa fa-trash icon hand del" title="<?php echo T('delete');?>"></i>	
		<i class="fa fa-clone icon hand copytodir" title="<?php echo T('copy to dir');?>"></i>
		<i class="fa fa-files-o icon hand movetodir" title="<?php echo T('move to dir');?>"></i>
	</div>
	<div class="btn-file">
		<i class="fa fa-clone icon hand copy" title="<?php echo T('copy');?>"></i>
		<i class="fa fa-file-word-o icon hand rename" title="<?php echo T('rename');?>"></i>
		<i class="fa fa-eye icon hand open" title="<?php echo T('view file');?>"></i>
	</div>
</h1>

<div>
<?php foreach ($data as $file) { ?>
	
	<?php if($file['is_dir']) { ?>
	<div class="filenode dir">
		<p class="name"><b><a href="<?php echo $curr_url . $file['name']; ?>"><?php echo $file['name']; ?></a></b></p>
		<p><?php echo fdateunix($file['filemtime']); ?></p>
		<p><?php echo T('files', $file['files'], true); ?></p>
	</div>
	<?php } else { 
		$filetype = processFileType($file['filetype']); 
		if($filetype == 'image' && (0 === strpos($file['name'], THUMB_PREFIX))) continue;
	?>
	<div class="filenode <?php echo $filetype;?>"<?php echo getThumb($file['name'], $curr_path);?>>
		<p class="name file"><b><?php echo $file['name']; ?></b></p>
		<p><?php echo fdateunix($file['filemtime']); ?></p>
		<p><?php echo formatBytes($file['size']); ?></p>
	</div>
	<?php } ?>
	
		
<?php } ?>

</div>

<style>
.dropzone.dz-clickable {
    min-width: 500px;
}


</style>

<script>



$( document ).ready(function() {
    $('.filenode').each(function() {
		$(this).click(function() {
			$(this).toggleClass('selected');
			toggleButtons();
		});
	});
	
	$('.name.file').click(function() {
		$('.selected').removeClass('selected');
		$(this).parent().addClass('selected');
		openFile();
	});
	
	toggleButtons();
	
	$('.buttons i').each(function() {
		$(this).click(function() {
			var command = $(this).attr('class').split(' ');
			command = command[command.length - 1];
			switch(command) {
				case 'open': 
					openFile();
				break
			
				case 'del':
					if(confirm('<?php echo t('conf_delete');?>')){
						var data = {
							'path':'<?php echo $curr_path;?>',
							'files': getSelectedFiles(),
						};
						$.post('<?php echo BASE_URL . 'fileviewer/del';?>?ajax=1', data)
						.done(function( data ) {
							processResponse(data);
						});
					}
				break;
			
				default:
					dialog(command);
				break;	
			}	
		});
	});
	
	
	function openFile() {	
		var url = getSelectedFiles();
		url = '<?php echo $curr_url;?>' + url;
		console.log(url);
		modal(url);	
	}
	
	function toggleButtons() {		
		$('.buttons div').hide();
		$('.btn-all').show();
		var length = $('.selected').length;
		if(length > 0) $('.btn-files').show();
		if(length == 1) $('.btn-file').show();
	}
	
	function dialog(action) { 
		var data = {
			'path':'<?php echo $curr_path;?>',
			'files': getSelectedFiles(),
			'action': action,
		};
		modal('<?php echo BASE_URL;?>fileviewer/dialog', data);
	}
	
	function getSelectedFiles() {
		var result = $(".selected .name b").map(function() {
                 return $(this).text();
              }).get();
		return result;	  
	}
	
});
</script>