<?php 
global $_PATH; 
$curr_path = $_PATH; unset($curr_path[0]);
$file = implode('/', $curr_path);
$path = BASEFMURL  . $file ;
 ?>
<!--<h1><?php echo $data['name'];?></h1><br>-->
<?php 
$ftype = explode('/', $data['filetype']);
switch($ftype[0]) {
	case 'image':
		echo '<img src="' . $path . '">';
	break;
	
	case 'video':
		echo '<video src="' . $path . '"></video>';
	break;
	
	default:
		$path = BASEFMDIR . implode('/', $curr_path);
		$content = htmlentities(file_get_contents($path)); ?>
		<form id="form" action="<?php echo BASE_URL;?>fileviewer/save">
			<input type="hidden" name="path" value="<?php echo $file ;?>"></input>
			<textarea name="content"><?php echo $content;?></textarea>
			<?php echo btn_submitForm(); ?>
			<div class="messages"></div>
		</form>
		<?php 
	break;	
	
} ?>