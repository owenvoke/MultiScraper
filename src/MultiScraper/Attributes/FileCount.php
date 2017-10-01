<?php
namespace YeTii\MultiScraper\Attributes;

class FileCount {

	protected $value;

	function __construct($value = null) {
		if (!is_null($value))
			$this->set($value);
	}

	public function get($default = null) {
		return is_numeric($this->value) ? $this->value : $default;
	}

	public function set($value) {
		$this->value = (int)$value;
		
		return $this;
	}

}