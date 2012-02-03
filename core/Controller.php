<?php

// contain's methods for all the user defined controllers
class Controller extends Object {
	
	public $autoRender = true;
	public $name = null;
	
	public function __construct() {
		if ($this->name === null) {
			$this->name = get_class($this);
		}
	}
	
	public function load_model($name) {
		$model_path = APP_PATH . '/models/' . $name . 'Model.php';
		if ( file_exists( $model_path ) ) {
			include_once $model_path;
			
			if ( class_exists( $name ) ) {
				$tmp_class = new $name();
				self::get_user_vars($tmp_class);
			} else
				die('The class <strong>' . $name . '</strong> could not be found in <pre>' . APP_PATH . '/models/' . $name . 'Model.php</pre>');
		} else 
			die('The file <strong>' . $name . 'Model.php</strong> could not be found at <pre>' . APP_PATH . '/models/' . $name . 'Model.php</pre>');
		
		return $tmp_class;
	}
	
	public function render($view = null, $layout = null){
		$this->beforeRender();
		$this->autoRender = false;
		
		$this->name = get_class($this);
		
		if ($view === null) {				
			$view = Router::view_path($this->name);
		} else {
			if (strpos($view, '/')) {
				$r = explode('/', $view);
				$view = Router::view_path($r[0], $r[1]);
			} else {
				$view = Router::view_path($this->name, $view);
			}
		}
		
		if ($layout === null) {
			$layout = Router::get_layout($this->name);
		} else {
			$layout = Router::get_layout($layout);
		}
		
		Router::render($view, $layout);
		$this->afterRender();
		
		return true;
	}
	
	public function beforeFilter() {
	}
	
	public function beforeRender() {
	}
	
	public function afterRender() {
	}
	
}