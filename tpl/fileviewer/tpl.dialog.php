<?php
$do = $data['action'];
$path = trim($data['path'], '/');
$files = $data['files'];
/** all **/

if($do == 'upfile') {
?>
	<h2><?php echo T('upload files');?></h2>
		<div class="dropzone"></div>
		<script>
			$('div.dropzone').dropzone({
				url: "<?php echo BASE_URL;?>fileviewer/upload?path=<?php echo $path;?>",
				init: function() {
					this.on('success', function(file) {
						var thumbname = '<?php echo BASEFMURL . $data['path'] . '/' . THUMB_PREFIX;?>' + file.name; console.log(thumbname);						
						//this.createThumbnailFromUrl(file, thumbname);
						file.previewElement.querySelector("img").src = thumbname;
					});
				}
			});
		</script>
<?php } else { ?>

<form id="form" action="<?php echo BASE_URL;?>fileviewer/<?php echo $do;?>">
<input type="hidden" name="path" value="<?php echo $path;?>">
<input type="hidden" name="files" value="<?php echo implode(',',$files);?>">
<?php 
$text = $do;
switch($do) {
	
	case 'mkdir': 
		$text = 'create';?>
		<h2><?php echo T('create directory');?></h2>
		<?php echo T('dirname');?>: <input class="dirname" name="dirname"> 
	<?php break;	
	
	
	case 'mkfile':$text = 'create'; ?>
		<h2><?php echo T('create file');?></h2>
		<?php echo T('filename');?>: <input class="filename" name="filename">
	<?php break;
	
	
	/** files **/
	
	
	case 'copytodir':$text = 'copy'; ?>
		<h2><?php echo T('copy to directory');?></h2>
		<?php echo T('Copy files or directories to a new directory');?><br>
		<?php echo T('Directory');?>: 
		<select class="dir" name="dir">
			<?php echo drawdirs(); ?>
		</select>
	<?php break;

	case 'movetodir': $text = 'move'; ?>
		<h2><?php echo T('move to directory');?></h2>
		<?php echo T('Move file(s) or directory to a new directory');?><br>
		<?php echo T('Directory');?>: 
		<select class="dir" name="dir">
			<?php echo drawdirs(); ?>
		</select>
	<?php break;
	
	/** file **/
	
	case 'copy': ?>
		<h2><?php echo T('copy');?></h2>
		<?php echo T('Copy file or directory.');?><br>
		<?php echo T('new name');?>: <input class="filename" name="filename">	
	<?php break;

	case 'rename': ?>
		<h2><?php echo T('rename');?></h2>
		<?php echo T('Rename file or directory');?><br>
		<?php echo T('new name');?>: <input class="filename" name="filename">
	<?php break; 
	} ?>
	<?php echo btn_submitForm($text); ?>

 <div class="messages"></div>
</form>

<?php } ?>

