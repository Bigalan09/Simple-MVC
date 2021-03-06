<?php
session_start ();

include (APP_PATH . '/config/config.php');

if ($debug_enable) {
	error_reporting ( E_ALL ^ E_NOTICE );
} else {
	error_reporting ( 0 );
	ini_set ( 'error_reporting', 0 );
}

if (isset ( $default_time_zone )) {
	date_default_timezone_set ( $default_time_zone );
}

if ($ad_enable) {
	$ad = ldap_connect ( $ad_host ) or die ( "Could not connect to the Active Directory Server at <strong>$ad_table</strong>. Please check the config file at: <pre>" . APP_PATH . "/config/config.php</pre>" );
	
	if ($ad_options_enable) {
		if (is_array ( $ad_options )) {
			foreach ( $ad_options as $key => &$val ) {
				
				ldap_set_option ( $ad, $key, $val ) or die ( "There Is a Problem with Option <strong>$key</strong> with value <strong>$val</strong>. Please check the config file at: <pre>" . APP_PATH . "/config/config.php</pre>" );
			}
		} else {
			die ( "ad_options requires an array, For example <strong> ad_options = array(option name => value,option name 2 => value)</strong>  Please check the config file at: <pre>" . APP_PATH . "/config/config.php</pre>" );
		}
	}
}

include (BASE_PATH . 'core/Object.php');
include (BASE_PATH . 'core/Controller.php');
include (BASE_PATH . 'core/Model.php');
include (BASE_PATH . 'core/helpers/Helper.php');
include (BASE_PATH . 'core/helpers/HTMLHelper.php');
include (BASE_PATH . 'core/Router.php');

if ($db_enable) {
	
	$dns = $db_engine . ':dbname=' . $db_table . ";host=" . $db_host;
	try {
		$db = new PDO ( $dns, $db_user, $db_password );
		Model::init ( $db );
	} catch ( Exception $e ) {
		die ( "Could not connect to database. Please check the config file at: <pre>" . APP_PATH . "/config/config.php</pre>" );
	}
}

$dir = APP_PATH . '/DTO';
if (is_dir ( $dir )) {
	if ($dh = opendir ( $dir )) {
		while ( ($file = readdir ( $dh )) !== false ) {
			if ($file != ".." && $file != ".") {
				$dto = substr ( $file, - 7, 3 );
				if ($dto == "DTO") {
					include ( $dir . '/' . $file );
				}
			}
		}
		closedir ( $dh );
	}
}

$requestURI = explode ( '/', key ( $_GET ) );

$control = 'pages';
$method = 'index';
$params = array ();

foreach ( $requestURI as $key => $URI ) {
	if ($key == 1 && $URI != "") {
		$control = $URI;
	}
	if ($key == 2 && $URI != "") {
		$method = $URI;
	}
	if ($key >= 3 && $URI != "") {
		$params [] = $URI;
	}
}
function env($key) {
	if ($key === 'HTTPS') {
		if (isset ( $_SERVER ['HTTPS'] )) {
			return (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off');
		}
		return (strpos ( env ( 'SCRIPT_URI' ), 'https://' ) === 0);
	}
	
	if ($key === 'SCRIPT_NAME') {
		if (env ( 'CGI_MODE' ) && isset ( $_ENV ['SCRIPT_URL'] )) {
			$key = 'SCRIPT_URL';
		}
	}
	
	$val = null;
	if (isset ( $_SERVER [$key] )) {
		$val = $_SERVER [$key];
	} elseif (isset ( $_ENV [$key] )) {
		$val = $_ENV [$key];
	} elseif (getenv ( $key ) !== false) {
		$val = getenv ( $key );
	}
	
	if ($key === 'REMOTE_ADDR' && $val === env ( 'SERVER_ADDR' )) {
		$addr = env ( 'HTTP_PC_REMOTE_ADDR' );
		if ($addr !== null) {
			$val = $addr;
		}
	}
	
	if ($val !== null) {
		return $val;
	}
	
	switch ($key) {
		case 'SCRIPT_FILENAME' :
			if (defined ( 'SERVER_IIS' ) && SERVER_IIS === true) {
				return str_replace ( '\\\\', '\\', env ( 'PATH_TRANSLATED' ) );
			}
			break;
		case 'DOCUMENT_ROOT' :
			$name = env ( 'SCRIPT_NAME' );
			$filename = env ( 'SCRIPT_FILENAME' );
			$offset = 0;
			if (! strpos ( $name, '.php' )) {
				$offset = 4;
			}
			return substr ( $filename, 0, strlen ( $filename ) - (strlen ( $name ) + $offset) );
			break;
		case 'PHP_SELF' :
			return str_replace ( env ( 'DOCUMENT_ROOT' ), '', env ( 'SCRIPT_FILENAME' ) );
			break;
		case 'CGI_MODE' :
			return (PHP_SAPI === 'cgi');
			break;
		case 'HTTP_BASE' :
			$host = env ( 'HTTP_HOST' );
			$parts = explode ( '.', $host );
			$count = count ( $parts );
			
			if ($count === 1) {
				return '.' . $host;
			} elseif ($count === 2) {
				return '.' . $host;
			} elseif ($count === 3) {
				$gTLD = array (
						'aero',
						'asia',
						'biz',
						'cat',
						'com',
						'coop',
						'edu',
						'gov',
						'info',
						'int',
						'jobs',
						'mil',
						'mobi',
						'museum',
						'name',
						'net',
						'org',
						'pro',
						'tel',
						'travel',
						'xxx' 
				);
				if (in_array ( $parts [1], $gTLD )) {
					return '.' . $host;
				}
			}
			array_shift ( $parts );
			return '.' . implode ( '.', $parts );
			break;
	}
	return null;
}

if (! defined ( 'ENCRYPT_KEY' )) {
	define ( 'ENCRYPT_KEY', $encrypt_key );
}

if (! defined ( 'FULL_BASE_URL' )) {
	$s = null;
	if (env ( 'HTTPS' )) {
		$s = 's';
	}
	
	$httpHost = env ( 'HTTP_HOST' );
	$base = basename ( dirname ( dirname ( __FILE__ ) ) );
	
	if ($httpHost == $base || $base == "htdocs") {
		$base = "";
	} else {
		$base = '/' . $base;
	}
	
	if (isset ( $httpHost )) {
		if (strpos ( $httpHost, 'www' ) !== false) {
			define ( 'FULL_BASE_URL', 'http' . $s . ':/' . $base );
		} else {
			define ( 'FULL_BASE_URL', 'http' . $s . '://' . $httpHost . $base );
		}
	}
	unset ( $httpHost, $s );
}

Router::dispatch ( $control, $method, $params );