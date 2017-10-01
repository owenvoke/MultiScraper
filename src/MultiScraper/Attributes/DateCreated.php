<?php
namespace YeTii\MultiScraper\Attributes;

class DateCreated {

	protected $value;

	function __construct($value = null) {
		if (!is_null($value))
			$this->set($value);
	}

	public function get($default = null) {
		return is_numeric($this->value) ? $this->value : $default;
	}

	public function set($value) {
		if (preg_match('/^\d+$/', trim($value))){
			$this->value = (int)$value;
		} else {
			$value = strtotime($value);
			if ($value)
				$this->value = $value;
			else
				throw new \Exception("Invalid DateCreated", 1);
		}
		
		return $this;
	}

}