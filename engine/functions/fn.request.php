<?php
/**
 * Created by PhpStorm.
 * User: Sergei Popov
 * Date: 12/7/2017
 * Time: 5:46 PM
 */


function get($key,$default = NULL) {
    global $_GET;
    if(isset($_GET[$key])) return $_GET[$key];
    return $default;
}

function post($key = null, $default = NULL) {
    global $_POST;
    if($key == null) return $_POST;
    if(isset($_POST[$key])) return $_POST[$key];
    return $default;
}
function request($key, $default = NULL) {
    global $_REQUEST;
    if(isset($_REQUEST[$key])) return $_REQUEST[$key];
    return $default;
}

function session($key = null, $value = null) {
    global $_SESSION;
    if(!$key) return $_SESSION;
    if($value) {
        $_SESSION[$key] = $value;
    }
    if(!isset($_SESSION[$key])) return null;
    return $_SESSION[$key];
}


/*** FILTERS **/

function getFilterState($class,$field){
    $f = explode("_",val('sort_'.$class));
    if($f[0] == $field){
        switch ($f[1]){
            case 'NONE': return 'ASC'; break;
            case 'ASC': return 'DESC'; break;
            case 'DESC': return 'NONE'; break;
        }
    }
    return 'ASC';
}

function filterImg($class,$field){
    $f = explode("_",val('sort_'.$class));
    if($f[0] == $field){
        switch ($f[1]){
            case 'ASC': echo "&uArr;"; break;
            case 'DESC': echo "&dArr;"; break;
        }
    }
}


function filter($name, $value = null) {
    if($value != null) {
        session($name, $value);
        die();
    }
    if(!session($name)) return FALSE;
    $val = explode("_", session($name));
    return [
        $val[0] => @$val[1],
    ];
}
/** eof filters **/



function globals(){
    global $_GLOBALS;
    $_GLOBALS = M('system')->globals();
}

/**
 * Variables is just list of variables that we just use during execution of current request;
 * Example: text labels, some temporary values like wrap, ajax, etc;
 * Unlike Session, we don't want to save them in Session;
 * Unline Globals, we don't want to save them in database\cache;
 */
$_VARIABLES = [];
function val($key, $value = null) {
    global $_VARIABLES;
    if($value) {
        $_VARIABLES[$key] = $value;
    }
    if(isset($_VARIABLES[$key])) return  $_VARIABLES[$key];
    return null;
}

function files($key = null) {
    global $_FILES;
    if(isset($_FILES[$key])) return $_FILES[$key];
    return null;
}

/**
 * Path functions
 * class/id/page/search
 */

function path($key = null) {
    global $_PATH;
    if($key !== null) {
        if (isset($_PATH[$key])) return $_PATH[$key];
        return NULL;
    }
    return $_PATH;
}


function lang() {
    return path(0);
}

function module() {
    return path(1);
}

function id() {
    return path(2);
}


$_PATH = [];
function route() {
    global $_SERVER, $_PATH;
    $vars = explode('?', $_SERVER['REQUEST_URI']);
    $path = trim(str_replace(HOST_FOLDER, '', $vars[0]), '/');
    $path = mapping($path);
    $_PATH = explode('/', $path);
}

function mapping($path) { return $path;
    include(BASE_FOLDER . 'mapping.php');
    foreach ($mapping as $k => $v){
        $path = preg_replace('/'.$k.'/',$v,$path);
    }
    return $path;
}

function S($name, $value = NULL) {
    global $_SESSION; //var_dump($_GLOBALS);
    //if($name == 'db_lastbackup') { var_dump($_GLOBALS); foreach ($_GLOBALS as $k => $v) echo $k . ' ' . $v . "<BR>";}
    if($value != NULL) {
        $_SESSION[$name] = $value;
    }
    return (isset($_SESSION[$name]) ? $_SESSION[$name] : NULL);
}

function processRequest() {
    route();
    if(module() == 'filter'){ filter(path(1), path(2)); }
    /* lang settings */
    G('langs', cache('langs'))
    loadTextLabels();
    $controller = module() . '/' . id();
    $controller = controller($controller));
    if($controller) {
      id(path(3));
    } else {
      $controller = controller(module());
    }
    if(!$controller) {
      redirect();
    }
    echo $controller->api();
}
