<?php
namespace YeTii\MultiScraper\Attributes;

class Images {

	protected $value;

	function __construct($value = null) {
		if (!is_null($value))
			$this->set($value);
	}

	public function get($default = null) {
		return is_array($this->value) ? $this->value : $default;
	}

	public function set(array $value) {
		$this->value = $value;
		
		foreach ($value as $image) {
			$this->add($image)
		}

		return $this;
	}

	public function add($value) {
		if (is_array($value)) {
			foreach ($value as $value2) {
				$this->add($value2);
			}
		}else{
			if (!is_array($this->value))
				$this->value = [];
			if (!in_array($value, $this->value))
				$this->value[] = new Image($value);
		}

		return $this;
	}

	public function remove($key) {
		if (isset($this->value[$key]))
			unset($key);

		return $this;
	}


}