<?php
class FileGenerator {
	
	public $filename;
	public $content;
	public $filePrefix;
	
	public function __construct($filename, $content) {
		$filename = str_replace(":", "", str_replace(" ", "", $filename));
		$this->filename = $filename;
		$this->content = $content;
		$this->filePrefix = date('Y-m-d_H-i-s');
	}
	
	public function execute($prefix = true) {
		$uri = $prefix ? "files/".$this->filePrefix."_".$this->filename : "files/".$this->filename;
		$handle = fopen($uri, "w");
		fwrite($handle, $this->content);
		fclose($handle);
		return $uri;
	}
	
	public function setFilename($filename) {
		$this->filename = $filename;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	
}