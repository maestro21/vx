<?php
/**
 * Created by PhpStorm.
 * User: Sergei Popov
 * Date: 12/7/2017
 * Time: 5:48 PM
 * Miscelaneous functions
 */


function doLogin(){
    $sql = "SELECT * from users where login='".getPost('login')."' AND pass=md5('".getPost('pass')."')";
    if (DBnumrows($sql)>0){
        $user = DBrow($sql);
        $user['logged'] = 1;
        setVar('admin',$user);
    }
    goBack();
}

function doLogout(){
    unsetVar('admin');
    unsetVar('logged');
    //print_r($_SESSION);
    die();
}



/** CSS\JS GENERATOR **/
// headers;



function Hset($hname) {
    global $_SESSION;
    $_H = $_SESSION['headerlinks'];
    if(!in_array($hname, $_H)) 	$_SESSION['headerlinks'][] = $hname;
}

function Hget($hname) {
    global $_SESSION;
    $_H = @$_SESSION['headerlinks'];
    if(isset($_H[$hname])) return $_H[$hname];
    return FALSE;
}