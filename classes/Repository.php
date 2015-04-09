<?php
class Repository {
	
	private $path;
	private $files = array();
	public $epmls = array();
	
	public function __construct($userUserSessionPath = false) {
		
		if ( $userUserSessionPath ) {
			$sessionID = session_id();
			$this->path = Config::FILES_PATH."/".$sessionID;
			if ( is_dir($this->path) ) $this->files = scandir($this->path);
		} else {
			$this->path = Config::REPOSITORY_PATH;
			$this->files = scandir($this->path);
		}
		
		// remove all entries which are no empl
		foreach ( $this->files as $index => $entry ) {
			if ( substr($entry, -5) != ".epml" ) unset($this->files[$index]);
		}
		
		$this->loadEPMLs();
	}

	/**
	 * Loading the epmls and counts the number of contained models
	 * Saving the infos to $epmls[nameOfTheFile] = number of contained models
	 */
	private function loadEPMLs() {
		$epmlsTemp = array();
		$correctEPMLNames = array();
		foreach ( $this->files as $file ) {
			$content_file = file_get_contents($this->path."/".$file);
			$xml = new SimpleXMLElement($content_file);
			$modelsInFile = count($xml->xpath("//epc"));
			$epmlsTemp[strtolower(substr($file, 0, -5))] = $modelsInFile;
			$correctEPMLNames[strtolower(substr($file, 0, -5))] = substr($file, 0, -5);
		}
		ksort($epmlsTemp);
		foreach ( $epmlsTemp as $key => $numModels ) {
			$this->epmls[$correctEPMLNames[$key]] = $numModels;
		}
	}
	
	public function isEmpty() {
		return count($this->epmls) == 0 ? true : false;
	}
	
}
?>