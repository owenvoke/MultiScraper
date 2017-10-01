<?php
namespace YeTii\MultiScraper;

use \JJG\Request;

class Scraper {

	public function getHtml($url) {
		$hash = sha1($url);
		if ($this->debug&&file_exists("cache/{$hash}.cache"))
			return file_get_contents("cache/{$hash}.cache");
		for ($i=0;$i<3;$i++) {
			// printDie('Requesting iteration x'.($i+1).' on '.$url, false);
			$request = new Request($url);
			$request->execute();
			if ($request->getHttpCode()==200) {
				$html = $request->getResponse(); $i=3;
				if ($this->debug)
					file_put_contents("cache/{$hash}.cache", $html);
				return $html;
			}
		}
		return null;
	}

	public function getScraper($name) {
		$name = preg_match('/^http(?:s??):\/\//', $name) ? preg_quote($name) : 'http(?:s??):\/\/'.preg_quote($name);
		foreach (scandir('src/MultiScraper/Scrapers') as $f) {
			if ($f[0]=='.') continue;
			$class = 'YeTii\\MultiScraper\\Scrapers\\'.Str::beforeLast($f, '.');
			if (class_exists($class)) {
				if (preg_match($name, $class->base_url))
					return $class;
			}
		}
		return null;
	}

	public function getMap($name) {
		return isset($this->map[$name]) ? $this->map[$name] : null;
	}
	public function setMap($name, $value) {
		if (isset($this->map[$name]))
			$this->map[$name] = $value;
	}
	public function getRule($name) {
		return isset($this->rules[$name]) ? $this->rules[$name] : null;
	}
	public function setRule($name, $value) {
		if (isset($this->rules[$name]))
			$this->rules[$name] = $value;
	}

	public function listMap() {
		$return = [];
		foreach ($this->map as $key => $value) {
			$return[] = $key;
		}
		return $return;
	}

	public function validate($torrent) {
		$all = $this->listMap();
		$ignore = [];
		$required = [];
		if ($this->getRule('require_all')) {
			$required = $all;
		}elseif ($fields = $this->getRule('require_fields')) {
			$required = $fields;
		}elseif ($fields = $this->getRule('ignore_fields')) {
			$ignore = $fields;
		}

		foreach ($all as $key) {
			if (in_array($key, $required)) {
				if (!isset($torrent->{$key})) {
					log("Skipping torrent {$torrent->hash} -- missing field: {$key}"); 
					return false;
				}
			}elseif (in_array($key, $ignore)) {
				if (isset($torrent->{$key}))
					unset($torrent->{$key});
			}
		}

		if ($this->getRule('image_extract')) {
			$torrent->images = $this->extractImages($torrent->description);
		}

		return true;
	}
	public function extractImages($description) {
		$images = [];
		preg_match_all('/(http(?:s)??:\/\/[^\s\/"]+\/[^\s"]+\.(?:png|jpeg|jpg))/im', $description, $m);
		if (isset($m[1]) && !empty($m[1]) && $raw_images = $m[1]) {
			$image_path = $this->getRule('image_save_path');
			if (is_string($image_path) && strlen($image_path)) {
				$image_path = rtrim($image_path, '/');
				$image_convert = $this->getRule('image_convert');
				if ($image_convert!=='png'||$image_convert!=='jpg') $image_convert = null;
				if (file_exists($image_path) && !is_dir($image_path))
					throw new Exception("Image output folder is a file. Cannot output here.", 1);
				if (!file_exists($image_path))
					mkdir($image_path);
				foreach ($raw_images as $image) {
					preg_match('/\.(png|jpg|jpeg)$/i', $image, $m);
					if (isset($m[1])) {
						if ($resource = @file_get_contents($image)) {
							if ($im = imagecreatefromstring($resource)) {
								if (!$image_convert) {
									$image_convert = $m[1];
								}
								if (($imw = imagesx($im)) && ($imh = imagesy($im))) {
									if (file_exists('tmp.image'))
										unlink('tmp.image');
									if ($image_convert=='png') {
										imagepng($im, 'tmp.image');
									}else{
										imagejpeg($im, 'tmp.image');
									}
									if (file_exists('tmp.image')) {
										$hash = sha1_file('tmp.image');
										$i = new \stdClass;
										$i->{$this->map['images.remote']} = $image;
										$i->{$this->map['images.local']} = $image_path.'/'.$hash.'.'.$image_convert;
										if (file_exists('tmp.image')) {
											copy('tmp.image', $i->{$this->map['images.local']});
										}
										$i->{$this->map['images.width']} = $imw;
										$i->{$this->map['images.height']} = $imh;
										$images[] = $i;
									}
								}
							}
						}
					}
				}
			}else{
				$images = $m[1];
			}
		}
		return $images;
	}

	public function getGetter($key) {
		$new = 'get';
		foreach (preg_split('/[\.\-\_]/', $key) as $k) {
			$new .= ucfirst($k);
		}
		return $new;
	}

}