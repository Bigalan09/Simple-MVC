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
	
	public function redirect($url, $autoRender = false){
		$this->autoRender = $autoRender;
		
		// Check if not external link
		if (!
				(strpos($url, '://') !== false ||
				(strpos($url, 'javascript:') === 0) ||
				(strpos($url, 'mailto:') === 0)) ||
				(!strncmp($url, '#', 1))
		) {
			if (strpos($url, '/')) {
				$url = FULL_BASE_URL . $url;
			} else {
				$this->name = get_class($this);
				
				$url = FULL_BASE_URL . '/' . $this->name . '/' . $url;
			}
		}
		
		if (function_exists('session_write_close')) {
			session_write_close();
		}
		
		if ($url !== null) {
			$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
			if ($_SERVER["SERVER_PORT"] != "80")
			{
			    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			} 
			else 
			{
			    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
			if ($pageURL !== $url)
				header('Location: ' . $url);
		}
		
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