<?php 
if($larr) {
	echo "<a title='". T('first')."' ". str_replace('{p}', 1, $url) . ">&larr;</a>";
} 
foreach ($range as $p){
	if($p == $page)
		echo "<a href='javascript:void(0)' class='current_page'>$p</a>";
	else	
		echo "<a ". str_replace('{p}', $p, $url) . ">$p</a>";
}
if($rarr) {
	echo "<a title='". T('last')."' ". str_replace('{p}', $pages, $url) . ">&rarr;</a>";
} 	
	