<?php 


include('../engine/autoload.php');
val('lang', get('lang'));
S('labels', cache('i18n'));


ob_start(); ?>
var base_url = '<?php echo BASE_URL;?>';

<?php
$tp = '../' . tpath() . 'script.php';  
if(file_exists($tp)){
	include($tp);
}


$js = dir_list('js');
foreach($js as $file) {
	include('js/' . $file); 
}



