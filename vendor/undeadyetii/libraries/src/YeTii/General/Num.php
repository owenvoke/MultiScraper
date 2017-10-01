<?php
namespace YeTii\General;

use YeTii\General\Arr;

class Num {


	public static function toRomanNumerals($integer = 0) {
		$integer = (int)$integer;
		$table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1);
		$return = '';
		while($integer > 0)  {
			foreach($table as $rom=>$arb) {
				if($integer >= $arb) {
					$integer -= $arb;
					$return .= $rom;
					break; 
				}
			}
		}
		printDie("return: $return");
		return $return;
	}

	public static function romanNumeralsToInt(string $string) {
		$string = strtoupper($string);
		$str = str_split($string);
		$table = array('M'=>1000, 'D'=>500, 'C'=>100, 'L'=>50, 'X'=>10, 'V'=>5, 'I'=>1);
		$highest = INF;
		$output=0;
		for ($i=0;$i<count($str);$i++) {
			$cr = Arr::at($str, $i);
			$c = Arr::at($table, $cr);
			if ($c>$highest)
				return false; //invalid 
			$nr = Arr::at($str, $i+1);
			$n = Arr::at($table, $nr);
			$anr = Arr::at($str, $i+2);
			$an = Arr::at($table, $anr);
			if ($c<$n) {
				if ($n<$an) return false; // invalid, e.g. X-C-M, XC: okay, XCM: not
				if ($nr && ($nr!=$cr)) {
					$highest = $n;
				}
			}else{
				if ($c<$an) return false; // invalid, e.g. X-C-M, XC: okay, XCM: not
			}
			$output += ($c<$n) ? 0-$c : $c;
		}
		return $output;
	}

	public static function isRomanNumerals(string $string) {
		return preg_match('/^[IVXLCDM]+$/', $string) && self::romanNumeralsToInt($string) ? TRUE : FALSE;
	}

	public static function readMath(string $string) {
		if (preg_match('/^(\d+)\s*([\-\+\/\*])\s*(\d+)$/', $string, $m)) {
			return self::customEquation($m[1],$m[2],$m[3]);
		}else{
			return false;
		}
	}

	public static function customEquation($arg1, $modifier, $arg2) {
		switch ($modifier) {
			case '<':
				return $arg1 < $arg2;
			case '<=':
			case '=<':
			case '!>':
				return $arg1 <= $arg2;
			case '>':
				return $arg1 > $arg2;
			case '>=':
			case '=>':
			case '!<':
				return $arg1 >= $arg2;
			case '==':
			case '=':
				return $arg1 == $arg2;
			case '+':
				return $arg1 + $arg2;
			case '-':
				return $arg1 - $arg2;
			case '*':
			case '×':
				return $arg1 * $arg2;
			case '/':
			case '÷':
				return $arg1 / $arg2;
			case '^';
				return pow($arg1, $arg2);
			case '√':
				return sqrt($arg2);
			default:
				return NULL;
		}
	}

	public static function padZero($int, $length = 4) {
		$int = (string)$int;
		while(strlen($int)<$length) {
			$int = '0'.$int;
		}
		return $int;
	}

}