<?php

use \YeTii\General\Str;

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
}
function printDie($var, $die = true) {
	print '<pre>'.print_r($var, true).'</pre>';
	if ($die) die();
}
function strtobytes($str) {
	preg_match('/(\d+(?:\.\d+|))(?:&nbsp;|\W)*(b|k|m|g|t|p)/i', $str, $m);
	if (!isset($m[1])) return null;
	if (!isset($m[2])&&isset($m[1])) return $m[1];
	$multiplier = 1;
	switch (strtolower($m[2])) {
		case 'p':
			$multiplier *= 1024;
		case 't':
			$multiplier *= 1024;
		case 'g':
			$multiplier *= 1024;
		case 'm':
			$multiplier *= 1024;
		case 'k':
			$multiplier *= 1024;
	}
	return (int)($multiplier*$m[1]);
}
function getScrapers($ignore = []) {
	$scrapers = [];
	foreach (scandir('src/MultiScraper/Sites') as $f) {
		if ($f[0]=='.') continue;
		$c = 'YeTii\\MultiScraper\\Sites\\'.Str::beforeLast($f, '.');
		if (class_exists($c) && !in_array(Str::beforeLast($f, '.'), $ignore))
			array_push($scrapers, $c);
	}
	return $scrapers;
}
// function log($str, $die = false) {
// 	echo $str.PHP_EOL;
// 	if ($die) die();
// }
// function nestFiles($files) {
// 	$return = (array)[];
// 	foreach ($files as $file) {
// 		$return = set_val($return, $file->dir, $file);
// 	}
// 	printDie($return);
// }

// function set_val(array &$arr, $path, $val) {
// 	if (!Str::contains($path, '/')) return $arr;
// 	$loc = &$arr;
// 	$parts = explode('/', Str::beforeLast($path, '/'));
// 	foreach(explode('/', $path) as $step) {
// 		$loc = &$loc[$step];
// 	}
// 	return $loc = $val;
// }

