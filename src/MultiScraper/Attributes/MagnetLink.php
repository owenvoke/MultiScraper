<?php
namespace YeTii\MultiScraper\Attributes;

class MagnetLink {

	protected $value;

	function __construct($value = null) {
		if (!is_null($value))
			$this->set($value);
	}

	public function get($default = null) {
		return is_string($this->value) ? $this->value : $default;
	}

	public function set($value) {
		if (preg_match('/^magnet:\?xt=urn:btih:([a-f0-9]{40}).+/', trim($value))){
			$this->value = $value;
		}else{
			throw new \Exception("Invalid MagnetLink (`{$value}`)", 1);
		}
		
		return $this;
	}

}