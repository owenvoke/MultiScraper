<?php
namespace YeTii\MultiScraper;

use \JJG\Request;
use \YeTii\General\Str;

class MultiScraper extends Scraper {

	private $debug = true;

	private $user = null;
	private $query = null;
	private $page = 1;

	function __construct(array $args = null) {
		$this->init($args);
	}

	function __get(string $name) {
		return isset($this->{$name}) ? $this->{$name} : null;
	}

	function __set(string $name, $value) {
		$this->{$name} = $value;
	}

	private function init($args) {
		if (is_array($args)) {
			foreach ($args as $key => $value) {
				// add 
			}
		}
	}

	public function latest($page = 1) {
		$this->page = $page;
		return $this->scrape();
	}

	public function scrape() {
		$torrents = [];
		$sites = getScrapers();
		foreach ($sites as $class) {
			print "Class: $class<br>";
			$scraper = new $class($this); 
			$torrent = null;
			if ($this->user) {
				$scraper->user($this->user, $this->page);
			}elseif ($this->query) {
				$scraper->search($this->query, $this->page);
			}else{
				$scraper->latest($this->page);
			}
		}
		print 'Scrape end';
		return true;
	}



}