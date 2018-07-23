<?php 
if(!isset($script)) $script = true;
if(!isset($prefix)) $prefix = 'form';
if(!isset($plain)) $plain = false;
if(!isset($table)) $table = true;
if(!isset($btn)) $btn = true;
if($plain) { ?>
	<div class='tr'<?php if(isset($rowid)) echo " data-rowid='" . $rowid . "'";?>>
<?php }
if($table) { echo "<table cellpadding=0 cellspacing=0>"; }

foreach($fields as $key => $field) { 
    if(empty($key))        continue;
	$name = (isset($field['name']) ? $field['name'] : $key);
	$widget = @$field[0]; inspect($widget);
	$class = (isset($field['class']) ? ' ' . $field['class'] : '');
	$value = (isset($data[$key]) ? $data[$key] : (isset($field['default'])? $field['default'] : "")); 
	$required = (@$field['required'] > 0);
	$uid = arr2line($prefix) . '-'. $key;


	if(isset($field['options'])) {
        if(!is_array($field['options'])) $field['options'] = keyvalues($field['options']);
        $options[$key] = $field['options'];
    }
       

	if(!$plain) { ?>
	<tr>
		<?php
			switch ($widget) {

				case WIDGET_TABS: ?>
					<ul class="nav nav-tabs">
						<?php $first = true; foreach($field['children'] as $tabname => $child) { ?>
						<li class="nav-item">
							<a class="nav-link<?php if($first) echo ' active';?>" 
								id="tab_<?php echo $tabname;?>"
								href="#tabpane_<?php echo $tabname;?>"
								data-toggle="tab"  
								aria-controls="tabpane_<?php echo $tabname;?>" 
								aria-selected="<?php if($first) echo 'true'; else echo 'false';?>">
								<?php echo T($tabname);?>										
							</a>
						</li>
						<?php $first=false; } ?>
					</ul>
					<div class="tab-content">
						<?php $first = true; foreach($field['children'] as $tabname => $child) {  ?>
						<div id="tabpane_<?php echo $tabname;?>" 
							class="tab-pane<?php if($first){ $first = false; echo ' active'; } ?>"
							role="tabpanel" aria-labelledby="tabpane_<?php echo $tabname;?>">
							<?php echo form([
								'fields' => $child,
								'data' => $data,
								'script' => false,
								'btn' => false,
								'table' => false
							]); ?>
					  	</div>					  	
						<?php $first=false;  } ?>
					</div>
					<?php
				break;

				case WIDGET_ARRAY:
					$_prefix = $prefix . "[$key]";
					if(!is_array($value)) $value = unserialize($value);
					?><td colspan=2>
					<fieldset class="fieldset_<?php echo $key;?>">
						<legend><h3><?php echo T($key);?></h3></legend>
						<table>
						<?php echo form([
							'fields' => $field['children'],
							'data' => $value,
							'prefix' => $_prefix,							
							'btn' => false,
						]); ?>
						</table>
					</fieldset></td>
				<?php break;

				case WIDGET_TABLE: if(!is_array($value)) $value = unserialize($value);
					$_prefix = $prefix . "[$key]"; 
					?>
					<?php if($table) { ?> <td colspan=2> <?php } ?>
						<h3><?php echo T($key);?></h3>
						<div class="table tbl-<?php echo $key;?>">
							<div class='tr thead'>
								<?php
								// head
								foreach($field['children'] as $_field => $_f){
									echo "<div class='td'>" . T($_field) . "</div>";
								} ?>
								<div class='td'></div>
							</div>
							<?php
							// body
							$__i = 0;
							if(is_array($value)) {
								if(isset($value['{key}'])) unset($value['{key}']);
								foreach (@$value as $__row) {
									echo form([
										'fields' => $field['children'],
										'data' => $__row,
										'plain' => true,
										'table' => false,
										'prefix' => $_prefix . "[$__i]",
										'rowid' => $__i
									]);
									$__i++;
								}
							} ?>
							<div class="field-<?php echo $key;?> hidden">
								<?php echo form([
									'fields' => $field['children'],
									'plain' => true,
									'prefix' => $_prefix . "[{key}]"
								]); ?>
							</div>
						</div>
						<div class="btn addField-<?php echo $key;?>" >
							<?php echo T('add') . ' ' . T($key);?>
						</div>
						<script>
							var <?php echo $key;?>key = <?php echo (int)@$__i;?>;
							$( document ).ready(function() {
								$('.addField-<?php echo $key;?>').click(function (e) {
									str = $('.field-<?php echo $key;?>').html().replace(/{key}/g, <?php echo $key;?>key);
									<?php echo $key;?>key++;
									$('.tbl-<?php echo $key; ?>').append(str);
									bindForm();
								});
							});
						</script>
					 <?php
					//die();
					break;

				case WIDGET_HIDDEN:
					break;

				case WIDGET_HTML:
				case WIDGET_TEXTAREA:
				case WIDGET_BBCODE:
					echo "<td colspan=2>";
					break;

				default:
					echo "<td class='lbl'>" . T($name) . ":" . ($required ? "<sup>*</sup>" : '') . "</td><td>";
					break;
			}
		} else { ?>
			<div class='td <?php echo $class;?>'>
		<?php }

		switch($widget) {

		case WIDGET_PLAINCODE: echo $value; break;

		case WIDGET_BTN: ?>
			<div class="btn <?php echo $key;?>" onclick="serveBtn(this,'<?php echo $key;?>')" data-val="<?php echo $value;?>"><?php echo T($key);?></div>

		<?php	break;

		case WIDGET_COLOR: ?>
				<input type="text" maxlength="6"  class="jscolor" name="<?php echo $prefix;?>[<?php echo $key;?>]" value="<?php echo $value;?>">
			<?php break;

			case WIDGET_CHAR: ?>
				<input type="text" maxlength="1" class="char" name="<?php echo $prefix;?>[<?php echo $key;?>]" value="<?php echo $value;?>">
				<?php break;

			case WIDGET_COORDS:
				$x = T('X'); $y = T('Y');
				$coords = true;
			case WIDGET_SIZE:
				if(!@$coords) {
					$x = T('width');
					$y = T('height');
				}
			?>
			<?php echo $x;?>: <input type="text" class="sml"  id="<?php echo $uid;?>-x" name="<?php echo $prefix;?>[<?php echo $key;?>][x]" value="<?php echo (int)@$value['x'];?>">
			<?php echo $y;?>: <input type="text" class="sml"  id="<?php echo $uid;?>-y" name="<?php echo $prefix;?>[<?php echo $key;?>][y]" value="<?php echo (int)@$value['y'];?>">

			<?php break;

            // lets do it from gallery only
			case WIDGET_SELECTFILE: {
				$thumb = (bool)@$field['thumb'];
				?>
				<div class="table">
					<div class="tr">
						<div class="td">
							<input type="<?=($thumb?'hidden':'text');?>" id="<?php echo $uid;?>" value="<?php echo $value;?>"	name="<?php echo $prefix;?>[<?php echo $key;?>]">
							<i class="fa fa-plus icon file" onclick="selectFile('<?php echo $field['src'];?>', '<?php echo $uid;?>')"></i>
						</div>
				<?php if($thumb) { ?>
					<div class="td">
					<div class="crop" style="width:<?php echo (int)$field['thumbsize'];?>px;height:<?php echo (int)$field['thumbsize'];?>px;">
                        <img src="<?php echo getThumbById($value);?>" id="<?php echo $uid;?>-thumb">
					</div>
					</div>
				<?php } ?>
				</div></div>
			<?php }
			break;

			case WIDGET_FILE:  ?>
				<div>
					<label for="<?php echo $uid;?>"><i class="fa fa-plus icon file"></i></label>
					<input type="file" name="<?php echo $uid;?>" id="<?php echo $uid;?>" />
					<span class="name">
						<?php if(empty($value)) {
							echo T('no file selected');
						} else {
							$filename = BASEFMURL .$value['name'];
							$thumb = $value['thumb'];
							$type = $value['type']; ?>
							<input type="hidden" value="<?php echo $value['name'];?>"	name="<?php echo $prefix;?>[<?php echo $key;?>][name]">
							<input type="hidden" value="<?php echo $thumb;?>"	name="<?php echo $prefix;?>[<?php echo $key;?>][thumb]">
							<input type="hidden" value="<?php echo $type;?>"	name="<?php echo $prefix;?>[<?php echo $key;?>][type]">

								
								
							<a href="<?php echo $filename; ?>" target="_blank">
								<?php
								if ($type == 'image') {
									if (!$thumb) $thumb = $filename; else $thumb = BASEFMURL . $thumb; ?>
									<img src="<?php echo $thumb;?>">
								<?php } else { ?>
									<?php echo $filename;?></a>
								<?php }
							}?>
						<a class="fa fa-delete icon delfile"></a>
					</span>
				</div>
			<?php 	
			break;
			
			case WIDGET_INFO: ?>
				<?php echo $value;?>
				<input type="hidden"
					value="<?php echo $value;?>"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"<?php if($required) echo " required";?> />


			<?php break;


			case WIDGET_TEXT: ?>
				<input type="text"
					value="<?php echo $value;?>"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"<?php if($required) echo " required";?> />
			<?php break;

			case WIDGET_SLUG: ?>
				<input type="text"
					class="slug"
					value="<?php echo $value;?>"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"<?php if($required) echo " required";?> />
			<?php break;

			case WIDGET_EMAIL: ?>
				<input type="email"
					value="<?php echo $value;?>"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"<?php if($required) echo " required";?> />
			<?php break;

			case WIDGET_PHONE: ?>
				<input type="tel"
					class="phone"
					value="<?php echo $value;?>"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"<?php if($required) echo " required";?> />
			<?php break;

			case WIDGET_NUMBER: ?>
				<input type="number"
					value="<?php echo $value;?>"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"<?php if($required) echo " required";?> />
			<?php break;

			case WIDGET_URL: ?>
				<input type="url"
					value="<?php echo $value;?>"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"<?php if($required) echo " required";?> />
			<?php break;

			case WIDGET_LIST:
			case WIDGET_KEYVALUES:
				/*$values = array(); 
                if($value) {
                    foreach($value as $k => $v) {
                        if($widget == WIDGET_KEYVALUES) {
                            $v = $k . '=' . $v;
                        }
                        $values[] = $v;
                    }
                    $value = implode(PHP_EOL, $values);
                }    */
			?>  
                <div class="btn" onclick="$(this).parent().find('textarea').toggle()"><?php echo T('list');?></div>
				<textarea<?php if($required) echo " required";?> class="list"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"><?php echo $value;?></textarea>
			<?php break;

			case WIDGET_TEXTAREA: ?>
				<?php echo T($key);?>:<br>
				<textarea<?php if($required) echo " required";?> cols="100" rows="10"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"><?php echo $value;?></textarea>
			<?php break;

			case WIDGET_HTML:  ?>
				<?php echo T($key);?>:<br>
				<textarea<?php if($required) echo " required";?> class="html" cols="100" rows="10"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"><?php echo $value;?></textarea>

			<?php
				/*include('external/maestroeditor/editor.php');
				maestroeditor($key, $key, $value);

			/* ?>
				<?php echo T($key);?>:<br>
				<textarea cols="100" rows="20"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $key;?>"><?php echo $value;?></textarea>
				<script type="text/javascript">
					<!--CKEDITOR.replace( "<?php echo $key;?>" );-->
					bkLib.onDomLoaded(function() {
						new nicEditor({fullPanel : true,maxHeight : 600}).panelInstance('<?php echo $key;?>');
					});
				</script>
			<?php break;	/**/
			break;

			case WIDGET_BBCODE: ?>
				<?php echo T($key);?>:<br>
				<textarea<?php if($required) echo " required";?> cols="100" rows="15"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>"><?php echo $value;?></textarea>
				<script type="text/javascript">
					CKEDITOR.config.toolbar_Full = [
						["Source"],
						["Undo","Redo"],
						["Bold","Italic","Underline","-","Link", "Unlink"],
						["Blockquote", "TextColor", "Image"],
						["SelectAll", "RemoveFormat"]
					] ;
					CKEDITOR.config.extraPlugins = "bbcode";
					//<![CDATA["
						var sBasePath = document.location.pathname.substring(0,document.location.pathname.lastIndexOf("plugins")) ;
						var CKeditor = CKEDITOR.replace( "<?php echo $key;?>", {
								customConfig : sBasePath + "plugins/bbcode/_sample/bbcode.config.js"
						}  );
					//]]>
				</script>
			<?php break;

			case WIDGET_PASS: ?>
				<input type="password"<?php if($required) echo " required";?> value="" name="<?php echo $prefix;?>[<?php echo $key;?>]" id="<?php echo $uid;?>" />";
			<?php break;

			case WIDGET_HIDDEN: ?>
				<input type="hidden"
					value="<?php echo $value;?>"
					name="<?php echo $prefix;?>[<?php echo $key;?>]"
					id="<?php echo $uid;?>" />
			<?php break;

			case WIDGET_CHECKBOX: ?>
				<!--<input type="hidden" name="<?php echo $prefix;?>[<?php echo $key;?>]" value="">-->
				<input type="checkbox"<?php if($required) echo " required";?>  value=1 name="<?php echo $prefix;?>[<?php echo $key;?>]" id="<?php echo $uid;?>"
				<?php if($value == 1) echo " checked";?> />
			<?php break;

			case WIDGET_RADIO: ?>
                <ul class="radiolist">
			<?php
				if(is_array($options) && sizeof($options) > 0) {
					foreach (@$options[$key] as $kk => $vv){ ?>
						<li><input type="radio"
							name="<?php echo $prefix;?>[<?php echo $key;?>]"
							value="<?php echo $kk;?>"<?php if($required) echo " required";?>
							<?php if($kk == $value) echo " checked";?> /> 
                            <?php echo T($vv);?>
				<?php } ?>
			<?php } ?>
                </ul>
			<?php break;

			case WIDGET_SELECT:?>
				<select name="<?php echo $prefix;?>[<?php echo $key;?>]" class="select <?php echo $key;?>" id="<?php echo $uid;?>">
				<?php
					if(is_array($options) && sizeof($options) > 0) {
						foreach (@$options[$key] as $kk => $vv){ echo $kk; ?>
							<option value="<?php echo $kk;?>"
								<?php if($kk == $value) echo " selected='selected'";?>><?php echo T($vv);?>
							</option>
					<?php } ?>
				<?php } ?>
				</select>
			<?php break;

			case WIDGET_MULTSELECT: ?>
				<select multiple<?php if($required) echo " required";?> name="<?php echo $prefix;?>[<?php echo $key;?>][]" id="<?php echo $uid;?>">
				<?php
					$dat = array_flip(explode(",", $value));
					if(is_array($options) && sizeof($options) > 0) {
						foreach (@$options[$key] as $kk => $vv){ ?>
							<option value="<?php echo $kk;?>"
								<?php if(isset($dat[$kk])) echo " selected='selected'";?>><?php echo T($vv);?>
							</option>
					<?php } ?>
				<?php } ?>
				</select>
			<?php break;

			case WIDGET_DATE:
			case WIDGET_TIME:
			case WIDGET_DATETIME: ?>
				<div class="input-group date" id="<?php echo $key;?>" data-target-input="nearest">
					 <input type="text" class="form-control datetimepicker-input" data-target="#<?php echo $key;?>"/>
                    <div class="input-group-append" data-target="#<?php echo $key;?>" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
			<?php break;

			 /*
			case WIDGET_DATE:
				preg_match_all("/[[:digit:]]{2,4}/", $value, $matches);
				$nums = $matches[0]; ?>
				<input type="text" class="date year" name="<?php echo $prefix;?>[<?php echo $key;?>][y]"
					value="<?php echo (isset($nums[0])?$nums[0]:date("Y"));?>" size="4">-
				<select name="<?php echo $prefix;?>[<?php echo $key;?>][m]>">
					<?php if(!isset($nums[1])) $nums[1] = date("m");
					for($i=1;$i<13;$i++) { ?>
						<option value="<?php echo $i;;?>"<?php if($i==@$nums[1]) echo ' selected="selected"';?>><?php echo T("mon_$i");?>
					</option>
					<?php } ?>
				</select>
				<input type="text" class="date" name=<?php echo $prefix;?>[<?php echo $key;?>][d] value="<?php echo (isset($nums[2])?$nums[2]:date("d"));?>" size=2> (YYYY-MM-DD)

			<?php break;


			case WIDGET_TIME:
				preg_match_all("/[[:digit:]]{2,4}/", $value, $matches);
				$nums = $matches[0]; ?>
				<input type="text" class="date" name=<?php echo $prefix;?>[<?php echo $key;?>][h] value="<?php echo (isset($nums[0])?$nums[0]:date("G"));?>" size=2>:
				<input type="text" class="date" name=<?php echo $prefix;?>[<?php echo $key;?>][mi] value="<?php echo (isset($nums[1])?$nums[1]:date("i"));?>" size=2>:
				<input type="text" class="date" name=<?php echo $prefix;?>[<?php echo $key;?>][s] value="<?php echo (isset($nums[2])?$nums[2]:date("s"));?>" size=2>(HH:MM:SS)

			<?php break;

			case WIDGET_DATETIME: ?>
			<div class="form-group">
                <div class="input-group datetime" id="<?php echo $uid;?>" data-target-input="nearest">
                    <input type="text" name=<?php echo $prefix;?>[<?php echo $key;?>] value="<?php echo $value;?>" class="form-control datetimepicker-input" data-target="#<?php echo $uid;?>"/>
                    <div class="input-group-append" data-target="#<?php echo $uid;?>" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
			<?php break; */

			case WIDGET_CHECKBOXES:
				$i = 0;
				$dat = array_flip(explode(",",@$data[$key]));?>
				<div>
				<?php foreach (@$options[$key] as $kk => $vv){
					if($i % 10 == 0){  ?>
						</div><div style="float:left;border:1px black solid;">
					<?php } ?>
					<p><input type="checkbox" value="$kk" name="<?php echo $prefix;?>[<?php echo $key;?>][]"
						<?php if(isset($dat[$kk])) echo " checked";?>><?php echo T($vv);?></p>
					<?php $i++;
				} ?>
				</div>
			<?php break;

		} ?>
		<label for="<?php echo $uid;?>"></label>

	<?php if(!$plain) { ?>
		</td></tr>
	<?php	} else { ?>
		</div>
	<?php } ?>
<?php }
if($plain) { ?>
	<div class="td">
		<div class="btn" onclick="javascript:delRow($(this).closest('.tr'));"><?php echo T('del');?></div>
	</div>
</div>
<?php } elseif($table) { echo "</table>"; }   ?>
<?php if($btn) { ?>
<div class="">
	<div class="btn btn-primary submit"><?php echo T('save');?></div>
	<div class="messages"></div>
</div>
<?php } ?>