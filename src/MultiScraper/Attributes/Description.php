<?php
namespace YeTii\MultiScraper\Attributes;

class Description {

	protected $value;

	function __construct($value = null) {
		if (!is_null($value))
			$this->set($value);
	}

	public function get($default = null) {
		return is_string($this->value) ? $this->value : $default;
	}

	public function set($value) {
		if (!is_string($value))
			throw new \Exception("Invalid Description", 1);
		$this->value = trim($value);
		
		return $this;
	}

}