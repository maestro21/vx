<?php
define('DEV', TRUE);
include('engine/autoload.php');

/** uncomment if you want to make your website completely private **/
/** if(!logged()) redirect(BASE_URL . 'login.php'); /**/

route();

render();
