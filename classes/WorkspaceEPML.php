<?php
class WorkspaceEPML {
	
	public $sessionID;
	public $filepath;	// e.g. workspace
	public $file;		// complete filepath e.g. workspace/workspace.epml
	public $filename = "workspace.epml";
	
	public $sources = array();
	public $sourceAssignments = array();
	public $numSources = 0;
		
	public $epcs = array();
	public $numModels = 0;
	
	private $currentNodeID = 1;
	private $currentEpcID = 1;
	
	/**
	 * Constructor
	 */
	public function __construct() {	
		$this->init();
	}
		
	private function init() {
		$this->sessionID = session_id();
		$this->filepath = Config::WORKSPACE_PATH."/".$this->sessionID;
		$this->file = $this->filepath."/".$this->filename;
		
		// create session directory
		if ( !is_dir($this->filepath) ) {
			mkdir($this->filepath, 0777, true);
			chmod($this->filepath, 0777);
		} 
		
		// create/load workspace epml
		if ( file_exists($this->file) ) {
			$content_file = file_get_contents($this->file);
			$xml = new SimpleXMLElement($content_file);
			$this->numModels = count($xml->xpath("//epc"));
			
			// load meta data
			$metaData = $xml->xpath("//rmm-workspace");
			$this->currentNodeID = intval($metaData[0]["maxNodeID"]);
			$this->currentEpcID = intval($metaData[0]["maxEpcID"]);
			
			// load source assignments
			foreach ( $xml->xpath("//source-assignment") as $assignment ) {
				$this->sourceAssignments[intval($assignment["modelID"])] = (string) $assignment["source"];
				if ( !in_array((string) $assignment["source"], $this->sources) ) array_push($this->sources, (string) $assignment["source"]);
			}
			
			$this->numSources = count($this->sources);
			$this->numModels = count($this->sourceAssignments);
			
			$this->loadEPCs($xml);
		} else {
			// create workspace epml file
			$this->updateWorkspaceEPMLFile();
		}
	}
	
	private function loadEPCs($xml) {
		foreach ($xml->xpath("//epc") as $xml_epc) {
			$epc = new EPC($xml, $xml_epc["epcId"], $xml_epc["name"]);
			$this->epcs[(string) $xml_epc["epcId"]] = $epc;
		}
		ksort($this->epcs);
	}
	
	private function updateIDsInAllEPCs() {
		foreach ( $this->epcs as $index => $epc ) {
			$this->updateIDsInEPC($index);
		}
	}
	
	private function updateIDsInEPC($epcIndex) {
			$idConversion = array();
			
			$functionRebuild = array();
			foreach ( $this->epcs[$epcIndex]->functions as $id => $label ) {
				$functionRebuild[$this->currentNodeID] = $label;
				$idConversion[$id] = $this->currentNodeID;
				$this->currentNodeID++;
			}
			$this->epcs[$epcIndex]->functions = $functionRebuild;
			
			$eventRebuild = array();
			foreach ( $this->epcs[$epcIndex]->events as $id => $label ) {
				$eventRebuild[$this->currentNodeID] = $label;
				$idConversion[$id] = $this->currentNodeID;
				$this->currentNodeID++;
			}
			$this->epcs[$epcIndex]->events = $eventRebuild;
			
			$xorRebuild = array();
			foreach ( $this->epcs[$epcIndex]->xor as $id => $label ) {
				$xorRebuild[$this->currentNodeID] = $label;
				$idConversion[$id] = $this->currentNodeID;
				$this->currentNodeID++;
			}
			$this->epcs[$epcIndex]->xor = $xorRebuild;
						
			$orRebuild = array();
			foreach ( $this->epcs[$epcIndex]->or as $id => $label ) {
				$orRebuild[$this->currentNodeID] = $label;
				$idConversion[$id] = $this->currentNodeID;
				$this->currentNodeID++;
			}
			$this->epcs[$epcIndex]->or = $orRebuild;
			
			$andRebuild = array();
			foreach ( $this->epcs[$epcIndex]->and as $id => $label ) {
				$andRebuild[$this->currentNodeID] = $label;
				$idConversion[$id] = $this->currentNodeID;
				$this->currentNodeID++;
			}
			$this->epcs[$epcIndex]->and = $andRebuild;

			$edgeRebuild = array();
			foreach ( $this->epcs[$epcIndex]->edges as $edge ) {
				$keys = array_keys($edge);
				$source = $keys[0];
				$target = $edge[$source];
				$newEdge = array($idConversion[$source] => $idConversion[$target]);
				array_push($edgeRebuild, $newEdge);
			}
			$this->epcs[$epcIndex]->edges = $edgeRebuild;
	}
	
	public function getEPC($epcID) {
		if ( empty($epcID) || is_null($epcID) ) return null;
		return isset($this->epcs[$epcID]) ? $this->epcs[$epcID] : null;
	}
	
	public function addEPC(EPC $epc, $sourceFilename) {
		if ( !in_array($sourceFilename, $this->sources) ) array_push($this->sources, $sourceFilename);
		$this->sourceAssignments[$this->currentEpcID] = $sourceFilename;
		$this->epcs[$this->currentEpcID] = $epc;
		$this->epcs[$this->currentEpcID]->id = $this->currentEpcID;
		$this->updateIDsInEPC($this->currentEpcID);
		$this->currentEpcID++;
	}
	
	/**
	 * Return to a source given in $this->sources all assigned EPC IDs
	 * 
	 * @param string $source
	 * @return array of EpcIDs
	 */
	public function getModelsFromSource($source) {
		$assignedEpcIDs = array();
		foreach ( $this->sourceAssignments as $epcID => $assignedSource ) {
			if ( $source == $assignedSource ) array_push($assignedEpcIDs, $epcID);
		}
		return $assignedEpcIDs;
	}
	
	public function updateWorkspaceEPMLFile() {
		$content =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$content .= "<epml:epml xmlns:epml=\"http://www.epml.de\"\n";
		$content .= "  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"epml_1_draft.xsd\">\n";
		$content .= "  <rmm-workspace maxNodeID=\"".$this->currentNodeID."\" maxEpcID=\"".$this->currentEpcID."\">\n";
		foreach ( $this->sourceAssignments as $modelID => $source ) {
			$content .= "     <source-assignment modelID=\"".$modelID."\" source=\"".$source."\" />\n";
		}
		$content .= "  </rmm-workspace>\n";
		
		foreach ( $this->sources as $source ) {			
			$content .= "  <directory name=\"".$source."\">\n";
			
			$assignedEpcIDs = $this->getModelsFromSource($source);
			foreach ( $assignedEpcIDs as $epcID ) {
				
				$content .= "    <epc epcId=\"".$epcID."\" name=\"".$this->epcs[$epcID]->getEPCName()."\">\n";
				
				foreach ( $this->epcs[$epcID]->functions as $id => $label ) {
					$content .= "      <function id=\"".$id."\">\n";
					$content .= "        <name>".utf8_encode($this->epcs[$epcID]->convertIllegalChars($label))."</name>\n";
					$content .= "      </function>\n";
				}
				
				foreach ( $this->epcs[$epcID]->events as $id => $label ) {
					$content .= "      <event id=\"".$id."\">\n";
					$content .= "        <name>".utf8_encode($this->epcs[$epcID]->convertIllegalChars($label))."</name>\n";
					$content .= "      </event>\n";
				}
				
				foreach ( $this->epcs[$epcID]->getAllConnectors() as $id => $label ) {
					$content .= "      <".$label." id=\"".$id."\" />\n";
				}
				
				foreach ( $this->epcs[$epcID]->edges as $index => $edge ) {
					$keys = array_keys($edge);
					$source = $keys[0];
					$target = $edge[$source];
				
					$content .= "      <arc id=\"".$this->currentNodeID."\">\n";
					$content .= "        <flow source=\"".$source."\" target=\"".$target."\" />\n";
					$content .= "      </arc>\n";
				
					$this->currentNodeID++;
					if ( $this->currentNodeID == 21 ) echo "NOW - generate";
				}
				$content .= "    </epc>\n";				
			}
			$content .= "  </directory>\n";
		}

		$content .= "</epml:epml>";
		
		$handler = fopen($this->file, "w");
		fwrite($handler, $content);
		fclose($handler);
		chmod($this->file, 0777);
	}
	
}
?>