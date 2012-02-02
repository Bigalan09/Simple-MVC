<?php

class Helper {
	
	public static function base() {
		$host = '/' . $_SERVER['HTTP_HOST'];
		$base = '/' . basename(dirname(dirname(dirname(__FILE__))));
		if ($host == $base) {
			$base = "";
		}
		return $base;
	}
}