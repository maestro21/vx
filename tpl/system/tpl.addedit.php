<h1><?php echo T('settings');?></h1>
<form method="POST" id="form" class="content" action="<?php echo BASE_URL . $class;?>/save?ajax=1">
<?php $tabs = cache('settings'); $data =  cache('settings_data'); ?>
<div id="vue">
<?php echo form(['fields' => $tabs, 'data' =>$data, 'table' => false, 'plain' => false]); ?>
</div>
</form>