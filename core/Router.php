<?php

class Router extends Object {
	public static $path = null;
	protected static $_action = null;
	
	public static function dispatch($control, $action = '', $params = null) {
		$class_name = $control;
		if (strtolower($class_name) == "api") $class_name = strtolower($class_name);
		
		if (empty($action)) {
			$action = "index";
		}
		self::$_action = $action;
		
		self::load_controller('app');
		$app_class = new AppController();
		self::get_user_vars($app_class);
		
		self::load_controller($class_name);
		
		if (class_exists($class_name)) {
			$tmp_class = new $class_name();
			$tmp_class->method = $action;
			$tmp_class->beforeFilter();
			//if ($tmp_class->autoRender) {
				if (is_callable(array($tmp_class, $action))) {
					$tmp_class->$action($params);
				} else if (is_callable(array($tmp_class, '_' . $action))) {
						$action = '_' . $action;
						if (is_callable(array($tmp_class, 'isAuthorised'))) {
							if ($tmp_class->isAuthorised()) {
								$tmp_class->$action($params);
								$action = substr($action, 1, strlen($action));
							} else {
								die('The action <strong>' . substr($action, 1, strlen($action)) . '</strong> has not been authorised from the controller <strong>' . $class_name . '</strong>.');
							}
						} else {
							die('The action <strong>isAuthorised()</strong> needs to be implemented in the controller <strong>' . $class_name . '</strong>.');
						}
				} else {		
					die('The action <strong>' . $action . '</strong> could not be called from the controller <strong>' . $class_name . '</strong>');
				}
			//}
		} else {
			die('The class <strong>' . $class_name . '</strong> could not be found in <pre>' . APP_PATH . '/controllers/' . $class_name . 'Controller.php</pre>');
		}
		
		if ($tmp_class->autoRender) {
			$tmp_class->beforeRender();
			self::get_user_vars($tmp_class);
			self::render(self::view_path($control, $action), self::get_layout($control, $action));
			$tmp_class->afterRender();
		}
		
	}
	
	public static function render($view = null, $layout = null) {
		if (!empty($layout)) {
			$layout = file_get_contents($layout);
			$view_path = $view;
			
			if (file_exists($view_path)) {
				$layout = str_replace('{{ CONTENT }}', file_get_contents($view_path), $layout);
			} else {
				$layout = str_replace('{{ CONTENT }}', '', $layout);
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
			die ('The file <strong>' . $view_path . '</strong> could not be found. (load_view)');
		}
	}
	
	public static function get_layout($controller, $action = null) {
		if ($action === null) {
			$action = self::$_action;
		}
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
	
	public static function view_path($controller, $action = null) {
		if ($action === null) {
			$action = self::$_action;
		}
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