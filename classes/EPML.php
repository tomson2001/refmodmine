<?php
class EPML {
	
	public $numModels;
	public $numDirectories = 0;
	
	public $filename;
	public $filepath;
	public $epmlName;
	
	public $epcs = array();
	public $directories = array();
	
	/**
	 * Constructor
	 * 
	 * @param string $filename The filename of the epml-file with its path. If 
	 * 						   only the name of the file without .epml and without
	 * 						   the path is given, it is tried to load it from the repository
	 * 		  string $source   repo (path in the Config.php) or userRepo ("files" directory in a session subdir) 
	 */
	public function __construct($filename, $source = "repo") {
		
		// Detect path
		$repoPath = Config::REPOSITORY_PATH;
		if ( $source == "userRepo" ) {
			$sessionID = session_id();
			$repoPath = Config::FILES_PATH."/".$sessionID;
		}
		
		$this->filename = ( substr($filename, -5) == ".epml" ) ? $filename : $repoPath."/".$filename.".epml";
		$this->epmlName = str_replace($repoPath."/", "", $this->filename);
		$this->epmlName = substr($this->epmlName, 0, -5);
		$this->filepath = str_replace($this->epmlName.".epml", "", $this->filename);
		if ( file_exists($this->filename) ) {
			$content_file = file_get_contents($this->filename);
			$xml = new SimpleXMLElement($content_file);
			$this->numModels = count($xml->xpath("//epc"));
			$this->loadEPCs($xml);
		}
	}
	
	private function loadEPCs($xml) {
		foreach ($xml->xpath("//epc") as $xml_epc) {
			$epc = new EPC($xml, $xml_epc["epcId"], $xml_epc["name"]);
			$this->epcs[$epc->modelPath] = $epc;
			if ( !array_key_exists($epc->modelPathOnly, $this->directories) ) $this->directories[$epc->modelPathOnly] = array();
			array_push($this->directories[$epc->modelPathOnly], $epc->modelPath);
		}
		ksort($this->epcs);
		$this->numDirectories = count($this->directories);
	}
	
}
?>