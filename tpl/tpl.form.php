<?php
if(!isset($prefix)) $prefix = 'form';

foreach($fields as $key => $field) {
    if(empty($key))        continue;
	$name = (isset($field['name']) ? $field['name'] : $key);
	$widget = @$field[0];
	$class = (isset($field['class']) ? ' ' . $field['class'] : '');
	$value = (isset($data[$key]) ? $data[$key] : (isset($field['default'])? $field['default'] : ""));
	$required = (@$field['required'] > 0);
	$uid = arr2line($prefix) . '-'. $key;

	Vdata($name, $value);
print_r($value);
	switch ($widget) {

		case WIDGET_TABS: ?>
			<v-tabs>
				<?php foreach($field['children'] as $tabname => $child) { ?>
					<v-tab ripple>
						<?php echo T($tabname);?>
					</v-tab>
				<?php } ?>
				<?php foreach($field['children'] as $tabname => $child) { ?>
					<v-tab-item>
						<v-card flat>
						 	<v-card-text>
								<?php echo form([
									'fields' => $child,
									'data' => $data
								]); ?>
							</v-card-text>
						</v-card>
					</v-tab-item>
				<?php } ?>
			</v-tabs>
		<?php
		break;


    case WIDGET_TABLE: print_r($field);

		case WIDGET_TEXT:
		default: ?>

    			<v-text-field
    			   value="<?php echo $value;?>"
    			  label="<?php echo $name;?>"
    			></v-text-field>
		<?php break;
	}
} ?>
