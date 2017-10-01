<?php
namespace YeTii\General;

use YeTii\General\Debug;

class Json {

	public static function output($json, $die = FALSE) {
		Debug::json($json, $die);
	}
	
	public static function toString($json) {
		return json_decode($json);
	}

}
