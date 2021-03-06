<?php
class FileGenerator {
	
	public $filename;
	public $content;
	public $filePrefix;
	public $path = "";
	public $isPathFilename = false;
	
	public function __construct($filename, $content) {
		$this->filename = $this->setFilename($filename);
		$this->content = $content;
		$this->filePrefix = date('Y-m-d_H-i-s');
	}
	
	public function execute($prefix = true, $bom = true) {
		$uri = "";
		if ( $this->isPathFilename ) {
			$uri = $this->filename;
		} else {
			$uri = $prefix ? "files/".$this->path."".$this->filePrefix."_".$this->filename : "files/".$this->path.$this->filename;
		}
		$handle = fopen($uri, "w");
		
		$content = $bom ? "\xEF\xBB\xBF".$this->content : $this->content;
		
		fwrite($handle, $content);
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
	
	public function setPathFilename($filename) {
		$filename = str_replace(":", "", str_replace(" ", "", $filename));
		$filename = str_replace("[", "-", $filename);
		$filename = str_replace("]", "-", $filename);
		$this->filename = $filename;
		$this->isPathFilename = true;
	}
	
	public function setPath($path) {
		$this->path = $path."/";
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	
}