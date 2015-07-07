<?php
class PetriNet {
	
	public $id;
	public $name;
	private $xml;
	
	public $places = array();
	public $transitions = array();
	public $arcs = array();
	
	/**
	 * SimpleXMLElement aus einer PNML-File
	 *
	 * @param SimpleXMLElement $epc
	 */
	public function __construct($xml, $modelID, $modelName, $format="pnml") {
		$this->xml = $xml;
		$this->id = $modelID;
		$this->name = $modelName;
		if ( $format == "pnml" ) {
			$this->loadPNML($xml, $modelID);
		}
		unset($this->xml);
	}
	
	/**
	 * Loads a petri net from a pnml file
	 *
	 * @param SimpleXML $xml
	 * @param mixed $modelID
	 */
	private function loadPNML($xml, $modelID) {
		// load places
		foreach ($xml->xpath("//net[@id='".$modelID."']/place") as $place) {
			$this->places[(string) $place["id"]] = rtrim(ltrim((string) $place->name->text));
		}
	
		// load transitions
		foreach ($xml->xpath("//net[@id='".$modelID."']/transition") as $transition) {
			$this->transitions[(string) $transition["id"]] = rtrim(ltrim((string) $transition->name->text));
		}
	
		// load arcs
		foreach ($xml->xpath("//net[@id='".$modelID."']/arc") as $arc) {
			$this->arcs[(string) $arc["id"]] = array("source" => (string) $arc["source"], "target" => (string) $arc["target"]);
		}
	}
	
	public function transform2EPC() {
		$epc = new EPC(null, $this->id, $this->name, null);
		$epc->functions = $this->transitions;
		$epc->events = $this->places;
		foreach ( $this->arcs as $id => $arc ) {
			$epc->edges[$id] = array($arc["source"] => $arc["target"]);
		}
		
		/**
		 * Handling connectors
		 */
		
		foreach ( $this->transitions as $id => $label ) {
			// Add AND-Splits
			$successors = $this->getSuccessors($id);
			$numSucc = count($successors);
			if ( $numSucc > 1 ) {
				$connectorID = md5("andsplit_".$id."_".$label);
				$epc->and[$connectorID] = "and";
				// add edge from transition to connector
				array_push($epc->edges, array($id => $connectorID));
				foreach ( $successors as $succID ) {
					// add edges from connector to successors
					array_push($epc->edges, array($connectorID => $succID));
					// remove original edges
					$epc->deleteEdge($id, $succID);
				}
			}
			
			// Add AND-Joins
			$predecessors = $this->getPredecessors($id);
			$numPred = count($predecessors);
			if ( $numPred > 1 ) {
				$connectorID = md5("andjoin_".$id."_".$label);
				$epc->and[$connectorID] = "and";
				// add edge from connector to transition
				array_push($epc->edges, array($connectorID => $id));
				foreach ( $predecessors as $predID ) {
					// add edges from predecessors to connector
					array_push($epc->edges, array($predID => $connectorID));
					// remove original edges
					$epc->deleteEdge($predID, $id);
				}
			}
			
		}
		
		foreach ( $this->places as $id => $label ) {
			// Add XOR-Splits
			$successors = $this->getSuccessors($id);
			$numSucc = count($successors);
			if ( $numSucc > 1 ) {
				$connectorID = md5("xorsplit_".$id."_".$label);
				$epc->xor[$connectorID] = "xor";
				// add edge from place to connector
				array_push($epc->edges, array($id => $connectorID));
				foreach ( $successors as $succID ) {
					// add edges from connector to successors
					array_push($epc->edges, array($connectorID => $succID));
					// remove original edges
					$epc->deleteEdge($id, $succID);
				}
			}
				
			// Add XOR-Joins
			$predecessors = $this->getPredecessors($id);
			$numPred = count($predecessors);
			if ( $numPred > 1 ) {
				$connectorID = md5("xorjoin_".$id."_".$label);
				$epc->and[$connectorID] = "xor";
				// add edge from connector to place
				array_push($epc->edges, array($connectorID => $id));
				foreach ( $predecessors as $predID ) {
					// add edges from predecessors to connector
					array_push($epc->edges, array($predID => $connectorID));
					// remove original edges
					$epc->deleteEdge($predID, $id);
				}
			}
		}
		
		return $epc;
	}
	
	public function getSuccessors($nodeID) {
		$successors = array();
		foreach ( $this->arcs as $id => $arc ) {
			if ( $arc["source"] == $nodeID ) array_push($successors, $arc["target"]);
		}
		return $successors;
	}
	
	public function getPredecessors($nodeID) {
		$predecessors = array();
		foreach ( $this->arcs as $id => $arc ) {
			if ( $arc["target"] == $nodeID ) array_push($predecessors, $arc["source"]);
		}
		return $predecessors;
	}
	
}
?>