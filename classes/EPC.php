<?php
class EPC {
	
	public $name;
	private $xml;
	
	public $functions = array();
	public $events = array();
	public $edges = array();
	public $xor = array();
	public $or = array();
	public $and = array();
	
	/**
	 * SimpleXMLElement aus einer EPML-File
	 * 
	 * @param SimpleXMLElement $epc
	 */
	public function __construct($xml, $modelID, $format="epml") {
		$this->xml = $xml;
		$this->name = $modelID;
		if ( $format == "epml" ) {
			$this->loadEPML($xml, $modelID);
		}
	}
	
	/**
	 * Laedt eine EPK aus einer EPML-Datei
	 * 
	 * @param SimpleXML $xml
	 * @param mixed $modelID
	 */
	private function loadEPML($xml, $modelID) {
		// Funktionen laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/function") as $function) {
			$this->functions[utf8_decode((string) $function["id"])] = rtrim(ltrim(utf8_decode($function->name)));
		}
		
		// Ereignisse laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/event") as $event) {
			$this->events[utf8_decode((string) $event["id"])] = rtrim(ltrim(utf8_decode($event->name)));
		}
		
		// XOR laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/xor") as $xor) {
			$this->xor[utf8_decode((string) $xor["id"])] = "xor";
		}
		
		// OR laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/or") as $or) {
			$this->or[utf8_decode((string) $or["id"])] = "or";
		}
		
		// AND laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/and") as $and) {
			$this->and[utf8_decode((string) $and["id"])] = "and";
		}
		
		// Kanten laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/arc") as $edge) {
			$flow = $edge->flow;
			$edge = array((string) $flow['source'] => (string) $flow['target']);
			array_push($this->edges, $edge);
		}
	}
	
	/**
	 * Ermittelt den Typ des Knotens mit der ID $nodeID.
	 * Falls der Knoten nicht existiert wird false zurueckgegeben
	 * 
	 * @param mixed $nodeID
	 * @return string|boolean
	 */
	public function getType($nodeID) {
		if ( array_key_exists($nodeID, $this->functions) ) {
			return "function";
		} elseif ( array_key_exists($nodeID, $this->events) ) {
			return "event";
		} elseif ( array_key_exists($nodeID, $this->xor) ) {
			return "xor";
		} elseif ( array_key_exists($nodeID, $this->or) ) {
			return "or";
		} elseif ( array_key_exists($nodeID, $this->and) ) {
			return "and";
		} else {
			return false;
		}
	}
	
	/**
	 * Gibt die ID des oder der Nachfolgerknoten zurueck
	 * 
	 * @param mixed $nodeID
	 * @return array of NodeIDs:
	 */
	public function getSuccessor($nodeID) {
		$successors = array();
		foreach ( $this->edges as $edge ) {
			if ( array_key_exists($nodeID, $edge) ) {
				array_push($successors, $edge[$nodeID]);
			}
		}
		return $successors;
	}
	
	/**
	 * Gibt die ID des oder der Vorgaengerknoten zurueck
	 * 
	 * @param mixed $nodeID
	 * @return array of NodeIDs:
	 */
	public function getPredecessor($nodeID) {
		$predecessor = array();
		foreach ( $this->edges as $edge ) {
			$flipped = array_flip($edge);
			if ( array_key_exists($nodeID, $flipped) ) {
				array_push($predecessor, $flipped[$nodeID]);
			}
		}
		return $predecessor;
	}
	
	/**
	 * Prueft, ob es sich bei einem Knoten um einen Konnektor handelt
	 * 
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isConnector($nodeID) {
		$connectors = array("xor", "or", "and");
		if ( in_array($this->getType($nodeID), $connectors) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Gibt den Knoten mit dem Identifier $noteID aus der EPK zurueck
	 * 
	 * @param mixed $nodeID
	 * @return multitype:|boolean
	 */
	public function getNodeLabel($nodeID) {
		if ( array_key_exists($nodeID, $this->functions) ) {
			return $this->functions[$nodeID];
		} elseif ( array_key_exists($nodeID, $this->events) ) {
			return $this->events[$nodeID];
		} elseif ( array_key_exists($nodeID, $this->xor) ) {
			return "xor";
		} elseif ( array_key_exists($nodeID, $this->xor) ) {
			return "or";
		} elseif ( array_key_exists($nodeID, $this->and) ) {
			return "and";
		} else {
			return false;
		}
	}
	
	/**
	 * Gibt alle Konnektorknoten zurueck
	 * 
	 * @return multitype:
	 */
	public function getAllConnectors() {
		return $this->xor + $this->or + $this->and;
	}
	
	/**
	 * Prueft ob eine Kante existiert
	 * 
	 * @param mixed $sourceNodeID
	 * @param mixed $targetNodeID
	 * @return boolean
	 */
	public function edgeExists($sourceNodeID, $targetNodeID) {
		foreach ( $this->edges as $index => $edge ) {
			if ( array_key_exists($sourceNodeID, $edge) && $edge[$sourceNodeID] == $targetNodeID ) {
				return $index;
			}
		}
		return false;
	}
	
	public function assignFunctionMapping(IMapping $mapping) {
		foreach ($this->functions as $id => $label) {
			$mappedFunctionID = $mapping->mappingExistsTo($id);
			if ( $mappedFunctionID && $mappedFunctionID != $id ) {
				
				// FunctionID in $functions aendern
				$this->functions[$mappedFunctionID] = $label;
				unset($this->functions[$id]);
				
				// FunctionID in $edges aendern
				foreach ( $this->edges as $index => $edge ) {
					foreach ( $edge as $sourceNodeID => $targetNodeID ) {
						if ( $id == $sourceNodeID ) {
							$newEdge = array($mappedFunctionID => $targetNodeID);
							$this->edges[$index] = $newEdge;
						}
						if ( $id == $targetNodeID) {
							$newEdge = array($sourceNodeID => $mappedFunctionID);
							$this->edges[$index] = $newEdge;
						}
					}
				}
			}
		}
	}

}
?>