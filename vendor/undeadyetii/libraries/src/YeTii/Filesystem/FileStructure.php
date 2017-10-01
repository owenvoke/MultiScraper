<?php
namespace YeTii\FileSystem;

use YeTii\General\Str;
use YeTii\General\Num;

class FileStructure {

	protected $filename; // name of file or folder
	protected $is_dir; // 1 or 0
	protected $exists; // 1 or 0
	protected $parent_path; // path of directory
	protected $parent_path_real; // path of directory
	protected $parent_name; // basename of parent_path
	protected $file_path; // $parent_path/$filename
	protected $file_path_real; // $parent_path/$filename
	protected $file_size; // size in bytes
	protected $date_modified; // date modified of file
	protected $children; // children as FileStructure object
	protected $children_count; // count of children
	protected $base_path; // the path to ignore from prefix


	// ------------------- INTERNAL FUNCTIONS ---------------------

	function __construct($path = null, array $args = []) {
		$this->settings = (object)array(
								'get_children'=>isset($args['get_children']) ? $args['get_children'] : TRUE,
								'get_recursive'=>isset($args['recursive']) ? $args['recursive'] : TRUE,
								'show_hidden_files'=>isset($args['show_hidden_files']) ? $args['show_hidden_files'] : TRUE,
		);
		if ($path) {
			$this->base_path = isset($args['base_path']) ? Str::parseDir($args['base_path']) : NULL;
			$this->set_file_path($path);
			$this->initialize();
		}
	}

	private function set_file_path($file_path_real) {
		if ($this->base_path!=null)
			$this->file_path = Str::replacePrefix($file_path_real, $this->base_path, '');
		else
			$this->file_path = $file_path_real;
		$this->file_path_real = $file_path_real;
		$this->filename = Str::afterLast(rtrim($this->file_path,'/'), '/');
		$this->parent_path = Str::beforeLast(rtrim($this->file_path,'/'), '/');
		$this->parent_path_real = Str::beforeLast(Str::parseDir($file_path_real), '/');
		$this->parent_name = Str::afterLast($this->parent_path, '/');
	}

	private function initialize() {
		if (!$this->file_path) return false;
		if (file_exists($this->file_path)) {
			$this->exists = 1;
			$this->is_dir = is_dir($this->file_path);
			$this->file_size = $this->filesize($this->file_path);
			$this->date_modified = filemtime($this->file_path);
			$this->children = ($this->settings->get_children || $this->settings->get_recursive) ? $this->children() : NULL;
		}else{
			$this->exists = 0;
		}
	}
	private function filesize($path){
		if (!is_dir($path)) return filesize($path);
		$size = 0;
	    foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
	    	if (file_exists($file->getRealPath()) && $file->isReadable())
		        $size += $file->getSize();
	    }
	    return $size;
	}

	private function filedelete($path) {
		if (file_exists($path)) {
			if (is_dir($path)) {
				foreach(scandir($path) as $f) {
					if($f=='.'||$f=='..'||$f=='.DS_Store') continue;
					$this->filedelete("$path/$f");
				}
				return rmdir($path);
			}else{
				return unlink($path);
			}
		}else{
			return false;
		}
	}

	private function internalfind($path, $search) {
		if (!isset($this->tmp)) $this->tmp = array();
		$path = rtrim($path, '/');
		if (file_exists($path)) {
			if (is_dir($path)) {
				foreach(scandir($path) as $f) {
					if($f=='.'||$f=='..'||$f=='.DS_Store') continue;
					$this->internalfind("$path/$f", $search);
				}
			}else{
				$name = Str::afterLast($path, '/');
				foreach ($search as $key => $value) {
					if ($key=='regex' && !preg_match($value, $name)) return false;
					elseif ($key=='text' && !Str::contains($name, $value)) return false;
					elseif ($key=='date_modified') {
						if (preg_match('/^(>|<|>=|<=|=<|=>|!=|==|=|!<|!>|)\s*(\d+)$/', $value, $m)) {
							if (!Num::customEquation(filemtime($path), $m[1]?$m[1]:'==', $m[2])) return false;
						}else {
							return null;
						}
					}elseif ($key=='size') {
						if (preg_match('/^(>|<|>=|<=|=<|=>|!=|==|=|!<|!>|)\s*(\d+)$/', $value, $m)) {
							if (!Num::customEquation(filesize($path), $m[1]?$m[1]:'==', $m[2])) return false;
						}else {
							return null;
						}
					}
				}
				$this->tmp[] = new FileStructure($path);
				return true;
			}
		}else{
			return false;
		}
		return true;
	}
	private function get_children($children) {
		if (empty($children)) return null;
		$return = array();
		foreach ($children as $child) {
			array_push($return, $child->get());
		}
		return $return;
	}


	// ------------------- PUBLIC FUNCTIONS ---------------------

	public function base_path(string $path) {
		$path = trim($path, '/');
	}
	public function mock_rename(string $to) {
		$copy = clone $this;
		$copy->filename = $to;
		$copy->file_path = Str::parseDir($copy->parent_path, $to);
		return $copy;
	}
	public function rename(string $to) {
		if (!$this->exists) return false;
		if (!strlen($to)) return false;
		$new_path = Str::parseDir($this->parent_path, $to);
		if (file_exists($new_path)) return false;
		rename($this->file_path, $new_path);
		$this->filename = $to;
		$this->parent_path = Str::afterLast(rtrim($new_path,'/'),'/');
		$this->file_path = $new_path;
	}

	public function find($what) {
		if (is_string($what) && preg_match('/^\/(.+)\/(|[igsm]+)$/', $what)) {
			$this->internalfind($this->file_path, ['regex'=>$what]);
		}elseif(is_string($what)) {
			$this->internalfind($this->file_path, ['text'=>$what]);
		}elseif(is_array($what)) {
			$this->internalfind($this->file_path, $what);
		}else{
			return false;
		}
		$return = $this->tmp;
		$this->tmp = null;
		return $return;
	}

	public function children() {
		if (!$this->is_dir) return NULL;
		$this->children = array();
		foreach (scandir($this->file_path) as $f) {
			if ($f=='.' || $f=='..'||$f=='.DS_Store') continue; // ignore these
			if (!$this->settings->show_hidden_files && $file[0]=='.') continue;
			$this->children[] = new FileStructure(Str::parseDir($this->file_path, $f), $this->settings->get_recursive);
		}
		$this->children_count = count($this->children);
		return $this->children;
	}

	public function parent($levels = 1) {
		if ($tmp = rtrim($this->parent_path, '/')) {
			printDie("level is $levels", false);
			while ($levels>1) {
				$tmp = Str::beforeLast($tmp, '/');
				$levels -= 1;				
			}
			printDie("level is now $levels", false);
			if (!$tmp) return null;
			$d = (new FileStructure($tmp, FALSE, FALSE));
			printDie($d->get());
		}else{
			return null;
		}
	}

	public function delete() {
		return $this->filedelete($this->file_path);
	}

	public function getExt() {
		if ($this->is_dir) return FALSE;
		return preg_match('/\.([a-z0-9]+)$/i', $this->filename, $m) ? $m[1] : NULL;
	}

	public function hasExt($match = null) {
		if ($this->is_dir) return FALSE;
		return $ext = $this->getExt() ? (is_string($match) ? $ext==$match : (is_array($match) ? in_array($ext, $match) : $ext?true:'false')) : false;
	}

	public function exists() {
		return $this->exists;
	}

	public function filename() {
		return $this->filename;
	}

	public function file_path() {
		return $this->file_path;
	}

	public function breadcrumbs() {
		$return = [];
		$base = '';
		foreach (explode('/', trim($this->file_path_real, '/')) as $crumb) {
			if ($this->base_path && Str::contains($this->base_path, Str::parseDir($base, $crumb)))
				continue;
			$return[] = (object)array(
				'filename'=>$crumb,
				'file_path_real'=>Str::parseDir(Str::parseDir($this->base_path, $base), $crumb),
				'file_path'=>$this->base_path ? Str::replacePrefix(Str::parseDir($base, $crumb), $this->base_path, '') : Str::parseDir($base, $crumb)
			);
			$base .= "/$crumb";
		}
		$this->breadcrumbs = $return;
		return $return;
	}




	// ------------------- TO OBJECT ---------------------

	public function get() {
		return (object)array(
			'filename'=>$this->filename,
			'is_dir'=>$this->is_dir,
			'exists'=>$this->exists,
			'parent_path'=>$this->parent_path,
			'parent_path_real'=>$this->parent_path_real,
			'parent_name'=>$this->parent_name,
			'file_path'=>$this->file_path,
			'file_path_real'=>$this->file_path_real,
			'file_size'=>$this->file_size,
			'date_modified'=>$this->date_modified,
			'children'=>$this->get_children($this->children),
			'children_count'=>$this->children_count,
			'base_path'=>$this->base_path,
		);
	}


}