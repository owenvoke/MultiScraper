<?php
namespace YeTii\General;

use YeTii\General\Num;

class Str {

	public static function contains(string $haystack, string $needle, $ignoreCase = FALSE) {
		return $ignoreCase ? substr_count(strtolower($haystack), strtolower($needle)) : substr_count($haystack, $needle);
	}

	public static function startsWith(string $haystack, string $needle, $ignoreCase = FALSE) {
		return $needle === "" || ($ignoreCase ? strripos($haystack, $needle, -strlen($haystack)) : strrpos($haystack, $needle, -strlen($haystack))) !== false;
	}

	public static function endsWith(string $haystack, string $needle, $ignoreCase = FALSE) {
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && ($ignoreCase ? stripos($haystack, $needle, $temp) : strpos($haystack, $needle, $temp)) !== false);
	}

	public static function afterLast(string $string, string $delimiter, $ignoreCase = FALSE) {
		return ($pos = ($ignoreCase ? strripos($string, $delimiter) : strrpos($string, $delimiter))) === false ? $string : substr($string, $pos + 1);
	}

	public static function beforeLast(string $string, string $delimiter, $ignoreCase = FALSE) {
		return substr($string, 0, ($ignoreCase ? strripos($string, $delimiter) : strrpos($string, $delimiter)));
	}

	public static function afterFirst(string $string, string $delimiter, $ignoreCase = FALSE) {
		return ($pos = ($ignoreCase ? stripos($string, $delimiter) : strpos($string, $delimiter))) !== false ? substr($string, $pos+1) : ''; 
	}

	public static function beforeFirst(string $string, string $delimiter, $ignoreCase = FALSE) {
		return substr($string, 0, ($ignoreCase ? stripos($string, $delimiter) : strpos($string, $delimiter)));
	}

	public static function first(string $string, int $length) {
		return strlen($string)<$length ? $string : substr($string, 0, $length);
	}

	public static function last(string $string, int $length) {
		return strlen($string)<$length ? $string : substr($string, -$length);
	}

	public static function isLowerCase(string $string) {
		return $string===strtolower($string);
	}

	public static function isUpperCase(string $string) {
		return $string===strtoupper($string);
	}

	public static function toLowerCase(string $string) {
		return strtolower($string);
	}

	public static function toUpperCase(string $string) {
		return strtoupper($string);
	}

	public static function isCapitalized(string $string) {
		return strlen($string) ? ($string[0]==strtoupper($string[0])) : false;
	}

	public static function capitalizeWords(string $string, $forceLowerCaseOther = FALSE) {
		$string = $forceLowerCaseOther ? ucwords(strtolower($string)) : ucwords($string);

		foreach (array('-', '\'', '.', ' ') as $delimiter) {
			if (strpos($string, $delimiter)!==false) {
				$string = implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
			}
		}
		return $string;
	}

	public static function capitalizeTitle(string $string, $forceLowerCaseOther = FALSE) {
		$words = explode(' ', self::capitalizeWords($string, $forceLowerCaseOther));
		for ($i=0;$i<count($words);$i++) {
			if (!$i) continue;
			$word = $words[$i]; $prevword = isset($words[$i-1]) ? $words[$i-1] : NULL;
			if (in_array(strtolower($word), array('a','in','for','the','of','if','on','to')) && !preg_match('/[\.\:\!\?]$/', $prevword)) {
				$word = strtolower($word);
			}elseif (Num::isRomanNumerals(preg_replace('/[\W]+/', '', strtoupper($word)))) {
				$word = strtoupper($word);
			}
			$words[$i] = $word;
		}
		return implode(' ', $words);
	}

	public static function isRomanNumerals(string $string) {
		return Num::toRomanNumerals($string);
	}
	
	public static function replace(string $subject, string $find, string $replace = NULL, $ignoreCase = FALSE) {
		return $ignoreCase ? str_ireplace($find, $replace, $subject) : str_replace($find, $replace, $subject);
	}
	
	public static function replaceRegex(string $subject, string $find, string $replace = NULL, $ignoreCase = FALSE) {
		return $ignoreCase ? preg_replace("/$find/i", $replace, $subject) : preg_replace("/$find/", $replace, $subject);
	}
	
	public static function replaceFirst(string $haystack, string $needle, string $replace = NULL, $ignoreCase = FALSE) {
		return (($pos = ($ignoreCase ? stripos($haystack, $needle) : strpos($haystack, $needle))) !== false) ? substr_replace($haystack, $replace, $pos, strlen($needle)) : $haystack;
	}
	
	public static function replaceLast(string $haystack, string $needle, string $replace = NULL, $ignoreCase = FALSE) {
		return (($pos = ($ignoreCase ? strripos($haystack, $needle) : strrpos($haystack, $needle))) !== false) ? substr_replace($haystack, $replace, $pos, strlen($needle)) : $haystack;
	}

	public static function suffix(string $string, string $suffix, $ifNotExists = FALSE, $ignoreCase = FALSE) {
		return $ifNotExists && self::endsWith($string, $suffix, $ignoreCase) ? $string : $string.$suffix;
	}

	public static function prefix(string $string, string $prefix, $ifNotExists = FALSE, $ignoreCase = FALSE) {
		return $ifNotExists && self::startsWith($string, $prefix, $ignoreCase) ? $string : $prefix.$string;
	}

	public static function replaceSuffix (string $subject, string $find, string $replace = NULL, $ignoreCase = FALSE) {
		return self::endsWith($subject, $find, $ignoreCase) ? self::replaceLast($subject, $find, $replace, $ignoreCase) : $subject;
	}

	public static function replacePrefix (string $subject, string $find, string $replace = NULL, $ignoreCase = FALSE) {
		return self::startsWith($subject, $find, $ignoreCase) ? self::replaceLast($subject, $find, $replace, $ignoreCase) : $subject;
	}

	public static function betweenGreedy(string $string, string $first, string $last = NULL, $ignoreCase = FALSE) {
		if ($last===NULL) $last = $first;
		return self::afterFirst(self::beforeLast($string, $last, $ignoreCase), $first, $ignoreCase);
	}

	public static function between(string $string, string $first, string $last = NULL, $ignoreCase = FALSE) {
		if ($last===NULL) $last = $first;
		return self::beforeFirst(self::afterFirst($string, $first, $ignoreCase), $last, $ignoreCase);
	}

	public static function betweenLazy(string $string, string $first, string $last = NULL, $ignoreCase = FALSE) {
		if ($last===NULL) $last = $first;
		return self::afterLast(self::beforeLast($string, $last, $ignoreCase), $first, $ignoreCase);
	}

	public static function equals(string $string1, string $string2, $ignoreCase = FALSE) {
		return $ignoreCase ? strtolower($string1)===strtolower($string2) : $string1===$string2;
	}

	public static function words(string $string) {
		return preg_split('/(\s[\W]\s|[\s])+/', $string);
	}

	public static function html(string $string) {
		return htmlspecialchars($string, ENT_QUOTES);
	}

	public static function newline(string $subject, $newline = NULL) {
		if ($newline===NULL) $newline = "\n";
		return preg_replace('/\R/u', $newline, $subject);
	}

	public static function reverse(string $string) {
		return strrev($string);
	}

	public static function acronym(string $string, $outputCapitalised = FALSE, $ignoreLowerCase = FALSE) {
		$str = '';
		foreach (self::words($string) as $word) {
			if (!$ignoreLowerCase || ($ignoreLowerCase && $word[0]==strtoupper($word[0])))
				$str.=$word[0];
		}
		return $outputCapitalised ? strtoupper($str) : $str;
	}

	public static function parseDir() {
		$return = '';
		foreach (func_get_args() as $str) {
			if ($str == '' || $str == NULL || $str == FALSE) continue;
			$return .= '/'.trim($str, '/');
		}
		return $return;
	}

	public static function stripExtension(string $string) {
		return preg_replace('/\.([a-z0-9]+)$/i', '', $string);
	}

	public static function url(string $str) {
		return trim(strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $str)), '-');
	}
}