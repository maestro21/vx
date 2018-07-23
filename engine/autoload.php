<?php
session_name("engine");
session_start();

define('BASE_PATH', dirname(dirname(__FILE__)) . '/');



/* Installation check */
$installed = 3;
if(!file_exists(BASE_PATH .'settings.php')) {
    if(DEV) {
        $index = true;
        $installed = 1;
        require_once(BASE_PATH . 'install.php');
    } else {
        die();
    }
}
if($installed == 1) die();

/* Example of settings
    $settings = [
      'folder_path' => 'misc/v7',
      'db_host' => 'localhost',
      'db_name' => 'root',
      'db_pass' => '',
      'db_db' => 'maestro',
      'db_type' => 'mysql',
      'mysql_dump_path' => 'C:/xampp/mysql/bin/mysqldump.exe',
      'mysql_path' => 'C:/xampp/mysql/bin/mysql.exe',
    ];
*/
require_once(BASE_PATH .'settings.php');


/**
 * Constant defininition
 */
define('ADM_PASS', '566e9199ea6408a99fd1c7333047d8a3');


/** database settings **/
define('HOST_SERVER',$settings['db_host']);
define('HOST_NAME',$settings['db_name']);
define('HOST_PASS',$settings['db_pass']);
define('HOST_DB',$settings['db_db']);
define('DB_TYPE',$settings['db_type']);
define('HOST_DRIVER_NODB', DB_TYPE . ':host=' . HOST_SERVER);
define('HOST_DRIVER',DB_TYPE . ':dbname=' . HOST_DB . ';host=' . HOST_SERVER);

/* database dump */
define('MYSQL_BIN_PATH', 'C:/xampp/mysql/bin/');
define('MYSQLDUMP_PATH', $settings['mysql_dump_path']);//MYSQL_BIN_PATH . 'mysqldump.exe'); //mysqldump if it is already defined
define('MYSQL_PATH',  $settings['mysql_path']);//MYSQL_BIN_PATH . 'mysql.exe'); //mysql if it is already defined
define('DUMP_ONE_FILE', TRUE); // defines if we want to dump only in one filename or want to dump each time in new file
define('DUMP_DIR', 'data/db/dump/');
define('DUMP_FILE', DUMP_DIR . 'last.sql');

/* path settings */
define('SITE_PATH', BASE_PATH . $settings['folder_path'] .'/');
define('BASE_FOLDER', '');
define('PUB_FOLDER', BASE_FOLDER . 'front/');
define('TPL_FOLDER',  BASE_FOLDER . 'tpl/');
define('CLASS_FOLDER',  BASE_FOLDER . 'modules/');
define('DATA_FOLDER',  BASE_FOLDER . 'data/');
define('EXT_FOLDER',  BASE_FOLDER . 'external/');
define('THEME_FOLDER',  BASE_FOLDER . 'themes/');
define('UPLOADS_FOLDER', DATA_FOLDER . 'uploads/');


/* URL settings */
define('HOST', 'http://' . $_SERVER['SERVER_NAME']);
define('HOST_FOLDER', '/' . $settings['folder_path']);
define('BASE_URL', HOST . HOST_FOLDER . '/');
define('PUB_URL', BASE_URL . PUB_FOLDER);
define('IMG_URL', PUB_URL . 'img/');
define('UPLOADS_URL', BASE_URL . UPLOADS_FOLDER);

/* default settings */
define('DEFMODULE', 'modules');
define('DEFTHEME', 'greenlite');


/** misc stettings **/
define('EXT_TPL', '.tpl.php');


/**
 * Include files
 */
function dir_list($directory) {
    $files = array_diff(scandir($directory), array('..', '.'));
    return $files;
}

$dirs = ['classes' , 'db', 'functions'];
foreach($dirs as $dir) {
    $files = dir_list(BASE_PATH . 'engine/' . $dir);
    foreach($files as $file) {
        $path  = BASE_PATH . 'engine/' . $dir . '/' . $file;
        if(file_exists($path)) require_once($path);
    }
}


/**
 * Install if installation form submitted
 */
if($installed == 2) {
    dbinstall();
}


globals();


//DBbackup();

session('headerlinks',[]);
