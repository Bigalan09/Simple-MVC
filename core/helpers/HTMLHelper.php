<?php

class HTMLHelper extends Helper {
	
	public static function loadCSS() {
		$path = "http://".$_SERVER['HTTP_HOST']."/css/";
		if ($handle = opendir(BASE_PATH . '/css/')) {
			while (false !== ($entry = readdir($handle))) {
				$file_ext = substr($entry, -4);
				if ($entry != "." && $entry != ".." && $entry && $file_ext == ".css") {
					echo "\t<link type = \"text/css\" rel = \"stylesheet\" href = \"".$path.$entry."\" />\n";
				}
			}
			closedir($handle);
		}
		
	}
	
	public static function loadJS() {
		$path = "http://".$_SERVER['HTTP_HOST']."/js/";
		echo "\t<script type = \"text/javascript\" src = \"".$path."jquery.js\"></script>\n";
		if ($handle = opendir(BASE_PATH . '/js/')) {
			while (false !== ($entry = readdir($handle))) {
				$file_ext = substr($entry, -3);
				if ($entry != "." && $entry != ".." && $entry != "jquery.js" && $file_ext == ".js") {
					echo "\t<script type = \"text/javascript\" src = \"$path$entry\"></script>\n";
				}
			}
			closedir($handle);
		}
		
	}
	
	public static function image($name, $alt = '', $params = null) {
		$path = "http://".$_SERVER['HTTP_HOST']."/images/".$name;
		$attr = '';
		if ($params) {
			foreach ($params as $key => $value) {
				$attr .= " $key = \"$value\"";
			}
		}
		echo "<img src = \"$path\" alt = \"$name\" $attr />\n";
	}
	
	public static function link($path, $text, $attribs = null) {
		$attr = '';
		if ($attribs) {
			foreach ($attribs as $key => $value) {
				$attr .= " $key = \"$value\"";
			}
		}
		$path = "http://".$_SERVER['HTTP_HOST']."/".$path;
		echo "<a href = \"".$path."\"$attr>".$text."</a>";
	}
	
	public function image_link($path, $image, $attr = null) {
		$path = "http://".$_SERVER['HTTP_HOST']."/".$path;
		$attributes = '';
		if ($attr) {
			foreach ($attr as $key => $value) {
				$attributes .= " $key = \"$value\"";
			}
		}
		$img_path = "http://".$_SERVER['HTTP_HOST']."/images/".$image;
		
		echo "<a href = \"".$path."\"><img src = \"$img_path\" $attributes /></a>\n";
	}
	
}