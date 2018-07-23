<?php 
	//$key = ((empty($key) && $key !== '0') ? '{key}' : $key); 
	$type = (isset($data['type']) ? $data['type'] : 1);
	$typename = $widgets[$type];
?>
<div class="tr">
	<div class="td">
		<input name="form[fields][<?php echo $key;?>][label]" value="<?php echo @$data['label'];?>">
	</div>
	<div class="td">
		<select  name="form[fields][<?php echo $key;?>][type]" >
			<?php foreach($widgets as $widget => $wname) { ?>
				<option value="<?php echo $widget;?>" 
					<?php if($type == $widget) echo " selected='selected'";?>><?php echo T($wname);?>
				</option> 
			<?php } ?>
		</select>
	</div>	
	<?php 
		$langs = getLangs();	
		foreach($langs as $lang) {
			$lkey = $lang['abbr'];
			$value = @$data[$lkey]; 
			if($typename == 'keyvalues' && is_array($value)) {
				$values = array();
				foreach($value as $k => $v) {
					$values[] = $k . '=' . $v;
				}
				$value = implode(PHP_EOL, $values);
			}
			?>
			<div class="td">
				<?php if($typename == WIDGET_TEXT) { ?>
				<input name="form[fields][<?php echo $key;?>][<?php echo $lkey;?>]" value="<?php echo $value;?>">
				<?php } else { ?>
				<textarea name="form[fields][<?php echo $key;?>][<?php echo $lkey;?>]" rows=5 cols=20><?php echo $value;?></textarea>
				<?php } ?>
			</div>	
			
	<?php	}?>
	<div  class="td">
		<div class="btn" onclick="javascript:$(this).closest('.tr').remove();"><?php echo T('del');?></div>	
	</div>
</div>	