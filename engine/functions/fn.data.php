<?php



/** DATA fuctions **/

$_WIDGETS = [
    'TEXT',
    'TEXTAREA',
    'HTML',
    'BBCODE',
    'PASS',
    'HIDDEN',
    'CHECKBOX',
    'RADIO',
    'SELECT',
    'MULTSELECT',
    'DATE',
    'TIME',
    'DATETIME',
    'CHECKBOXES',
    'INFO',
    'KEYVALUES',
    'LIST',
    'EMAIL',
    'PHONE',
    'NUMBER',
    'URL',
    'SLUG',
    'TABLE',
    'ARRAY',
    'FILE',
    'SELECTFILE',
    'SIZE',
    'COORDS',
    'COLOR',
    'CHAR',
    'TABS',
    'BTN',
    'PLAINCODE',
];
$_WIDGETS = array_combine($_WIDGETS,$_WIDGETS);

foreach($_WIDGETS as $widget) {
      define('WIDGET_'.$widget,$widget);
}

function widgets() {
    global $_WIDGETS;
    return $_WIDGETS;
}

$_DBTYPES = [
    'VOID',
    'TEXT',
    'BLOB',
    'STRING',
    'BOOL',
    'INT',
    'DATE',
    'FLOAT',
    'CHAR',
];
$_DBTYPES = array_combine($_DBTYPES,$_DBTYPES);
function dbtypes() {
    global $_DBTYPES;
    return $_DBTYPES;
}


foreach($_DBTYPES as $dbtype) {
    define('DB_'.$dbtype,$dbtype);
}


function getWidgetDefaultDBType($widget)
{
    $ret = DB_STRING;
    switch($widget) {

        case WIDGET_TEXTAREA:
        case WIDGET_BBCODE:
        case WIDGET_MULTSELECT:
        case WIDGET_KEYVALUES:
        case WIDGET_LIST:
            $ret = DB_TEXT;
            break;

        case WIDGET_CHAR:
            $ret = DB_CHAR;
            break;

        case WIDGET_CHECKBOX:
            $ret = DB_BOOL;
            break;

        case WIDGET_DATE:
        case WIDGET_TIME:
        case WIDGET_DATETIME:
            $ret = DB_DATE;
            break;

        case WIDGET_SELECT:
        case WIDGET_NUMBER:
        case WIDGET_SIZE:
            $ret = DB_INT;
            break;

        case WIDGET_HTML:
        case WIDGET_ARRAY:
        case WIDGET_PLAINCODE:
        case WIDGET_TABLE:
            $ret = DB_BLOB;
            break;

        case WIDGET_PLAINCODE:
        case WIDGET_BTN:
            $ret = DB_VOID;
            break;

        default:
            $ret = DB_STRING;
            break;
    }
    return $ret;
}


function fType($data, $value = NULL) {
    $type       = v(@$data['type'], WIDGET_TEXT);
    $fieldname  = v(@$data['fieldname']);
    $options =  v(@$data['options']);

    switch($type) {
        case WIDGET_COLOR:
            return "<div class='colorbox' style='background-color:" . $value."'></div>";
            break;
        case WIDGET_SELECTFILE:
            return "<img src='" . getThumbById($value) . "'>";
            break;

        case WIDGET_CHAR:
            return stripslashes($value);
            break;
        case WIDGET_TABLE:
        case WIDGET_ARRAY:
            return count(unserialize($value)) - 1;
            break;

        case WIDGET_NUMBER:
            return (int)$value;
            break;


        case WIDGET_HTML:
            Hset('editor');

        case WIDGET_SLUG:
        case WIDGET_INFO:
        case WIDGET_TEXT:
        case WIDGET_TEXTAREA:
        case WIDGET_BBCODE:
        case WIDGET_EMAIL:
        case WIDGET_PHONE:
        case WIDGET_URL:
            return nl2br($value);
            break;

        case WIDGET_ARRAY: return count($value);
            break;

        case WIDGET_LIST :
            $values = array();
            foreach($value as $k => $v) {
                $values[] = $k . '=' . $v;
            }
            $result = implode('<br>', $values);
            return $result;
            break;

        case WIDGET_LIST:
        case WIDGET_KEYVALUES:
            $values = array();
            if($type == WIDGET_KEYVALUES) {
                $v = $k . '=' . $v;
            }
            $values[] = $v;
            $result = implode('<br>', $values);
            return $result;
            break;


        case WIDGET_PASS:
            if(!$fieldname) return '*****';
            break;

        case WIDGET_HIDDEN:
            return;
            break;


        case WIDGET_CHECKBOX:
            if($fieldname)
                return (!(bool)$value ?  T('not') : '') . ' ' . T($fieldname);
            else
                return ((bool)$value ? T('yes') : T('no'));
            break;

        case WIDGET_RADIO:
        case WIDGET_SELECT:
            return (isset($options[$value])? $options[$value] : $value);
            break;

        case WIDGET_DATE:
            return fDateTime($value);
            break;

        case WIDGET_CHECKBOXES:
        case WIDGET_MULTSELECT:
            $values = explode(',',$value);
            foreach($values as $k =>  $val) {
                if(isset($options[$val])) {
                    $values[$k] = $options[$val];
                }
            }
            $result = implode(',', $values);
            if($fieldname) $result = T($fieldname . 's') . ': ' . $result;
            return $result;
            break;

        default:
            return '';
            break;
    }
    return $value;
}

function sqlFormat($data) { //$type, $value = '', $quote = false){ //echo $type;
    $widget = v([$data,'widget'], WIDGET_TEXT);
    $dbtype = v([$data,'dbtype'], getWidgetDefaultDBType($widget));
    $value  = v([$data,'value']);
    $quote  = v([$data,'quote'], FALSE);

    if($dbtype == DB_VOID) return;

    switch($widget) {

        case WIDGET_LIST:
        case WIDGET_KEYVALUES:
            $values = [];
            $value = explode(PHP_EOL, $value);
            foreach($value as $k => $v) {
                if($dbtype == WIDGET_KEYVALUES) {
                    $v = $k . '=' . $v;
                }
                $values[] = $v;
            }
            $value = $values;
        case WIDGET_DATE:
        case WIDGET_TIME:
        case WIDGET_DATETIME:
            if($value=='') $value = date("Y-m-d H:i:s"); else{
                $value = date("Y-m-d H:i:s",mktime(
                    intval(@$value['h']),
                    intval(@$value['mi']),
                    intval(@$value['s']),
                    intval(@$value['m']),
                    intval(@$value['d']),
                    intval(@$value['y'])
                ));
            }
            break;

        case WIDGET_PASS:
            $value = md5($value);
            break;
    }


    switch($dbtype) {
        case DB_BOOL:
        case DB_INT:
            $value = intval($value);
            break;
        /*
        case DB_CHAR:
        case DB_TEXT:
        case DB_BLOB:
            $value = parseString($value);
            break; */

        case DB_FLOAT:
            $value = floatval($value);
            break;
    }

    if($quote) $value = dbquote($value);
    return $value;
}

function now() {
    return sqlFormat(['widget' => WIDGET_DATETIME]);
}



function fDate($date) {
    $date = explode("-", $date);
    return (int)$date[2] . " " . T('mon_'.(int)$date[1]) . " " .$date[0];
}

function fTime($time) {
    $time = explode(":", $time);
    return (int)$time[0] . ":" . $time[1];
}

function fDateTime($datetime){
    $datetime = explode(" ", $datetime);
    return fDate($datetime[0]) . ", " . fTime($datetime[1]);
}

function fdateunix($unixtime) {
    $mysqltime  = date("Y-m-d H:i:s", $unixtime);
    return fDateTime($mysqltime);
}


/**
 * Created by PhpStorm.
 * User: Sergei Popov
 * Date: 12/7/2017
 * Time: 5:47 PM
 *
 * Data and database functions
 */

function keyvalues($data) {
    $ret = [];
    $data = explode(PHP_EOL, $data);
    foreach($data as $k=>$v){
        if(empty($v)) continue;
        $vv = explode('=', $v);
        if(isset($vv[1])) {
            $ret[$vv[0]] = $vv[1];
        } else {
            $ret[] = $v;
        }
    }
    return $ret;
}


function var2string($val) {
    if(is_array($val)) $val = implode(',', $val);
    return $val;
}

function striprow($arr = array()){
    if(!empty($arr))
        foreach ($arr as $k=>$v){
            $arr[$k] = stripslashes($v);
        }

    return $arr;
}


function strToKeyValues($data) {
    $return = array();
    $data = explode(PHP_EOL,$data);
    foreach($data as $row) {
        $_data = explode('=', $row);
        $key = trim($_data[0]);
        $value = trim($_data[1]);
        $return[$key] = $value;
    }
    return $return;
}


/**
 *  Saves array data by key
 *  Input array [ 0 => [ 'key' => 'test', 'value1'=> 'testvalue']]
 *  Output: [ 'test' =>  [ 'key' => 'test', 'value1'=> 'testvalue']]
 */
function saveByKey($arr, $key) {
    if(empty($arr)) return false;
    if(empty($key)) return false;
    if(!is_array($arr)) return false;

    $data = array();
    $langs = getLangs();
    foreach($arr as $row) {
        if(!isset($row[$key])) return $data;
        $data[$row[$key]] = $row;
    }
    ksort($data, SORT_FLAG_CASE);
    return $data;
}

function strToList($data, $divider = PHP_EOL) {
    $ret = explode($divider, $data);
    return $ret;
}


function chkz($int){
    if($int < 10) return '0'.$int;
}

function sqlPrepare($value) {
    $value = "'" . mysql_real_escape_string(stripslashes($value)) . "'";
    return $value;
}

/** FORMAT FUNCTIONS **/
function parseString($string = '') {
    return addslashes(htmlspecialchars(@trim($string)));
}

function string_decode($string) {
    return html_entity_decode(stripslashes($string));
}

function setArrayValue(&$data, $path, $value) {
    $temp = &$data;
    foreach ( $path as $key ) {
        $temp = &$temp[$key];
    }
    $temp = $value;
    return $value ;
}






function processFileType($mimeType) {
    $mimeType = explode('/',$mimeType);
    $type1 = $mimeType[0];
    $type2 = $mimeType[1];

    switch($type1) {
        case 'text' :
            if($type2 == 'x-php') return 'code';
            return 'text';
            break;

        case 'image':
            return 'image';
            break;

        case 'application':
            if($type2 == 'zip') return 'archive';
            break;
    }

    return 'default';
}


function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('bytes', 'KB', 'MB', 'GB', 'TB');

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}


function tslash($path){
    $path = rtrim($path, '/') . '/';
    return $path;
}

function trimslashes($path) {
    $path = trim($path, '/');
    $path = str_replace('//','/', $path);
    return $path;
}

/** DB  **/


function dbempty() {
    dbquery("DROP DATABASE IF EXISTS " . HOST_DB);
    dbquery("CREATE DATABASE " . HOST_DB);
}


function dbbackup() {
	if((int)G('db_backup_frequency') == 0) return;

    $now = strtotime("now");
    if(empty(G('db_lastbackup'))) {
        $updateTime = 0;
    } else {
        $updateTime = strtotime(G('db_lastbackup')) +  strtotime(G('db_backup_frequency'));
    }
    //echo $now . ' ' . $updateTime;
    if($now > $updateTime){
        dbdump();
        G('db_lastbackup', $now);
    }
}


function dbdump() {
    $host = HOST_SERVER;
    $user = HOST_NAME;
    $pass = (empty(HOST_PASS) ? '' : '--password=' . HOST_PASS) ;
    $db   = HOST_DB;
    $fn = DUMP_FILE;
    $ddir = DUMP_DIR;
    if (!file_exists(DUMP_DIR)) {
        mkdir(DUMP_DIR, 0777, true);
    }
    $mysqldumppath = MYSQLDUMP_PATH;
    $exec = "$mysqldumppath --user=$user $pass --host=$host $db > $fn";/// echo $exec;
    exec($exec,$output);
    if(!DUMP_ONE_FILE) {
        $exec = "$mysqldumppath --user=$user $pass --host=$host $db > $ddir/$now.sql";
        exec($exec,$output);
    }
}

function dbrestore() {
    $mysql = MYSQL_PATH;
    $user = HOST_NAME;
    $pass = (empty(HOST_PASS) ? '' : '-p' . HOST_PASS) ;
    $db   = HOST_DB;
    $fn = DUMP_FILE;
    if(file_exists($fn)) {
        $exec = "$mysql -u $user $pass $db < $fn"; //echo $exec;
        exec($exec,$output);
        return TRUE;
    }
    return FALSE;
}


function moveToBottom($arr, $key) {
    if(!isset($arr[$key])) return $arr;
    $v = $arr[$key];
    unset($arr[$key]);
    $arr[$key] = $v;
    return $arr;
}



$_DB;
function DB() {
    global $_DB;
    if(!$_DB) $_DB = new Medoo($params);
    return $_DB;
}




$_DATA = [];
function data($key = null, $value = null) {
    global $_DATA;
    if(!empty($key)) {
        if(!empty($value)) {
            $_DATA[$key] = $value;
        }
        return $_DATA[$key] ?? null;
    }
    return $_DATA;
}


/**
 * Cache getter\setter
 */
function cache($name, $data = null){
  $src = CACHE_PATH . $name . '.php');

  if($data) {
    $data = '<?php ' . $$name . ' = [];');
    fm()->fsave($src, $data);
  }

  if(file_exists($src)) {
    include($src);
    return $$name;
  }
  return false;
}
