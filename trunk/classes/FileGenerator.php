<?php
class FileGenerator {
	
	public $filename;
	public $content;
	public $filePrefix;
	
	public function __construct($filename, $content) {
		$this->filename = $filename;
		$this->content = $content;
		$this->filePrefix = date('Y-m-d_H-i-s');
	}
	
	public function execute() {
		$uri = "files/".$this->filePrefix."_".$this->filename;
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