<?php
namespace YeTii\MultiScraper\Attributes;

class Files {

	protected $value;

	function __construct($value = null) {
		if (!is_null($value))
			$this->set($value);
	}

	public function get($default = null) {
		return is_array($this->value) ? $this->value : $default;
	}

	public function set(array $value) {
		$this->value = [];

		foreach ($value as $file) {
			$this->add($file);
		}
		
		return $this;
	}

	public function add($value) {
		if (is_array($value)) {
			foreach ($value as $value2) {
				$this->add($value2);
			}
		}elseif (is_object($value)){
			$file = new File((object)[
				'path'=>$value->path,
				'file_size'=> $value->file_size
			]);
			if (!is_array($this->value))
				$this->value = [];
			if (!in_array($file, $this->value))
				$this->value[] = $file;
		}

		return $this;
	}

	public function remove($key) {
		if (isset($this->value[$key]))
			unset($key);

		return $this;
	}

	// private function validate($value) {
	// 	if (!isset($value->path)||!isset($value->file_size)) return false;

	// 	if (is_string($value->path)) {
	// 		if (get_class($value->file_size)=='FileSize') {
	// 			return true;
	// 		}else{
	// 			throw new \Exception("The file's size invalid. Must be an instance of YeTii\MultiScraper\Attributes\FileSize.", 1);
	// 		}
	// 	}else{
	// 		throw new \Exception("The file's path invalid. Must be a string.", 1);
	// 	}
	// 	return false;
	// }


}