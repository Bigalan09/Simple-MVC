<?php

class Router extends Object {
	public static $path = null;
	
	public static function dispatch($control, $action = '', $params = null) {
		$class_name = $control;
		
		if (empty($action)) {
			$action = "index";
		}
		
		self::load_controller('app');
		$app_class = new AppController();
		self::get_user_vars($app_class);
		
		self::load_controller($class_name);
		
		if (class_exists($class_name)) {
			$tmp_class = new $class_name();
		
			if (is_callable(array($tmp_class, $action))) {
				$tmp_class->$action($params);
			} else {
				die('The action <strong>' . $action . '</strong> could not be called from the controller <strong>' . $class_name . '</strong>');
			}
		} else {
			die('The class <strong>' . $class_name . '</strong> could not be found in <pre>' . APP_PATH . '/controllers/' . $class_name . 'Controller.php</pre>');
		}
		
		self::get_user_vars($tmp_class);
		
		$layout_path = self::get_layout($control, $action);
		
		if (!empty($layout_path)) {
			$layout = file_get_contents($layout_path);
			$view_path = self::view_path($control, $action);
			
			if (file_exists($view_path)) {
				$layout = str_replace('{PAGE_CONTENT}', file_get_contents($view_path), $layout);
			} else {
				$layout = str_replace('{PAGE_CONTENT}', '', $layout);
			}
			$filename = BASE_PATH . 'tmp/' . time() . '.php';
			
			$file = fopen($filename, 'a');
			fwrite($file, $layout);
			fclose($file);
			
			self::load_layout($filename);
			
			unlink($filename);
		} else
			die ('Could not find a layout in <pre>' . APP_PATH . '/views/layouts</pre>');
	}
	
	public static function load_controller($name) {
		$controller_path = APP_PATH . '/controllers/' . $name . 'Controller.php';
		if( file_exists($controller_path) )
			include_once $controller_path;
		else
			die('The file <strong>' . $name . 'Controller.php</strong> could not be found at <pre>' . $controller_path . '</pre>');
	}
	
	public static function load_view($controller, $action) {
		$view_path = self::view_path($controller, $action);
		if( !empty($view_path) ) {
			unset($controller, $action);
			foreach( self::$__user_vars as $var => $value ) {
				$$var = $value;}
			include_once $view_path;
		} else {
			die ('The file <strong>' . $view_path . '</strong> could not be found.');
		}
	}
	
	public static function get_layout($controller, $action) {
		// controller-action.php
		$controller_action_path = APP_PATH . '/views/layouts/' . $controller . '-' . $action . '.php';
		// controller.php
		$controller_path = APP_PATH . '/views/layouts/' . $controller . '.php';
		// application.php
		$application_path = APP_PATH . '/views/layouts/application.php';
		
		$path_to_use = null;
		// find the path to use
		if( file_exists($controller_action_path) )
			$path_to_use = $controller_action_path;
		elseif( file_exists($controller_path) )
			$path_to_use = $controller_path;
		elseif( file_exists($application_path) )
			$path_to_use = $application_path;
			
		return $path_to_use;
	}
	
	public static function view_path($controller, $action) {
		$view_path = APP_PATH . '/views/' . $controller . '/' . $action . '.php';
		$path = null;
    
		if( file_exists($view_path) )
			$path = $view_path;
		else
			die ('The file <strong>' . $view_path . '</strong> could not be found.');
			
		return $path;
	}
  
	public static function load_layout($filename) {
		foreach( self::$__user_vars as $var => $value ) {
			$$var = $value;
		}

		include $filename;
	}
  
}