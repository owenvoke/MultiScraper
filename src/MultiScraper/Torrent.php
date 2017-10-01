<?php
namespace YeTii\MultiScraper;

use \JJG\Request;

class Torrent {

	protected $save_path = 'torrents';

	protected $attributes = [
		'title'=>'',
		'hash'=>'',
		'date_created'=>'',
		'magnet_link'=>'',
		'file_size'=>'',
		'description'=>'',
		'is_verified'=>'',
		'files'=>'',
		'file_count'=>'',
		'trackers'=>'',
		'images'=>'',
		'category'=>'',
		'user'=>'',
		'tpb_id'=>''
	];

	protected $source = null;

	function __construct($instance, array $args = null) {
		$this->instance = $instance;
		foreach ($args as $key => $value) {
			$this->attributes[$key] = $value;
		}
		$this->dirty = $this->attributes;
	}

	function __get($name) {
		if (isset($this->attributes[$name]))
			return $this->attributes[$name]->get();
		elseif(isset($this->{$name}))
			return $this->{$name};
		else
			return null;
	}

	function __set($name, $value) {
		if (isset($this->attributes[$name]))
			$this->attributes[$name]->set($value);
		else
			$this->{$name} = $value;
	} 

	public function set($key, $value) {
		if (isset($this->attributes[$key]))
			$this->attributes[$key] = $value;
		else
			printDie("\$this->attributes['$key'] is not set", false);
	}

	public function fill() {
		$scrapers = getScrapers([$this->source]);
		$order = [];
		foreach ($scrapers as $scraper) {
			$count = 0;
			$s = new $scraper($this->instance);
			foreach ($s->scrapable as $key) {
				if (in_array($key, $this->attributes))
					$count++;
			}
			$order[$scraper] = $count;
			unset($s);
		}
		arsort($order);

		foreach ($order as $s => $count) {
			$scraper = new $s($this->instance);
			$tmp = $scraper->torrent((object)[
				'hash'=>$this->attributes['hash'],
				'tpb_id'=>$this->attributes['tpb_id']
			]);
			$this->merge($tmp);
			printDie($tmp, false);
			printDie('====', false);
			printDie($this->attributes);

			// if valid, break;
		}
		printDie('end of fill');
	}

	public function merge($with) {
		foreach ($with as $key => $value) {
			if (!is_null($value)) {
				$this->set($key, $value);
			}
		}
		return $this;
	}

	public function load($hash) {
		$p = $this->save_path.'/'.$hash;
		if (file_exists($p)) {
			$read = file_get_contents($p);
			if (!$read)
				throw new \Exception("Could not load Torrent: {$hash}", 1);
			$read = json_decode($read);
			if (!$read)
				throw new \Exception("Could not read Torrent: {$hash}", 1);
		}else{
			throw new \Exception("Could not find Torrent: {$hash}", 1);
		}
	}

	public function save() {
		if ($this->hash && $this->dirty!==$this->attributes) {
			$p = $this->save_path.'/'.$this->hash;
			file_put_contents($p, json_encode($this->toString()));
		}
	}

	public function toString() {
		$torrent = [];
		foreach ($this->attributes as $key => $value) {
			$torrent[$key] = $value->get();
		}
		return (object)$torrent;
	}

}