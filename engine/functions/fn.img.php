<?php
/**
 * Created by PhpStorm.
 * User: adiacom NUC1
 * Date: 12/8/2017
 * Time: 4:18 PM
 */



function createThumb($name,$target, $thumb_width, $thumb_height, $type, $newtype = null) {
	switch($type){
		case 'image/jpg':
		case 'image/jpeg':
			$src_img=imagecreatefromjpeg($name); $type = "jpg";
		break;
		
		case 'image/gif':
			$src_img=imagecreatefromgif($name); $type = "gif";
		break;
		
		case 'image/png':
			$src_img=imagecreatefrompng($name); $type = "png";
		break;
	}
	if($newtype == NULL) $newtype = $type;
	$filename = 'output.jpg';
	$width = imagesx($src_img);
	$height = imagesy($src_img);
	$original_aspect = $width / $height;
	$thumb_aspect = $thumb_width / $thumb_height;
	if ( $original_aspect >= $thumb_aspect )
	{
	   // If image is wider than thumbnail (in aspect ratio sense)
	   $new_height = $thumb_height;
	   $new_width = $width / ($height / $thumb_height);
	}
	else
	{
	   // If the thumbnail is wider than the image
	   $new_width = $thumb_width;
	   $new_height = $height / ($width / $thumb_width);
	}
	$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
	// Resize and crop
	imagecopyresampled($thumb,
					   $src_img,
					   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
					   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
					   0, 0,
					   $new_width, $new_height,
					   $width, $height);
					   
	switch($newtype){
		case 'jpg': imagejpeg($thumb,$target);  break;
		case 'gif': imagegif($thumb,$target);  break;
		case 'png': imagepng($thumb,$target); break;
	} 

	imagedestroy($thumb); 
	imagedestroy($src_img); 
}

const GFXDIR = 'gfx/galleries/';

function f($filename) {
    global $_FILES;
    if(isset($_FILES[$filename])) return $_FILES[$filename];
    return NULL;
}

function getImgById($id, $prefix = true) {
    $imgs = cache('galleries_images');
    if(!isset($imgs[$id])) return '';
    $img =  $imgs[$id];
    if($prefix) $img = BASEFMURL . GFXDIR . $img;
    return $img;
}

function getImgDataById($id) {
    $imgs = cache('galleries_images');
    $gals = cache('galleries');
    $img = $imgs[$id];
    $img['dir'] = $gals[$img['gal_id']]['slug'];
    return $img;
}

function getThumbById($id) {
    $imgs = cache('galleries_thumbs');
    if(isset($imgs[$id])) return $imgs[$id];
    return getImgById($id);
}

function getImg($img, $dir, $prefix = false) {
    $ret = $dir .'/' . $img;
    if($prefix) $ret = BASEFMURL . GFXDIR . $ret;
    return  $ret;
}

function getthumb($img, $dir = '', $echo = true, $prefix = false){
    $dir  = GFXDIR . $dir;
    $thumbpath = BASEFMDIR .  $dir . '/' . THUMB_PREFIX . $img;
    $thumburl = BASEFMURL .  $dir . '/' . THUMB_PREFIX . $img; //echo $thumburl; die();
    if(!file_exists($thumbpath)) {
        $thumbpath = BASEFMDIR .  $dir . '/' . $img;
        $thumburl = BASEFMURL .  $dir . '/' . $img;
    }
    if(file_exists($thumbpath)) {
        if($echo)
            return " style=\"background-image:url('$thumburl');\"";
        else
            return $thumburl;
    }

}

function img($gid, $iid, $echo = false, $prefix = false) {
    $gals = cache('galleries');
    $slug = $gals[$gid]['slug'];
    $imgs = cache('gal_' . $gid);
    $img = $imgs[$iid];
    $url = BASEFMURL . 'gfx/galleries/' . $slug . '/' . $img;
    if($echo)
        return " style=\"background-image:url('$url');\"";
    else
        return $url;
}
