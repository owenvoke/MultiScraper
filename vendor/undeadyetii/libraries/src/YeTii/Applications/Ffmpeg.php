<?php
namespace YeTii\Applications;

use YeTii\General\Str;

class Ffmpeg {

	protected $format = 'mp4';
	protected $ffmpeg_dir = '/usr/local/bin/ffmpeg';
	protected $delete_original = 0;

	protected $input;
	protected $output;

	protected $error;

	private $formats_available = array('mp4','mkv','avi','m4v','mpg','flv');

	function __construct(array $args = []) {
		if (isset($args['format'])) $this->format($args['format']);
		if (isset($args['ffmpeg_dir'])) $this->format($args['ffmpeg_dir']);
		if (isset($args['delete_original'])) $this->format($args['delete_original']);
	}

	public function format($value = NULL) {
		if (!in_array($value, $this->formats_available)) {
			$this->error = 'Unknown format: .'.$value; return FALSE;
		}
		$this->format = $value;
		return $this;
	}

	public function ffmpeg_dir($value = NULL) {
		if (!file_exists($value)) {
			$this->error = 'ffmpeg not found in '.$value; return FALSE;
		}
		$this->ffmpeg_dir = $value;
		return $this;
	}

	public function delete_original($value = NULL) {
		$this->delete_original = $value ? 1 : 0;
		return $this;
	}

	public function from(\YeTii\FileSystem\FileStructure $value = NULL) {
		if (!$value->exists()) {
			$this->error = 'Input file not found at '.$value->file_path; return false;
		}
		$this->input = $value;
		return $this;
	}

	public function to(\YeTii\FileSystem\FileStructure $value = NULL) {
		if ($ext = $value->getExt()) {
			if (!in_array($ext, $this->formats_available)) {
				$this->error = "Format .$ext is not supported"; return false;
			}else{
				$this->format = $ext;
				$value = $value->mock_rename(Str::stripExtension($value->filename()));
			}
		}
		$this->output = $value;
		return $this;
	}

	private function ready() {
		if ($this->error) return false;
		if (!$this->input->exists()) return false;
		if (file_exists($this->output->file_path().'.'.$this->format)) {
			$this->error = 'File already exists at '.$this->output->file_path().'.'.$this->format; return false;
		}
		return true;
	}

	public function mux() {
		if (!$this->ready()) return NULL;
		$str = exec($this->ffmpeg_dir.' -i "'.$this->input->file_path().'" -c copy "'.$this->output->file_path().'.'.$this->format.'" 2>&1');
		if (preg_match('/video:/', $str) && $this->delete_original)
			$this->input->delete();
		return preg_match('/video:/', $str) ? true : false;
	}

}