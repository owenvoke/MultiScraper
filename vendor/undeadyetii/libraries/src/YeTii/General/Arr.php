<?php
namespace YeTii\General;

class Arr {

	public static function index(array $haystack, $needle) {
		return array_search($needle, $haystack);
	}

	public static function at(array $haystack, $index, $default = null) {
		return isset($haystack[$index]) ? $haystack[$index] : $default;
	}

	public static function extend() {
		$arrays = func_get_args();
		$base = array_shift($arrays);
		foreach ($arrays as $array) {
			reset($base);
			while (list($key, $value) = @each($array))
				if (is_array($value) && @is_array($base[$key]))
					$base[$key] = Arr::extend($base[$key], $value);
				else $base[$key] = $value;
		}
		return $base;
	}

	public static function suffle(array $array, $key, $value) {
		shuffle($array);
		return $array;
	}

	

}