<?php
namespace YeTii\MultiScraper\Attributes;

class Id {

	protected $value;

	function __construct($value = null) {
		if (!is_null($value))
			$this->set($value);
	}

	public function get($default = null) {
		return !is_null($this->value) ? $this->value : $default;
	}

	public function set($value) {
		$this->value = $value;
		
		return $this;
	}

}