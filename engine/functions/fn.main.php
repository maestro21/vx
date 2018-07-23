<?php
/**
 * Created by PhpStorm.
 * User: adiacom NUC1
 * Date: 12/7/2017
 * Time: 5:49 PM
 * Core functions
 */



/** DEBUG FUNCTIONS **/
function debug($text=''){
    $info = debug_backtrace();
    $info = $info[0];
    $text = "File ".$info['file'] . "->class ".$info['type']."->function ".$info['function']."->line ".$info['line']."->data => (\n "
        . print_r($text,1);
    if(file_exists(LOGFILE)){
        $f = fopen(LOGFILE,"a+");
        fwrite($f,$text . "\n)\n\r");
        fclose($f);
    }
}

function inspect($data){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}


/** TEMPLATE FUNCTION **/

/**
Main function to display template;
Can be called -anywhere- in code.

@$_TPL string - path to template;
@$vars array - template variables;
@return string - parsed tempate html;
 **/
function tpl($_TPL, $vars=array()){
    /**
    Defining template to choose;
    If it has format class/method then class/tpl.method.php would be included;
    If it is basic template (i.e. `index`) then just tpl.method.php would be included;
     **/
    @list($class, $method) = explode('/',$_TPL);
    if($method == '') {
        $method = $class;
        $class = '';
    }
    /**
    Priority of template:
    1. Theme class method tpl
    2. Theme class default view tpl
    3. Default class method tpl
    4. Default class default view tpl
    5. Otherwise return 404 not found.
     **/
    $theme = (G('theme') != '' ? G('theme') : DEFTHEME);
    if(file_exists(THEME_FOLDER . "{$theme}/tpl/{$class}/tpl.{$method}.php")) {
        $_url = THEME_FOLDER . "{$theme}/tpl/{$class}/tpl.{$method}.php";
    } elseif(file_exists(THEME_FOLDER . "{$theme}/tpl/default/tpl.{$method}.php")) {
        $_url = THEME_FOLDER . "{$theme}/tpl/default/tpl.{$method}.php";
    } elseif(file_exists(TPL_FOLDER . "{$class}/tpl.{$method}.php")) {
        $_url = TPL_FOLDER . "{$class}/tpl.{$method}.php";
    } elseif(file_exists(TPL_FOLDER . "default/tpl.{$method}.php")) {
        $_url = TPL_FOLDER . "default/tpl.{$method}.php";
    } else {
        return '<h3>' . T('404 not found') . '</h3>';
    }
    /**
     * Parsing template variables and returning parsed template
     */
    if($_url){
        foreach ($vars as $k =>$v){
            if(!is_array($v) && !is_object($v))
                $$k=html_entity_decode(stripslashes($v));
            else
                $$k=$v;
        }

        ob_start();
        include($_url);
        $tpl = ob_get_contents();
        ob_end_clean();
    }

    return $tpl;
}

/**
 * Checks if variable or array element exists
 * If not - returns default value
 * @param string|array - variable or variable name
 */
function v($val = null, $defvalue = null) {
    if(is_array($val)) {
        $k = $val[1];
        $arr = $val[0];
        return (isset($arr[$k]) ? $arr[$k] : $defvalue);
    }
    if($val == null) return $defvalue;
    return $val;
}

/** $_GLOBALS[$name] getters\setter **/
function G($name, $value = NULL) {
    global $_GLOBALS; 
    //if($name == 'db_lastbackup') { var_dump($_GLOBALS); foreach ($_GLOBALS as $k => $v) echo $k . ' ' . $v . "<BR>";}
    if($value != NULL) {
        $_GLOBALS[$name] = $value;
        //M('system')->set($name, $value);
    }
    return (isset($_GLOBALS[$name]) ? $_GLOBALS[$name] : NULL);
}

function glist($name) {
    $data = explode(',', G($name));
    $data = array_combine($data, $data);
    return $data;
}

function delG($name) {
    M('system')->delByName($name);
}



function M($module) {
    global $masterdb;
    if(is_object($module)) return $module;
    $filename = BASE_PATH . CLASS_FOLDER . 'module.' . $module . '.php'; 
    if(file_exists($filename)) {
        require_once($filename);
        //require_once('engine/class.masterdb.php');
        return new $module();
    }
    return FALSE;
}

function loadClass($cl,$clname=''){
    if(file_exists("classes/$cl.php")){
        require_once("classes/$cl.php");
        $class = new $cl($clname); //echo $cl;
    } else{
        return FALSE;
    }
    return $class;
}


function sendMail($data){
    /*$headers =
"MIME-Version: 1.0 \r\n
Content-type: text/html; charset=utf-8\r\n
From: ".G('mailFrom')."\r\n"; */
    $headers  = "Content-type: text/html; charset=utf-8 \r\n";
    if(isset($data['from'])) {
        $from = $data['from'];
        $headers .= "From:  $from\r\n";
        $headers .= "Reply-To:  $from\r\n";
    }
    mail($data['to'],$data['title'],$data['subject'],$headers);
}

function modules(){
    return M('modules')->cache();
}



/** User login functions */

function logged(){
    global $_SESSION,$_POST,$_COOKIE;//s inspect($_SESSION);

    if(isset($_SESSION['user'])) return true;

    if(isset($_COOKIE['mail'])){
        $sql ="SELECT * FROM users where email='{$_COOKIE['mail']}'"; //echo $sql;
        $res = DBrow($sql); //inspect($res);
        if($res !='') $_SESSION['user'] = $res;
    }

    return isset($_SESSION['user']);
}

function user(){
    // todo replace with sql;
    return 1;
}



function superAdmin(){
    return (bool)(user() == 1);
}


function rights(){
    global $_SESSION, $_RIGHTS;
    $_RIGHTS['admin'] = TRUE;
}

function right($rightname) {
    global $_RIGHTS;
    return true; //(isset($_RIGHTS[$rightname]));
}





function R($rights) {
    return true;
}


