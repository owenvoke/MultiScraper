<?php
namespace YeTii\General;

class Debug {

	private static $styles_added = false;
	private static $method = 'print_r'; // print_r|json

	public static function json($var, $die = TRUE) {
		self::$method = 'json';
		return self::dump($var, $die);
	}
	public static function print($var, $die = TRUE) {
		self::$method = 'print_r';
		return self::dump($var, $die);
	}

	public static function dump($var, $die = TRUE) {
		$html = '';
		self::init_style();
		print '<pre>';
		$output = self::$method=='print_r' ? $output = print_r(self::debug_makevisible($var), true) : json_encode($var, JSON_PRETTY_PRINT);
		$regex = self::$method=='print_r' ?
				array(
					'=> (\d+(?:\.\d+){0,1})$' => '=> <i class="pu">$1</i>',
					'(\S+? Object|Array)\s+\(' => '<i class="b">$1 (</i>',
					'\[([^\]]+)\]' => '<i class="o">[$1]</i>',
					'(\s+)\)' => '$1<i class="b">)</i>',
					'=>' => '<i class="pi">=></i>'
				): 
				array(
					'(\[|\{)\s*(\}|\])' => '$1 <i class="pu">&lt;empty&gt;</i> $2',
					'": ""' => '": "<i class="pu">&lt;empty-string&gt;</i>"',
					'(\s+)"([^"]+)": (\w+)' => '$1<i class="o">"$2"</i>: <i class="pu">$3</i>',
					'^(\s+)"([^"]+)": (".*")' => '$1<i class="o">"$2"</i>: <i class="y">$3</i>',
					'(\s+)"([^"]+)": ([\{\[])' => '$1<i class="o">"$2"</i>: $3',
					'(^\s*)([\]\}])' => '</i>$1$2',
					'<\/i>: (\{|\[)' => '</i>: <i class="b">$1</i><i class="toggle" onclick="toggle(this)"> + </i><span class="open" style="display:none">',
					'<\/i>: ' => '</i><i class="pi">: </i>',
					'(\}|\])(,)*$' => '</span><i class="b">$1</i>$2',
					'^\{' => '<i class="b">{</i>'
					// '([\{\[])$' => '<i class="b">$1</i><i onclick="toggle(this)">+</i><i style="display:none">',
				);
		foreach ($regex as $key => $value) {
			$output = preg_replace("/$key/im", $value, $output);
		}
		// $output = preg_replace('//im', function($m) {
		// 	$id = microtime().random_int(0, 1000000);
		// 	return $m[1].'<i onclick="toggle(\'debug_'.$id.'\')">+</i><i id="debug_'.$id.'">';
		// }, $output);

		print_r($output);
		print '</pre>';	
		if ($die) die('');
	}

	private static function debug_makevisible($variable, $name = 'main') {
		$is_object = is_object($variable);
		$variable = $is_object ? (array)$variable : $variable;
		if (is_array($variable)) {
			foreach (array_keys($variable) as $i=>$key) {
				$value = $variable[$key];
				$variable[$key] = self::debug_makevisible($value, "$name/$key");
			}
			$variable = $is_object ? (object)$variable : $variable;
		}else{
			if ($variable===null) return '<i class="pu">null</i>';
			if ($variable===false) return '<i class="pu">false</i>';
			if ($variable===true) return '<i class="pu">true</i>';
			if ($variable==='') return '<i class="pu">empty-string</i>';
			if (is_string($variable)) return '<i class="y">'.$variable.'</i>';
		}
		return $variable;
	}

	private static function init_style() {
		if (self::$styles_added) return;
		print '<style>
					body {
						background:#272822;
						color: #f8f8f2;
						font-size:12px;
					}
					i {
						font-style: normal !important;
					}
					.pi {
						color: #fc1e70;
					}
					.b {
						color: #5bcfe4;
					}
					.pu {
						color: #af7dff;
					}
					.o {
						color:#ff9800;
					}
					.y {
						color: #e6dc6c;
					}
					.toggle {
						cursor: pointer;
					    -webkit-user-select: none; /* webkit (safari, chrome) browsers */
					    -moz-user-select: none; /* mozilla browsers */
					    -khtml-user-select: none; /* webkit (konqueror) browsers */
					    -ms-user-select: none; /* IE10+ */
					}
				</style><script>
					function toggle(btn){
						var elem = btn.nextElementSibling;
						if(elem.style.display == "inline"){
						    elem.style.display="none";
							btn.innerHTML = " + ";
						} else {
							btn.innerHTML = " - ";
						    elem.style.display="inline";
						    if (elem.innerText.trim()==\'<empty>\')
						    	btn.style.display="none";
						}
					}
				</script>';
		self::$styles_added = 1;
	}
}