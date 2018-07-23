<?php header("Content-type: text/css"); 

$rebuild = (!file_exists('style.css')); 
$rebuild = true;



/* rebuild css if needed */
if($rebuild) {	
	include('../engine/autoload.php');

	ob_start();

	/* FA */
	$fa = file_get_contents('../' . EXT_FOLDER . 'fa/css/font-awesome.min.css'); 
	$fa = str_replace('..', '../' . EXT_FOLDER . 'fa', $fa);
	echo $fa;
	
	/* dropzone */
	include('../' . EXT_FOLDER . 'dropzone/dropzone.css');
   // include('../' . EXT_FOLDER . 'filestyle/jquery-filestyle.css');

	/* css params */
	$mainColor = '#000'; //$system['mainColor']; //'#222';
	$mainColor2 = '#a00';
	$textColor = '#222';
	$bgColor = 'white';

	/* theme */
	$tp = '../' . tpath() . 'style_vars.php';  
	if(file_exists($tp)){
		include($tp);
	}
	
	/* main css */
	$css = dir_list('css');
	foreach($css as $file) {
		include('css/' . $file); 
	}

	
	/* theme */
	$tp = '../' . tpath() . 'style.css.php';  
	if(file_exists($tp)){
		include($tp);
	}
	
	
	$data = ob_get_contents(); 
	file_put_contents('style.css', $data);
	ob_end_clean();		
}


include('style.css');