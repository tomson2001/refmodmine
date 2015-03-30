<?php
class FileGenerator {
	
	public $filename;
	public $content;
	public $filePrefix;
	public $path = "";
	
	public function __construct($filename, $content) {
		$this->filename = $this->setFilename($filename);
		$this->content = $content;
		$this->filePrefix = date('Y-m-d_H-i-s');
	}
	
	public function execute($prefix = true) {
		$uri = $prefix ? "files/".$this->path."".$this->filePrefix."_".$this->filename : "files/".$this->path.$this->filename;
		$handle = fopen($uri, "w");
		fwrite($handle, $this->content);
		fclose($handle);
		return $uri;
	}
	
	public function setFilename($filename) {
		$filename = str_replace(":", "", str_replace(" ", "", $filename));
		$filename = str_replace("/", "-", $filename);
		$filename = str_replace("[", "-", $filename);
		$filename = str_replace("]", "-", $filename);
		$this->filename = $filename;
	}
	
	public function setPath($path) {
		$this->path = $path."/";
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	
}