<?php

class HTMLHelper extends Helper {
	
	public static function css($file) {
		if (is_array($file)) {
			foreach ($file as $name) {
				self::css($name);
			}
		} else {
			$checkNameString = strpos($file, 'http://');
			if ($checkNameString === false) {
				$file_ext = substr($file, -4);
				if ($file_ext != ".css") {
					$file .= ".css";
				}
				$path = BASE_PATH . 'css/' . $file;
				
				if (file_exists($path)) {
					$www_path = "/css/" . $file;
					echo "\t<link type = \"text/css\" rel = \"stylesheet\" href = \"" . $www_path . "\" />\n";
				}
			} else {
				echo "\t<link type = \"text/css\" rel = \"stylesheet\" href = \"" . $file . "\" />\n";
			}
		}
	}
	
	public static function script($file) {
		if (is_array($file)) {
			foreach ($file as $name) {
				self::script($name);
			}
		} else {
			$checkNameString = strpos($file, 'http://');
			if ($checkNameString === false) {
				$file_ext = substr($file, -3);
				if ($file_ext != ".js") {
					$file .= ".js";
				}
				$path = BASE_PATH . 'js/' . $file;
				
				if (file_exists($path)) {
					$www_path = "/js/" . $file;
					echo "\t<script type = \"text/javascript\" src = \"$www_path\"></script>\n";
				}
			} else {
				echo "\t<script type = \"text/javascript\" src = \"$file\"></script>\n";
			}
		}
	}
	
	public static function cssAll() {
		if ($handle = opendir(BASE_PATH . '/css/')) {
			while (false !== ($entry = readdir($handle))) {
				$file_ext = substr($entry, -4);
				if ($entry != "." && $entry != ".." && $file_ext == ".css") {
					self::css($entry);
				}
			}
			closedir($handle);
		}
		
	}
	
	public static function scriptAll() {
		if ($handle = opendir(BASE_PATH . '/js/')) {
			while (false !== ($entry = readdir($handle))) {
				$file_ext = substr($entry, -3);
				if ($entry != "." && $entry != ".." && $file_ext == ".js") {
					self::script($entry);
				}
			}
			closedir($handle);
		}
		
	}
	
	public static function image($name, $alt = '', $params = null) {
		$checkNameString = strpos($name, 'http://');
		if ($checkNameString === false){
			$path = "/img/" . $name;
		}else{
			$path = $name;
		}
		$attr = '';
		if ($params) {
			foreach ($params as $key => $value) {
				$attr .= " $key = \"$value\"";
			}
		}
		echo "<img src = \"$path\" alt = \"$alt\" $attr />\n";
	}
	
	public static function link($path, $text, $attribs = null) {
		$attr = '';
		if ($attribs) {
			foreach ($attribs as $key => $value) {
				$attr .= " $key = \"$value\"";
			}
		}
		$path = self::base() . '/' . $path;
		echo "<a href = \"" . $path . "\"$attr>" . $text . "</a>";
	}
	
	public static function image_link($path, $image, $imgattr = null, $linkattr = null) {
		$checkPathString = strpos($path, 'http://');
		$checkImageString = strpos($image, 'http://');
		if ($checkPathString === false){
			$path = self::base() . '/' . $path;
		}
	$imgattributes = '';
		if ($imgattr && is_array($imgattr)) {
			foreach ($imgattr as $key => $value) {
				$imgattributes .= " $key = \"$value\"";
			}
		} else if ($imgattr) {
			$imgattributes .= " alt = \"$imgattr\"";
		}
		
		$linkattributes = '';
		if ($linkattr && is_array($linkattr)) {
			foreach ($linkattr as $key => $value) {
				$linkattributes .= " $key = \"$value\"";
			}
		} else if ($linkattr) {
			$linkattributes .= " alt = \"$linkattr\"";
		}
		if ($checkImageString === false){
			$img_path = self::base() . '/img/' . $image;
		}else{
			$img_path = $image;
		}
		echo "<a href = \"".$path."\" $linkattributes><img src = \"$img_path\" $imgattributes /></a>\n";
	}
	
}