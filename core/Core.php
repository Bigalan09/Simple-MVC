<?php
session_start();

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