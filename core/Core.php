<?php
session_start();

include (APP_PATH . '/config/config.php');
error_reporting(E_ALL ^ E_WARNING);

$db = mysql_connect($db_host, $db_user, $db_password) or die("Could not connect to database. Please check the config file at: <pre>".APP_PATH."/config/config.php</pre>");
if (!$db) {
	die("no db");
}
if (!mysql_select_db($db_table, $db)) {
	die("No database table <strong>$db_table</strong> was found. Please check the config file at: <pre>".APP_PATH."/config/config.php</pre>"); 
}
if (!get_magic_quotes_gpc()) {
  $_GET = array_map('mysql_real_escape_string', $_GET); 
  $_POST = array_map('mysql_real_escape_string', $_POST); 
  $_COOKIE = array_map('mysql_real_escape_string', $_COOKIE);
} else {  
   $_GET = array_map('stripslashes', $_GET); 
   $_POST = array_map('stripslashes', $_POST); 
   $_COOKIE = array_map('stripslashes', $_COOKIE);
   $_GET = array_map('mysql_real_escape_string', $_GET); 
   $_POST = array_map('mysql_real_escape_string', $_POST); 
   $_COOKIE = array_map('mysql_real_escape_string', $_COOKIE);
}

error_reporting(E_ALL);

include (BASE_PATH . 'core/Object.php');
include (BASE_PATH . 'core/Controller.php');
include (BASE_PATH . 'core/Model.php');
include (BASE_PATH . 'core/helpers/Helper.php');
include (BASE_PATH . 'core/helpers/HTMLHelper.php');
include (BASE_PATH . 'core/Router.php');

$requestURI = explode('/', $_SERVER['REQUEST_URI']);

$control = 'pages';
$method = 'index';
$params = array();

foreach ($requestURI as $key => $URI) {
	if ($key == 1 && $URI != "") {
		$control = $URI;
	}
	if ($key == 2 && $URI != "") {
		$method = $URI;
	}
	if ($key >= 3 && $URI != "") {
		$params[] = $URI;
	}
}

Router::dispatch($control, $method, $params);