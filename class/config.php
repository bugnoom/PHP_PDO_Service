<?php
header('Access-Control-Allow-Origin: *');

define('_CFG_driver',"mysql");
define('_CFG_host',"localhost");
define('_CFG_user',"root");
define('_CFG_pass', "abcd!@#zxc");
define('_CFG_db_name', "fixxhome"); 

define('_CFG_connectInfo',"mysql:host="._CFG_host.";dbname="._CFG_db_name.";charset=utf8"); 

include_once (dirname(__FILE__)."/class_service.php");

	
?>
