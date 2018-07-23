<?php
/**
 * Created by PhpStorm.
 * User: adiacom NUC1
 * Date: 12/7/2017
 * Time: 5:58 PM
 * Frontend functions
 */


/**
 * Template from uploads module
 * @param $name
 * @param null $data
 * @return bool|string
 */
function mtpl($name, $data = null) {
    $fpath = UPLOADS_FOLDER . 'tpl/' . $name . EXT_TPL;
    if(!file_exists($fpath)) return FALSE;

    if($data) {
        foreach ($data as $k =>$v){
            if(!is_array($v) && !is_object($v))
                $$k=html_entity_decode(stripslashes($v));
            else
                $$k=$v;
        }
    }

    ob_start();
    include($fpath);
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}
function form($fields, $data) {
    return tpl('form', ['fields' => $fields, 'data' => $data]);
}

function mform($id) {
    return call('forms', 'view', $id);
}

function slider($id) {
    return call('galleries','slider', $id);
}


function parse_tags($sText) {
    $sPattern = "/{{(.*?)}}/s";
    preg_match_all($sPattern, $sText, $aMatches);
    foreach($aMatches[1] as $sMatch) {
        $aMatch = explode('|', $sMatch);
        $sPattern = '{{' . $sMatch . '}}';
        $replace = '';
        switch($aMatch[0]) {
            case 'slider':
                $replace = slider($aMatch[1]);
                break;
            case 'tpl':
                $replace = mtpl($aMatch[1]);
                break;
            case 'form':
                $replace = form($aMatch[1]);
                break;
            case 'var':
                $replace = constant($aMatch[1]);
                break;
            case 'icon':
                $replace = '<i class="icon fa ' . $aMatch[1] .'"></i>';
                break;
        }
        $sText = str_replace('{{' . $sMatch . '}}', $replace, $sText);
    }
    return $sText;
}

/**
  * Returns file url based on theme
  **/
function tdir() {
    $theme = (G('theme') != '' ? G('theme') : DEFTHEME);
    return  THEME_FOLDER . $theme . '/';
}
function turl() {
    return BASE_URL . tdir();
}
function tpath() {
    return BASE_FOLDER . tdir();
}



function fullpath() {
    global $_PATH;
    return BASE_URL . implode('/',$_PATH);
}

/** URL redirect fuctions  **/

function redirect($to,$time=0, $relative = false){
    $to = str_replace('#','', $to); if($relative) $to = fullpath()  . '/' . $to;
    echo "<html><body><script>setTimeout(\"location.href='$to'\", {$time}000);</script></body></html>";
    if($time==0) die();
}

function goBack(){
    global $_SERVER;
    redirect($_SERVER['HTTP_REFERER']);
}






function themePath() {
    $theme = (G('theme') != '' ? G('theme') : DEFTHEME);
    return THEME_FOLDER . $theme . '/';
}


function treeDraw($data, $tpl='', $eval = ''){
    $ret = '';
    foreach ($data as $k => $row){
        if($eval !='') eval($eval);
        $_T = $tpl; //echo $_T;
        if($row['children']!='')
            $row['children'] = treeDraw($row['children'],$tpl);

        foreach ($row as $kk => $vv){
            $_T = str_replace('%'.$kk, $vv, $_T);
        }
        $ret .=$_T;
    }
    return $ret;
}

/* tags */

function BB($text)	{
    //inspect($text);

    $text = preg_replace('/\[(\/?)(b|i|u|s|center|left|right)\s*\]/', "<$1$2>", $text);

    $text = preg_replace('/\[code\]/', '<pre><code>', $text);
    $text = preg_replace('/\[\/code\]/', '</code></pre>', $text);

    $text = preg_replace('/\[(\/?)quote\]/', "<$1blockquote>", $text);
    $text = preg_replace('/\[(\/?)quote(\s*=\s*([\'"]?)([^\'"]+)\3\s*)?\]/', "<$1blockquote>Цитата $4:<br>", $text);

    //$text = preg_replace('/\[url\](?:http:\/\/)?([a-z0-9-.]+\.\w{2,4})\[\/url\]/', "<a href=\"http://$1\">$1</a>", $text);
    /*$text = preg_replace('/\[url\s*\](?:http:\/\/)?([^\]\[]+)\[\/url\]/', "<a href=\"http://$1\" target='_blank'>$1</a>", $text);
    $text = preg_replace('/\[url\s?=\s?([\'"]?)(?:http:\/\/)?(.*)\1\](.*?)\[\/url\]/s', "<a href=\"http://$2\" target='_blank'>$3</a>", $text);*/
    $text = preg_replace("/\[url\](.*?)\[\/url\]/si","<a href=\\1 target=\"_blank\">\\1</a>",$text);
    $text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/si","<a href=\"\\1\" target=\"_blank\">\\2</a>",$text);

    $text = preg_replace('/\[img\s*\]([^\]\[]+)\[\/img\]/', "<img src='$1'/>", $text);
    $text = preg_replace('/\[img\s*=\s*([\'"]?)([^\'"\]]+)\1\]/', "<img src='$2'/>", $text);
    //inspect($text); die();

    $text = preg_replace_callback("/\[video\](.*?)\[\/video\]/si","parse_video_tag",$text);

    return nl2br($text);
}


function parse_video_tag($matches){
    $url = $matches[1];
    return '<div>'.parse_video($url).'</div>';
}

function parse_video($url,$title = '') {
    $site = parse_url($url);

    $query = explode($site['query']);
    $host = str_replace('www.','',$site['host']);

    if($host == 'local') {
        $id = str_replace('/','',$site['path']);
        $video = DBrow(sprintf("SELECT * FROM videos WHERE id=%d",$id));
        return parse_video($video['url'],$video['title']);
    }


    switch($host) {
        case 'youtube.com':
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                $video_id = $match[1];
            }
            $vurl = "http://www.youtube.com/v/$video_id&autoplay=1";
            $ret  = "<a href='$vurl' rel=\"shadowbox['field_video']\"><img src='http://img.youtube.com/vi/$video_id/0.jpg' width=400 height=300></a>"; //die();

            break;
    }

    if($ret != ''){
        if($title != '') {
            $ret = "<a href='$url' target='_blank'><b>$title</b></a><br>" . $ret;
        }
        return $ret;
    }
}


function pagination($data) {
    return tpl('pagination', $data);
}



function menu() {
    return M('pages')->menu();
}



function btns($buttons, $cl = '',  $params = array()) {
    $html = '';
    if(is_array($buttons) && sizeof($buttons) > 0) {
        foreach($buttons as $button => $text) {
            if(is_array($params) && sizeof($params) > 0) {
                foreach($params as $k => $v) {
                    $button = str_replace('{' . $k . '}', $v);
                }
            }
            $url = BASE_URL . $cl . '/' . $button;
            if (0 === strpos($text, 'fa-')) {
                $html .= "<a href='$url' class='fa $text icon'></a>";
            } else {
                $html .= "<a href='$url' class='btn btn-primary'>" . T($text) . "</a>";
            }
        }
    }
    return $html;
}




// return message
function msg($status, $message, $redirect = false) {
    $arr = array('status' => $status, 'message' => $message);
    if($redirect) $arr['redirect'] = $redirect;
    echo json_encode($arr); die();
}



function btn_submitForm($text = 'save', $form = 'form') {
    echo "<a class='btn submit' href='javascript:sendFormById(\"$form\");'>" . T($text) . "</a>";
}

function drawtreeoptions($tree, $lvl=-1){
    $lvl++;
    foreach($tree as $leaf => $subleafs) {
        $_leaf = substr($leaf, 1);
        $name =  str_repeat('—', $lvl) . $_leaf;
        echo "<option value='$_leaf'>$name</option>";
        if(is_array($subleafs)) {
            drawtreeoptions($subleafs, $lvl);
        }
    }
}

function drawdirs($dir = '') {
    $tree = fm()->dloop($dir);
    drawtreeoptions($tree);
}


function first() {
    global $_PATH;
    return (bool)(count($_PATH) < 2);
}


function tabwrap($data, $id = 'tab-1') { return $data;
  return '  <v-card flat class="page p1">
				<v-card-text>' .$data . '</v-card-text>
			  </v-card>
			</v-tab-item>';
}
