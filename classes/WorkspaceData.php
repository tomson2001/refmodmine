<?php
class WorkspaceData {

	public $path;
	public $files;
	
	private $filePrefix = "workspace.epml.";
	
	private $filetypes = array(
		"metrics" => "csv"
	);
	
	public function __construct($workspacePath) {

		$this->path = $workspacePath;
		$files = scandir($this->path);
		
		// remove all entries which are no empl
		foreach ( $this->files as $index => $entry ) {
			if ( substr($entry, 0, 15) != $this->filePrefix ) {
				unset($this->files[$index]);
			} else {
				$this->files[str_replace($this->filePrefix, "", $entry)] = $entry;
			}
		}
		
		var_dump($this->files);
	}
	
	private function getDownloadLink($file) {
		
	}
	
	private function getFileType($file) {
		
	}
	
}
?>