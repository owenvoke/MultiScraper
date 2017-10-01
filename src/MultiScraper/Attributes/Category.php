<?php
namespace YeTii\MultiScraper\Attributes;

class Category {

	protected $value;

	function __construct($value = null) {
		if (!is_null($value))
			$this->set($value);
	}

	public function get($default = null) {
		return is_numeric($this->value) ? $this->value : $default;
	}

	public function set($value) {
		if (is_numeric($value)) {
			$this->value = (int)$value;
		}elseif (is_object($value) && preg_match('^[a-z0-9]+Category$', get_class($value))){
			$this->value = $value;
		}else{
			throw new \Exception("Invalid Category", 1);
		}
		
		return $this;
	}

}