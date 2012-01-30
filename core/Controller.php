<?php

// contain's methods for all the user defined controllers
class Controller extends Object {
	
	
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
}