<?php
namespace YeTii\MultiScraper\Attributes;

class Image {

	protected $remote;
	protected $local;
	protected $width;
	protected $height;

	function __construct($value = null) {
		if (!is_null($value))
			$this->set($value);
	}

	public function get($default = null) {
		return is_numeric($this->value) ? $this->value : $default;
	}

	public function set(object $value) {
		if (!(isset($value->remote) && isset($value->local) && isset($value->width) && isset($value->height)))
			throw new \Exception("Invalid Image? Must have 'remote', 'local', 'width', and 'height'", 1);
		$this->remote = $value->remote;
		$this->local = $value->local;
		$this->width = $value->width;
		$this->height = $value->height;
		
		return $this;
	}

}