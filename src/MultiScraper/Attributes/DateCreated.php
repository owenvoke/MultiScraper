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
			$value2 = strtotime($value);
			if ($value2)
				$this->value = $value2;
			else
				throw new \Exception("Invalid DateCreated (`{$value}`)", 1);
		}
		
		return $this;
	}

}