<?php
class EPC {

	public $id;
	public $name;
	private $xml;
	public $modelPath;
	public $modelPathOnly;

	// basic constructs
	public $functions = array();
	public $events = array();
	public $edges = array();
	public $xor = array();
	public $or = array();
	public $and = array();
	
	// further constructs
	public $orgUnits = array();
	public $functionOrgUnitAssignments = array();

	public $traces = array();

	public $idConversion = array();

	public $autoCorrected = false;
	public $correctSplitJoinConnectors = false;
	public $correctSenselessConnectors = false;
	public $correctStandAndEndFunctions = false;

	public $warnings = array();
	
	public $spellingCorrections = array();

	public $internalID;
	public $internalReservedID = 1;
	
	public $metrics = array();

	/**
	 * SimpleXMLElement aus einer EPML-File
	 *
	 * @param SimpleXMLElement $epc
	 */
	public function __construct($xml, $modelID, $modelName, $format="epml") {
		$this->xml = $xml;
		$this->id = $this->convertIllegalChars($modelID);
		$this->name = $this->convertIllegalChars($modelName);
		if ( $format == "epml" ) {
			$this->loadEPML($xml, $modelID);
			$this->loadModelPath($xml, $modelID);
		}
		$this->cleanLabels();
		unset($this->xml);
		$this->internalID = $this->name."_".$this->id."_".rand();
		//print_r($this);
	}
	
	public function loadModelPath($xml, $modelID) {
		$path = $this->name;
		
		$epc_xml = null;
		foreach ( $xml->xpath("//epc") as $xml_epc ) {
			$epc_xml = isset($xml_epc["epcId"]) ? $xml->xpath("//epc[@epcId='".$modelID."']") : $xml->xpath("//epc[@EpcId='".$modelID."']");
		}
			
		$parent = $epc_xml[0]->xpath("parent::*");
		while ( !empty($parent) ) {
			$path = $parent[0]["name"]."/".$path;
			$parent = $parent[0]->xpath("parent::*");
		}
		$this->modelPath = $path;
		$this->modelPathOnly = str_replace("/".$this->name, "", $this->modelPath);
	}

	/**
	 * Laedt eine EPK aus einer EPML-Datei
	 *
	 * @param SimpleXML $xml
	 * @param mixed $modelID
	 */
	private function loadEPML($xml, $modelID, $useOriginalIDs = true) {
		
		$xPathEPCPart = null;
		foreach ( $xml->xpath("//epc") as $xml_epc ) {
			$xPathEPCPart = isset($xml_epc["epcId"]) ? "//epc[@epcId='".$modelID."']" : "//epc[@EpcId='".$modelID."']";
		}
		
		// Funktionen laden
		foreach ($xml->xpath($xPathEPCPart."/function") as $function) {
			$index = $useOriginalIDs ? (string) $function["id"] : $this->getNextID();
			$this->functions[$index] = rtrim(ltrim($this->convertIllegalChars($function->name)));
			$this->idConversion[(string) $function["id"]] = $index;
		}

		// Ereignisse laden
		foreach ($xml->xpath($xPathEPCPart."/event") as $event) {
			$index = $useOriginalIDs ? (string) $event["id"] : $this->getNextID();
			$this->events[$index] = rtrim(ltrim($this->convertIllegalChars($event->name)));
			$this->idConversion[(string) $event["id"]] = $index;
		}

		// XOR laden
		foreach ($xml->xpath($xPathEPCPart."/xor") as $xor) {
			$index = $useOriginalIDs ? (string) $xor["id"] : $this->getNextID();
			$this->xor[$index] = "xor";
			$this->idConversion[(string) $xor["id"]] = $index;
		}

		// OR laden
		foreach ($xml->xpath($xPathEPCPart."/or") as $or) {
			$index = $useOriginalIDs ? (string) $or["id"] : $this->getNextID();
			$this->or[$index] = "or";
			$this->idConversion[(string) $or["id"]] = $index;
		}

		// AND laden
		foreach ($xml->xpath($xPathEPCPart."/and") as $and) {
			$index = $useOriginalIDs ? (string) $and["id"] : $this->getNextID();
			$this->and[$index] = "and";
			$this->idConversion[(string) $and["id"]] = $index;
		}
		
		// OrgEinheiten laden
		foreach ($xml->xpath($xPathEPCPart."/role") as $role) {
			$index = $useOriginalIDs ? (string) $role["id"] : $this->getNextID();
			$this->orgUnits[$index] = rtrim(ltrim($this->convertIllegalChars($role->name)));
			$this->idConversion[(string) $role["id"]] = $index;
		}

		// Kanten laden
		foreach ($xml->xpath($xPathEPCPart."/arc") as $edge) {
			$flow = $edge->flow;
			$relation = $edge->relation;
			if ( isset($flow['source']) && isset($flow['target']) ) {
				if ( !isset($this->idConversion[(string) $flow['source']]) || !isset($this->idConversion[(string) $flow['target']]) ) continue;
				$sourceIndex = $this->idConversion[(string) $flow['source']];
				$targetIndex = $this->idConversion[(string) $flow['target']];
				$edge = array($sourceIndex => $targetIndex);
				array_push($this->edges, $edge);
			} elseif ( isset($relation['source']) && isset($relation['target']) ) {
				$this->functionOrgUnitAssignments[$this->idConversion[(string) $relation['target']]] = $this->idConversion[(string) $relation['source']];
			}
		}
	}

	public function cleanLabels() {
		foreach ($this->functions as $id => $label) {
			$this->functions[$id] = trim(str_replace("\\n", " ", $label));
		}

		foreach ($this->events as $id => $label) {
			$this->events[$id] = trim(str_replace("\\n", " ", $label));
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
		$nodeID = (string) $nodeID;
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
	
	public function getLabel($nodeID) {
		$type = $this->getType($nodeID);
		switch ( $type ) {
			case "function": return $this->functions[$nodeID];
			case "event": 	 return $this->events[$nodeID];
			default: return $type;
		}
	}
	
	public function getOrganizationUnit($nodeID) {
		return isset($this->functionOrgUnitAssignments[$nodeID]) ? $this->functionOrgUnitAssignments[$nodeID] : null;
	}
	
	public function isFunction($nodeID) {
		$nodeID = (string) $nodeID;
		return array_key_exists($nodeID, $this->functions);
	}
	
	public function getNumDifferentFunctionLabels() {
		$differentLabels = array();
		foreach ( $this->functions as $nodeID => $label ) {
			if ( in_array($label, $differentLabels) ) continue;
			array_push($differentLabels, $label);
		}
		return count($differentLabels);
	}

	public function isEvent($nodeID) {
		$nodeID = (string) $nodeID;
		return array_key_exists($nodeID, $this->events);
	}

	/**
	 * Gibt die ID des oder der Nachfolgerknoten zurueck
	 *
	 * @param mixed $nodeID
	 * @return array of NodeIDs:
	 */
	public function getSuccessor($nodeID) {
		$nodeID = (string) $nodeID;
		$successors = array();
		foreach ( $this->edges as $edge ) {
			if ( array_key_exists($nodeID, $edge) ) {
				array_push($successors, $edge[$nodeID]);
			}
		}
		return $successors;
	}
	
	/** 
	 * Returns an array of the successors IDs, where connectors are skipped. This is 
	 * especially for the determinatioin of an alternating function-event-style.
	 * 
	 * @param int $nodeID
	 * @return array
	 */
	public function getSuccessorsSkipConnectors($nodeID) {
		$successors = $this->getSuccessor($nodeID);
		
		// Recursion if one of the successors is a connector
		foreach ( $successors as $index => $succID ) {
			if ( $this->isConnector($succID) ) {
				unset($successors[$index]);
				$succSuccs = $this->getSuccessorsSkipConnectors($succID);
				foreach ( $succSuccs as $succSuccID ) {
					array_push($successors, $succSuccID);
				}
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
		$nodeID = (string) $nodeID;
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
		$nodeID = (string) $nodeID;
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
		$nodeID = (string) $nodeID;
		if ( array_key_exists($nodeID, $this->functions) ) {
			return $this->functions[$nodeID];
		} elseif ( array_key_exists($nodeID, $this->events) ) {
			return $this->events[$nodeID];
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

	/**
	 * Die IDs der Funktionen in der EPK werden auf die IDs einer gemappten EPK gesetzt
	 * Im Mapping ist diese EPK die zweite und die gemappte die erste
	 *
	 * @param IMapping $mapping
	 */
	public function assignFunctionMapping(IMapping $mapping) {
		// Func-IDs in der EPK aendern
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

		// Func-IDs in den Traces aendern
		if ( is_array($this->traces) ) {
			foreach ( $this->traces as $traceIndex => $trace ) {
				foreach ( $trace as $funcIndex => $funcID ) {
					$mappedFunctionID = $mapping->mappingExistsTo($funcID);
					if ( $mappedFunctionID && $mappedFunctionID != $funcID ) {
							
						// FunctionID in trace aendern
						$this->traces[$traceIndex][$funcIndex] = $mappedFunctionID;
							
					}
				}
			}
		}
	}
	
	/**
	 * Die IDs der Funktionen in der EPK werden auf die IDs einer gemappten EPK gesetzt
	 * Im Mapping ist diese EPK die erste und die gemappte die zweite
	 *
	 * @param IMapping $mapping
	 */
	public function assignFunctionMapping2(IMapping $mapping) {
		// Func-IDs in der EPK aendern
		foreach ($this->functions as $id => $label) {
			$mappedFunctionID = $mapping->mappingExistsFrom($id);
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
	
		// Func-IDs in den Traces aendern
		if ( is_array($this->traces) ) {
			foreach ( $this->traces as $traceIndex => $trace ) {
				foreach ( $trace as $funcIndex => $funcID ) {
					$mappedFunctionID = $mapping->mappingExistsFrom($funcID);
					if ( $mappedFunctionID && $mappedFunctionID != $funcID ) {
						//print ("\nreplace ".$funcID." by ".$mappedFunctionID);
						// FunctionID in trace aendern
						$this->traces[$traceIndex][$funcIndex] = $mappedFunctionID;
							
					}
				}
			}
		}
	}

	/**
	 * Prueft, ob innerhalb der EPK gleichbeschriftet Knoten existieren.
	 * Also insbesondere ob zwei Ereignisse mit gleichem Label oder
	 * zwei Funktion mit gleichem Label existieren
	 */
	public function hasEqualLabelledNodes() {
		return $this->hasEqualLabelledFunctions() || $this->hasEqualLabelledEvents();
	}

	/**
	 * Prueft, ob die EPK gleich beschriftete Funktionen enthaelt
	 *
	 * @return boolean
	 */
	public function hasEqualLabelledFunctions() {
		$checkedFunctions = array();
		foreach ( $this->functions as $label ) {
			if ( in_array($label, $checkedFunctions) ) {
				return true;
			} else {
				array_push($checkedFunctions, $label);
			}
		}
		return false;
	}

	/**
	 * Prueft, ob die EPK gleich beschriftete Ereignisse enthaelt
	 *
	 * @return boolean
	 */
	public function hasEqualLabelledEvents() {
		$checkedEvents = array();
		foreach ( $this->events as $label ) {
			if ( in_array($label, $checkedEvents) ) {
				return true;
			} else {
				array_push($checkedEvents, $label);
			}
		}
		return false;
	}

	public function deleteEdge($sourceNodeID, $targetNodeID) {
		$sourceNodeID = (string) $sourceNodeID;
		$targetNodeID = (string) $targetNodeID;
		$edgeIndex = null;
		foreach ( $this->edges as $index => $edge ) {
			foreach ( $edge as $sourceID => $targetID ) {
				if ( $sourceID == $sourceNodeID && $targetID == $targetNodeID ) {
					$edgeIndex = $index;
					break;
				}
			}
			if ( !is_null($edgeIndex) ) break;
		}
		//print("delete edge ".$edgeIndex."(".$sourceNodeID.", ".$targetNodeID.")\n");
		unset($this->edges[$edgeIndex]);
	}

	public function deleteEvent($eventID) {
		$eventID = (string) $eventID;
		foreach ( $this->edges as $index => $edge ) {
			// Event ist Quelle
			if ( array_key_exists($eventID, $edge) ) {
				$target = $edge[$eventID];
				$source = $this->getPredecessor($eventID);
				if ( !empty($source) ) {
					$source = $source[0];
					$newEdge = array($source => $target);
					$this->edges[$index] = $newEdge;
				} else {
					unset($this->edges[$index]);
				}
			}
			// Event ist Ziel
			$flipped = array_flip($edge);
			if ( array_key_exists($eventID, $flipped) ) {
				$source = $flipped[$eventID];
				$target = $this->getSuccessor($eventID);
				if ( !empty($target) ) {
					$target = $target[0];
					$newEdge = array($source => $target);
					$this->edges[$index] = $newEdge;
				} else {
					unset($this->edges[$index]);
				}
			}
		}
		unset($this->events[$eventID]);
	}

	public function deleteFunction($funcID) {
		$funcID = (string) $funcID;
		foreach ( $this->edges as $index => $edge ) {
			// Function ist Quelle
			if ( array_key_exists($funcID, $edge) ) {
				$target = $edge[$funcID];
				$source = $this->getPredecessor($funcID);
				if ( !empty($source) ) {
					$source = $source[0];
					$newEdge = array($source => $target);
					$this->edges[$index] = $newEdge;
				} else {
					unset($this->edges[$index]);
				}
			}
			// Function ist Ziel
			$flipped = array_flip($edge);
			if ( array_key_exists($funcID, $flipped) ) {
				$source = $flipped[$funcID];
				$target = $this->getSuccessor($funcID);
				if ( !empty($target) ) {
					$target = $target[0];
					$newEdge = array($source => $target);
					$this->edges[$index] = $newEdge;
				} else {
					unset($this->edges[$index]);
				}
			}
		}
		unset($this->functions[$funcID]);
	}

	public function addEdge($sourceNodeID, $targetNodeID) {
		$sourceNodeID = (string) $sourceNodeID;
		$targetNodeID = (string) $targetNodeID;
		$edge = array($sourceNodeID => $targetNodeID);
		array_push($this->edges, $edge);
	}

	public function getAllNodes() {
		return $this->functions + $this->events + $this->or + $this->xor + $this->and;
	}

	/**
	 * Ermittelt die Senke der EPK. Gibt konkret den ersten Knoten zurueck
	 * der gefunden werden kann, zu dem keine ausgehende Kante existiert.
	 *
	 * @return array(nodeID => type)
	 */
	public function getLastNode() {
		$nodes = $this->getAllNodes();
		foreach ( $nodes as $id => $label ) {
			$successors = $this->getSuccessor($id);
			if ( empty($successors) ) return $id;
		}
		return null;
	}

	public function isEndNode($nodeID) {
		$endNodes = $this->getAllEndNodes();
		if ( array_key_exists($nodeID, $endNodes) ) return true;
		return false;
	}
	
	public function isStartNode($nodeID) {
		$startNodes = $this->getAllStartNodes();
		if ( array_key_exists($nodeID, $startNodes) ) return true;
		return false;
	}

	/**
	 * Gibt alle Endknoten zurueck
	 * @return array
	 */
	public function getAllEndNodes() {
		$endNodes = array();
		$nodes = $this->getAllNodes();
		foreach ( $nodes as $id => $label ) {
			$successors = $this->getSuccessor($id);
			if ( empty($successors) ) $endNodes[$id] = $label;
		}
		return $endNodes;
	}

	/**
	 * Ermittelt die Quelle der EPK. Gibt konkret den ersten Knoten zurueck
	 * der gefunden werden kann, zu dem keine eingehende Kante existiert.
	 *
	 * @return array(nodeID => type)
	 */
	public function getFirstNode() {
		$nodes = $this->getAllNodes();
		foreach ( $nodes as $id => $label ) {
			$predecessors = $this->getPredecessor($id);
			if ( empty($predecessors) ) return $id;
		}
		return null;
	}

	/**
	 * Gibt alle Startknoten zurueck
	 * @return array
	 */
	public function getAllStartNodes() {
		$startNodes = array();
		$nodes = $this->getAllNodes();
		foreach ( $nodes as $id => $label ) {
			$predecessors = $this->getPredecessor($id);
			if ( empty($predecessors) ) $startNodes[$id] = $label;
		}
		return $startNodes;
	}

	/**
	 * Ermittelt eine nicht in der EPK belegte KnotenID
	 * @return int
	 */
// 	public function getFreeNodeID() {
// 		$allNodes = $this->getAllNodes();
// 		return max(array_keys($allNodes)) + 1;
// 	}

	/**
	 * Prueft die EPK hinsichtlich der AND-soundness.
	 *
	 * @return boolean - true = soundness ok

	 public function checkANDSoundness() {
		$andSplits = $this->getAllSplitConnectors($this->and);
		$andJoins = $this->getAllJoinConnectors($this->and);

		foreach ( $andSplits as $id => $type ) {
		if ( $this->isValidANDSplit($id) === false ) {
		array_push($this->warnings, "EPC is not AND-sound.");
		return false;
		}
		}
		return true;
		}
		*/

	/**
	 * Ermittelt alle Split-Konnektoren
	 *
	 * @param array $connectors $or, $and oder $xor
	 * @return array
	 */
	public function getAllSplitConnectors(array $connectors) {
		$splitConnectors = array();
		foreach ( $connectors as $id => $type ) {
			$successors = $this->getSuccessor($id);
			if ( count($successors) > 1 ) $splitConnectors[$id] = $type;
		}
		return $splitConnectors;
	}

	/**
	 * Ermittelt alle Join-Konnektoren
	 *
	 * @param array $connectors $or, $and oder $xor
	 * @return array
	 */
	public function getAllJoinConnectors(array $connectors) {
		$joinConnectors = array();
		foreach ( $connectors as $id => $type ) {
			$predecessors = $this->getPredecessor($id);
			if ( count($predecessors) > 1 ) {
				$joinConnectors[$id] = $type;
			}
		}
		return $joinConnectors;
	}

	public function isJoin($nodeID) {
		$nodeID = (string) $nodeID;
		$predecessors = $this->getPredecessor($nodeID);
		return count($predecessors) > 1;
	}

	public function isSplit($nodeID) {
		$nodeID = (string) $nodeID;
		$successors = $this->getSuccessor($nodeID);
		return count($successors) > 1;
	}

	/**
	 * Prueft, ob es sich bei einem Knoten um einen AND-Join handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isANDJoin($nodeID) {
		$nodeID = (string) $nodeID;
		$joinConnectors = $this->getAllJoinConnectors($this->and);
		return array_key_exists($nodeID, $joinConnectors);
	}

	/**
	 * Prueft, ob es sich bei einem Knoten um einen XOR-Join handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isXORJoin($nodeID) {
		$nodeID = (string) $nodeID;
		$joinConnectors = $this->getAllJoinConnectors($this->xor);
		return array_key_exists($nodeID, $joinConnectors);
	}

	/**
	 * Prueft, ob es sich bei einem Knoten um einen OR-Join handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isORJoin($nodeID) {
		$nodeID = (string) $nodeID;
		$joinConnectors = $this->getAllJoinConnectors($this->or);
		return array_key_exists($nodeID, $joinConnectors);
	}

	public function hasORJoin() {
		foreach ( $this->or as $orID => $label ) {
			if ( $this->isORJoin($orID) ) return true;
		}
		return false;
	}

	public function hasXORJoin() {
		foreach ( $this->xor as $xorID => $label ) {
			if ( $this->isXORJoin($xorID) ) return true;
		}
		return false;
	}

	public function isOr($nodeID) {
		$nodeID = (string) $nodeID;
		return array_key_exists($nodeID, $this->or);
	}

	public function isXor($nodeID) {
		$nodeID = (string) $nodeID;
		return array_key_exists($nodeID, $this->xor);
	}

	/**
	 * Prueft, ob es sich bei einem Knoten um einen AND-Split handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isANDSplit($nodeID) {
		$nodeID = (string) $nodeID;
		$splitConnectors = $this->getAllSplitConnectors($this->and);
		return array_key_exists($nodeID, $splitConnectors);
	}

	/**
	 * Prueft, ob es sich bei einem Knoten um einen AND-Split handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isORSplit($nodeID) {
		$nodeID = (string) $nodeID;
		$splitConnectors = $this->getAllSplitConnectors($this->or);
		return array_key_exists($nodeID, $splitConnectors);
	}

	/**
	 * Prueft, ob es sich bei einem Knoten um einen XOR-Split handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isXORSplit($nodeID) {
		$nodeID = (string) $nodeID;
		$splitConnectors = $this->getAllSplitConnectors($this->xor);
		return array_key_exists($nodeID, $splitConnectors);
	}

	/**
	 * Ueberprueft, ob die in der EPK enthaltenen AND-Konnektoren valide sind
	 * D.h. Alle AND-Splits muessen den gleichen oder keinen AND-Join haben
	 *
	 * @param mixed $andSplitID

	 public function isValidANDSplit($andSplitID) {
		$successors = $this->getSuccessor($andSplitID);
		$nextValidANDJoin = null;
		$count = 0;
		foreach ( $successors as $successor ) {

		// Ist der Nachfolgeknoten ein AND-Join?
		if ( $this->isANDJoin($successor) ) {
		// Untersuchen des ersten Pfades: Falls ein AND-Join gefunden wurde, dann setzen
		if ( $count == 0 ) {
		$nextValidANDJoin = $successor;
		} else {
		// Untersuchen der weiteren Pfade
		// Wenn der gefunde AND-Join gleich dem gefundenen AND-Join der vorher untersuchten Pfade ist, dann weitermachen
		if ( $nextValidANDJoin == $successor ) {
		$count++;
		continue;
		} else {
		array_push($this->warnings, "Error in EPC (AND-soundness) near Node \"".$this->getNodeLabel($successor)."\" (".$successor.")");
		return false;
		}
		}
		} else {
		$searchResult = $this->checkSuccessorsForANDJoin($successor, 0, array($successor));
		if ( (is_null($searchResult) && is_null($nextValidANDJoin)) || $searchResult == $nextValidANDJoin ) {
		$count++;
		continue;
		} else {
		array_push($this->warnings, "Error in EPC (AND-soundness) near Node \"".$this->getNodeLabel($successor)."\" (".$successor.")");
		return false;
		}
		}

		$count++;
		}
		}
		*/

	/**
	 *
	 *
	 * @param unknown_type $nodeID
	 * @param int $blocksForANDJoin soll der nachste AND-Join genommen werden (kann sein, dass nicht, wenn z.B. AND-Split -> AND-Split -> AND-Join -> AND-Join)
	 * @param array $walkedNodes Bereits durchlaufene Knoten
	 * @return NULL|Ambigous <>

	 public function checkSuccessorsForANDJoin($nodeID, $blocksForANDJoin, $walkedNodes = array()) {
		$successors = $this->getSuccessor($nodeID);

		// Wenn keine Nachfolge existieren, dann gib null zurueck
		if ( empty($successors) ) return null;

		// Nachfolger aussuchen
		$successor = null;
		foreach ( $successors as $succ ) {
		if ( is_null($successor) && !in_array($succ, $walkedNodes) ) {
		array_push($walkedNodes, $succ);
		$successor = $succ;
		}
		}

		// Kein waelbarer Nachfolger vorhanden (error in epc)
		if ( is_null($successor) ) {
		array_push($this->warnings, "Error in EPC near Node \"".$this->getNodeLabel($nodeID)."\" (".$nodeID.")");
		return false;
		}

		// Wenn nachster Knoten ein Konnektor
		if ( $this->isConnector($successor[0]) ) {
		// wenn nachster Knoten AND-Join
		if ( $this->isANDJoin($successors[0]) ) {
		// wenn der AND-Join genommen werden kann
		if ( $blocksForANDJoin == 0 ) {
		return $successors[0];
		} else {
		return $this->checkSuccessorsForANDJoin($successors[0], $blocksForANDJoin--, $walkedNodes);
		}
		} elseif ( $this->isANDSplit($successors[0]) ) {
		// Wenn naechster Knoten ein AND-Split, dann setze einen blockiere den nachsten auftretenden AND-Join
		return $this->checkSuccessorsForANDJoin($successors[0], $blocksForANDJoin++, $walkedNodes);
		}
		}
		// weiter suchen
		return $this->checkSuccessorsForANDJoin($successors[0], $blocksForANDJoin, $walkedNodes);
		}
		*/

	/**
	 * Prueft, ob das Modell genau einen Startknoten UND genau einen Endknoten hat.array
	 *
	 * @return boolean
	 */
	public function isSESE() {
		$startNodes = $this->getAllStartNodes();
		$endNodes = $this->getAllEndNodes();
		return count($startNodes) == 1 && count($endNodes) == 1;
	}

	public function exportEPML($print_ids = false) {
		$content =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$content .= "<epml:epml xmlns:epml=\"http://www.epml.de\"\n";
		$content .= "  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"epml_1_draft.xsd\">\n";
		$epcCodePart = $this->getEPMLCodePart($print_ids);
		$content .= $epcCodePart;
		$content .= "</epml:epml>";

		$fileGenerator = new FileGenerator(trim($this->name).".epml", $content);
		$fileGenerator->setFilename(trim($this->name).".epml");
		$file = $fileGenerator->execute();
		return $file;
	}
	
	public function getEPMLCodePart($print_ids = false) {
		$content = "  <epc epcId=\"".$this->id."\" name=\"".$this->convertIllegalChars($this->name)."\">\n";
		
		$maxID = 0;
		
		foreach ( $this->functions as $id => $label ) {
			$content .= "    <function id=\"".$id."\">\n";
			$content .= "      <name>".htmlspecialchars($this->convertIllegalChars($label));
			if ( $print_ids ) $content .= " (".$id.")";
			$content .= "</name>\n";
			$content .= "    </function>\n";
			if ( $id > $maxID ) $maxID = $id+1;
		}
		
		foreach ( $this->events as $id => $label ) {
			$content .= "    <event id=\"".$id."\">\n";
			$content .= "      <name>".htmlspecialchars($this->convertIllegalChars($label));
			if ( $print_ids ) $content .= " (".$id.")";
			$content .= "</name>\n";
			$content .= "    </event>\n";
			if ( $id > $maxID ) $maxID = $id+1;
		}
		
		foreach ( $this->getAllConnectors() as $id => $label ) {
			$content .= "    <".$label." id=\"".$id."\">\n";
			$content .= "      <name/>\n";
			$content .= "    </".$label.">\n";
			if ( $id > $maxID ) $maxID = $id+1;
		}
		
		foreach ( $this->edges as $index => $edge ) {
			$keys = array_keys($edge);
			$source = $keys[0];
			$target = $edge[$source];
		
			$content .= "    <arc id=\"".$maxID."\">\n";
			$content .= "      <flow source=\"".$source."\" target=\"".$target."\" />\n";
			$content .= "    </arc>\n";
		
			$maxID++;
		}
		
		foreach ( $this->orgUnits as $id => $label ) {
			$content .= "    <role id=\"".$id."\" optional=\"false\">\n";
			$content .= "      <name>".htmlspecialchars($this->convertIllegalChars($label));
			if ( $print_ids ) $content .= " (".$id.")";
			$content .= "</name>\n";
			$content .= "    </role>\n";
			if ( $id > $maxID ) $maxID = $id+1;
		}
		
		foreach ( $this->functionOrgUnitAssignments as $funcID => $roleID ) {	
			$content .= "    <arc id=\"".$maxID."\">\n";
			$content .= "      <relation source=\"".$roleID."\" target=\"".$funcID."\" type=\"role\"/>\n";
			$content .= "    </arc>\n";
		
			$maxID++;
		}
		
		$content .= "  </epc>\n";
		return $content;
	}

	public static function convertIllegalChars($string) {
// 		$string = str_replace("Ä", "Ae", $string);
// 		$string = str_replace("ä", "ae", $string);
// 		$string = str_replace("Ö", "Oe", $string);
// 		$string = str_replace("ö", "oe", $string);
// 		$string = str_replace("Ü", "Ue", $string);
// 		$string = str_replace("ü", "ue", $string);
// 		$string = str_replace("ß", "ss", $string);
 		$string = str_replace("\r\n", " ", $string);
 		$string = str_replace("\r", " ", $string);
 		$string = str_replace("\n", " ", $string);
		return $string;
	}

	/**
	 * Korrigiert Konnektoren, die mehrere eingehende UND mehrere ausgehende Kante haben,
	 * indem dafuer zwei Konnektoren erzeugt werden. Ein Split- und ein Join-Konnektor
	 */
	public function correctSplitJoinConnectors() {
		// XOR
		foreach ( $this->xor as $id => $label ) {
			$predecessors = $this->getPredecessor($id);
			$successors = $this->getSuccessor($id);
			if ( count($predecessors) > 1 && count($successors) > 1 ) {

				// Neuen Konnektor erstellen
				$newConnID = $this->getNextID();
				$this->xor[$newConnID] = "xor";
				$this->idConversion[$newConnID] = "REFMOD-AUTOGEN";

				// an den neuen Konnektor die ausgehenden Kanten anschliessen und vom alten abschliessen
				foreach ( $successors as $successor ) {
					$this->addEdge($newConnID, $successor);
					$this->deleteEdge($id, $successor);
				}

				// Kante zwischen altem und neuen Konnektor ziehen
				$this->addEdge($id, $newConnID);

				$this->correctSplitJoinConnectors = true;
			}
		}
		// OR
		foreach ( $this->or as $id => $label ) {
			$predecessors = $this->getPredecessor($id);
			$successors = $this->getSuccessor($id);
			if ( count($predecessors) > 1 && count($successors) > 1 ) {

				// Neuen Konnektor erstellen
				$newConnID = $this->getNextID();
				$this->or[$newConnID] = "or";
				$this->idConversion[$newConnID] = "REFMOD-AUTOGEN";

				// an den neuen Konnektor die ausgehenden Kanten anschliessen und vom alten abschliessen
				foreach ( $successors as $successor ) {
					$this->addEdge($newConnID, $successor);
					$this->deleteEdge($id, $successor);
				}

				// Kante zwischen altem und neuen Konnektor ziehen
				$this->addEdge($id, $newConnID);

				$this->correctSplitJoinConnectors = true;
			}
		}
		// AND
		foreach ( $this->and as $id => $label ) {
			$predecessors = $this->getPredecessor($id);
			$successors = $this->getSuccessor($id);
			if ( count($predecessors) > 1 && count($successors) > 1 ) {

				// Neuen Konnektor erstellen
				$newConnID = $this->getNextID();
				$this->and[$newConnID] = "and";
				$this->idConversion[$newConnID] = "REFMOD-AUTOGEN";

				// an den neuen Konnektor die ausgehenden Kanten anschliessen und vom alten abschliessen
				foreach ( $successors as $successor ) {
					$this->addEdge($newConnID, $successor);
					$this->deleteEdge($id, $successor);
				}

				// Kante zwischen altem und neuen Konnektor ziehen
				$this->addEdge($id, $newConnID);

				$this->correctSplitJoinConnectors = true;
			}
		}
	}

	/**
	 * Entfernt sinnlose Konnektoren, die genau eine Eingangs- und Ausgangskante haben.
	 */
	public function correctSenselessConnectors() {
		// XOR
		foreach ( $this->xor as $id => $label ) {
			$predecessors = $this->getPredecessor($id);
			$successors = $this->getSuccessor($id);
			if ( count($predecessors) == 1 && count($successors) == 1 ) {
				$predecessor = $predecessors[0];
				$successor = $successors[0];

				// Kante zwischen Vorgaenger und Nachfolgerknoten erstellen
				$this->addEdge($predecessor, $successor);

				// sinnlosen Konnektor entfernen
				unset($this->xor[$id]);
				$this->deleteEdge($predecessor, $id);
				$this->deleteEdge($id, $successor);

				$this->correctSenselessConnectors = true;
			}
		}

		// OR
		foreach ( $this->or as $id => $label ) {
			$predecessors = $this->getPredecessor($id);
			$successors = $this->getSuccessor($id);
			if ( count($predecessors) == 1 && count($successors) == 1 ) {
				$predecessor = $predecessors[0];
				$successor = $successors[0];

				// Kante zwischen Vorgaenger und Nachfolgerknoten erstellen
				$this->addEdge($predecessor, $successor);

				// sinnlosen Konnektor entfernen
				unset($this->or[$id]);
				$this->deleteEdge($predecessor, $id);
				$this->deleteEdge($id, $successor);

				$this->correctSenselessConnectors = true;
			}
		}

		// AND
		foreach ( $this->and as $id => $label ) {
			$predecessors = $this->getPredecessor($id);
			$successors = $this->getSuccessor($id);
			if ( count($predecessors) == 1 && count($successors) == 1 ) {
				$predecessor = $predecessors[0];
				$successor = $successors[0];

				// Kante zwischen Vorgaenger und Nachfolgerknoten erstellen
				$this->addEdge($predecessor, $successor);

				// sinnlosen Konnektor entfernen
				unset($this->and[$id]);
				$this->deleteEdge($predecessor, $id);
				$this->deleteEdge($id, $successor);

				$this->correctSenselessConnectors = true;
			}
		}
	}

	/**
	 * Sollten Start- bzw. Endfunktionen vorkommen, wird vor, bzw. nach diese ein Ereignis gesetzt.
	 */
	public function correctEndAndStartFunctions() {
		foreach ( $this->functions as $id => $label ) {
			$predecessors = $this->getPredecessor($id);
			$successors = $this->getSuccessor($id);
			// Startfunktion
			if ( count($predecessors) == 0 ) {
				// Neues Ereignis erstellen und Kante zur Funktion ziehen
				$newEventID = $this->getNextID();
				$this->events[$newEventID] = "RefMod-AutoGen: Startevent";
				$this->addEdge($newEventID, $id);
				$this->idConversion[$newEventID] = "REFMOD-AUTOGEN";
				$this->correctStandAndEndFunctions = true;
			}
			// Endfunktion
			if ( count($successors) == 0 ) {
				// Neues Ereignis erstellen und Kante zur Funktion ziehen
				$newEventID = $this->getNextID();
				$this->events[$newEventID] = "RefMod-AutoGen: Endevent";
				$this->addEdge($id, $newEventID);
				$this->idConversion[$newEventID] = "REFMOD-AUTOGEN";
				$this->correctStandAndEndFunctions = true;
			}
				
		}
	}

	public function tryToCorrectSyntax() {
		$this->correctSenselessConnectors();
		$this->correctSplitJoinConnectors();
		$this->correctEndAndStartFunctions();
		$this->autoCorrected = true;
		$this->exportEPML();
		return $this->isSyntaxCorrect();
	}

	/**
	 * Pruefung auf Syntax-Korrektheit, wie sie im State-Explosion Papier (Thaler) definiert ist
	 */
	public function isSyntaxCorrect() {

		// Es existiert mind. ein Start und Endereignis
		if ( count($this->getAllStartNodes()) == 0 ) return false;
		if ( count($this->getAllEndNodes()) == 0 ) return false;

		// Ereignis haben max. eine eingehende und ausgehende Kante
		foreach ( $this->events as $eventID => $label ) {
			if ( count($this->getPredecessor($eventID)) > 1 ) return false;
			if ( count($this->getSuccessor($eventID)) > 1 ) return false;
		}

		// Funktion haben genau eine eingehende und ausgehende Kante
		foreach ( $this->functions as $funcID => $label ) {
			if ( count($this->getPredecessor($funcID)) != 1 ) return false;
			if ( count($this->getSuccessor($funcID)) != 1 ) return false;
		}

		// Konnektoren haben genau eine eigehende und mehrere ausgehende Kanten (Split-Konnektor) oder mehrere eingehende und genau eine ausgehende Kante (Join-Konnektor).
		$connectors = $this->getAllConnectors();
		foreach ( $connectors as $connID => $type ) {
			if ( count($this->getPredecessor($connID)) == 0 || count($this->getSuccessor($connID)) == 0 ) return false;
			if ( count($this->getPredecessor($connID)) <= 1 && count($this->getSuccessor($connID)) <= 1 ) return false;
			if ( count($this->getPredecessor($connID)) > 1 && count($this->getSuccessor($connID)) > 1 ) return false;
		}

		return true;
	}
	
	/**
	 * return all syntax error as text within an array
	 * 
	 * Types
	 * 	1	Missing start and end events
	 *  2	activities have exactly one in-coming and exactly one outgoing edge
	 *  3	events have at least one in-coming and at least one outgoing edge
	 *  4	alternating events and activities
	 *  5	labels should be unique (each label occurs exactly once)
	 *  6   connectors are either split (one imcoming, more than one outgoing edges) or join (more than one incoming, one outgoing edges) connectors 
	 * 
	 * @return array
	 */
	public function getSyntaxErrors($errorTypes=array(1,2,3,6), $warningTypes=array(4,5)) {
		$issues = array();
		
		// Es existiert mind. ein Start und Endereignis
		if ( count($this->getAllStartNodes()) == 0 ) {
			if ( in_array(1, $errorTypes) ) array_push($issues, "Error: Start event missing.");
			if ( in_array(1, $warningTypes) ) array_push($issues, "Warning: Start event missing.");
		}
		if ( count($this->getAllEndNodes()) == 0 ) {
			if ( in_array(1, $errorTypes) ) array_push($issues, "Error: End event missing.");
			if ( in_array(1, $warningTypes) ) array_push($issues, "Warning: End event missing.");
		}
		
		// Ereignis haben max. eine eingehende und ausgehende Kante und sollten nur einmal modelliert werden
		$eventLabels = array();
		foreach ( $this->events as $eventID => $label ) {
			// check incoming and outgoing edges
			if ( count($this->getPredecessor($eventID)) > 1 ) {
				if ( in_array(3, $errorTypes) ) array_push($issues, "Error: Event \"".$label."\" has more than one incoming edge.");
				if ( in_array(3, $warningTypes) ) array_push($issues, "Warning: Event \"".$label."\" has more than one incoming edge.");
			}
			if ( count($this->getSuccessor($eventID)) > 1 ) {
				if ( in_array(3, $errorTypes) ) array_push($issues, "Error: Event \"".$label."\" has more than one outgoing edge.");
				if ( in_array(3, $warningTypes) ) array_push($issues, "Warning: Event \"".$label."\" has more than one outgoing edge.");
			}
			
			// check whether the labels are unique
			if ( !in_array($label, $eventLabels) ) {
				array_push($eventLabels, $label);
			} else {
				if ( in_array(5, $errorTypes) ) {
					$error = "Error: Event \"".$label."\" occurs more than once.";
					if ( !in_array($error, $issues) ) array_push($issues, $error);
				}
				if ( in_array(5, $warningTypes) ) {
					$error = "Warning: Event \"".$label."\" occurs more than once.";
					if ( !in_array($error, $issues) ) array_push($issues, $error);
				}
			}
			
			// check event-function alternation
			$successors = $this->getSuccessorsSkipConnectors($eventID);
			foreach ( $successors as $succID ) {
				if ( $this->isEvent($succID) ) {
					if ( in_array(4, $errorTypes) ) array_push($issues, "Error: Event \"".$label."\" is followed by another event.");
					if ( in_array(4, $warningTypes) ) array_push($issues, "Warning: Event \"".$label."\" is followed by another event.");
				}
			}
		}
		
		// Funktion haben genau eine eingehende und ausgehende Kante und sollten nur einmal modelliert werden
		$funcLabels = array();
		foreach ( $this->functions as $funcID => $label ) {
			// check incoming and outgoing edges
			if ( count($this->getPredecessor($funcID)) != 1 ) {
				if ( in_array(2, $errorTypes) ) array_push($issues, "Error: Function \"".$label."\" has not exactly one incoming edge.");
				if ( in_array(2, $warningTypes) ) array_push($issues, "Warning: Function \"".$label."\" has not exactly one incoming edge.");
			}
			if ( count($this->getSuccessor($funcID)) != 1 ) {
				if ( in_array(2, $errorTypes) ) array_push($issues, "Error: Function \"".$label."\" has not exactly one outgoing edge.");
				if ( in_array(2, $warningTypes) ) array_push($issues, "Warning: Function \"".$label."\" has not exactly one outgoing edge.");
			}
			
			// check whether the labels are unique
			if ( !in_array($label, $funcLabels) ) { 
				array_push($funcLabels, $label);
			} else {
				if ( in_array(5, $errorTypes) ) {
					$error = "Error: Function \"".$label."\" occurs more than once.";
					if ( !in_array($error, $issues) ) array_push($issues, $error);
				}
				if ( in_array(5, $warningTypes) ) {
					$error = "Warning: Function \"".$label."\" occurs more than once.";
					if ( !in_array($error, $issues) ) array_push($issues, $error);
				}
			}
			
			// check event-function alternation
			$successors = $this->getSuccessorsSkipConnectors($funcID);
			foreach ( $successors as $succID ) {
				if ( $this->isFunction($succID) ) {
					if ( in_array(4, $errorTypes) ) array_push($issues, "Error: Function \"".$label."\" is followed by another function.");
					if ( in_array(4, $warningTypes) ) array_push($issues, "Warning: Function \"".$label."\" is followed by another function.");
				}
			}
		}
		
		// Konnektoren haben genau eine eigehende und mehrere ausgehende Kanten (Split-Konnektor) oder mehrere eingehende und genau eine ausgehende Kante (Join-Konnektor).
		$connectors = $this->getAllConnectors();
		foreach ( $connectors as $connID => $type ) {
			// check incoming and outgoing edges
			$reported = false;
			if ( count($this->getPredecessor($connID)) == 0 || count($this->getSuccessor($connID)) == 0 ) { 
				if ( in_array(6, $errorTypes) ) array_push($issues, "Error: Missing incoming and outgoing edges for ".$type."-connector.");
				if ( in_array(6, $warningTypes) ) array_push($issues, "Warning: Missing incoming and outgoing edges for ".$type."-connector.");
				$reported = true; 
			}
			if ( count($this->getPredecessor($connID)) <= 1 && count($this->getSuccessor($connID)) <= 1 && !$reported ) {
				if ( in_array(6, $errorTypes) ) array_push($issues, "Error: Missing incoming or outgoing edges for ".$type."-connector.");
				if ( in_array(6, $warningTypes) ) array_push($issues, "Warning: Missing incoming or outgoing edges for ".$type."-connector.");
			}
			if ( count($this->getPredecessor($connID)) > 1 && count($this->getSuccessor($connID)) > 1 ) {
				if ( in_array(6, $errorTypes) ) array_push($issues, "Error: ".$type."-connectors is split and join connector at the same time.");
				if ( in_array(6, $warningTypes) ) array_push($issues, "Warning: ".$type."-connectors is split and join connector at the same time.");
			}
		}
		
		return $issues;
	}

	private function getNextID() {
		return count($this->idConversion);
	}

	public function getNumOfNodes() {
		return count($this->events) + count($this->functions) + count($this->xor) + count($this->or) + count($this->and);
	}

	public function deleteDummyTransitions() {
		foreach ( $this->functions as $id => $label ) {
			if ( preg_match("/^t[0-9]*$/", trim($label) ) ) $this->deleteFunction($id);
		}
	}

	public function transformFunctionToEvent($id) {
		if ( $this->isFunction($id) ) {
			$label = $this->functions[$id];
			unset($this->functions[$id]);
			$this->events[$id] = $label;
		}
	}
	
	/**
	 * Berechnet die Aehnlichkeit zwischen dieser und einer anderen EPK auf Basis der enthaltenen Kanten,
	 * wobei Kanten durch die Label der ein- und ausgehenden Knoten definiert ist.
	 * 
	 * @param EPC $epc
	 * @return number
	 */
	public function compareTo(EPC $epc) {
		$humanReadableEdgesThis = $this->convertEdgesToHumanReadable();
		$humanReadableEdgesOther = $epc->convertEdgesToHumanReadable();
		
		$foundEdgesOfThisInOther = 0;
		foreach ( $humanReadableEdgesThis as $edge ) {
			if ( in_array($edge, $humanReadableEdgesOther) ) $foundEdgesOfThisInOther++;
		}
		
		$foundEdgesOfOtherInThis = 0;
		foreach ( $humanReadableEdgesOther as $edge ) {
			if ( in_array($edge, $humanReadableEdgesThis) ) $foundEdgesOfOtherInThis++;
		}
		
		$numEdgesInThis = count($humanReadableEdgesThis);
		$numEdgesInOther = count($humanReadableEdgesOther);
		
		return round(((($foundEdgesOfThisInOther+$foundEdgesOfOtherInThis)/($numEdgesInThis+$numEdgesInOther)))*100, 2);
	}
	
	public function convertEdgesToHumanReadable() {
		$humanReadableEdges = array();
		foreach ( $this->edges as $edge ) {
			$keys = array_keys($edge);
			$sourceID = $keys[0];
			$targetID = $edge[$sourceID];
			$source = "[".$this->getType($sourceID)."] ".$this->getNodeLabel($sourceID);
			$target = "[".$this->getType($targetID)."] ".$this->getNodeLabel($targetID);
			array_push($humanReadableEdges, array($source => $target));
		}
		return $humanReadableEdges;
	}
	
	public function convertEdgesToHumanReadableWithIDs() {
		$humanReadableEdges = array();
		$humanReadableEdgesIDs = array();
		foreach ( $this->edges as $edge ) {
			$keys = array_keys($edge);
			$sourceID = $keys[0];
			$targetID = $edge[$sourceID];
			$source = "[".$this->getType($sourceID)."] ".$this->getNodeLabel($sourceID);
			$target = "[".$this->getType($targetID)."] ".$this->getNodeLabel($targetID);
			array_push($humanReadableEdges, array($source => $target));
			array_push($humanReadableEdgesIDs, array($sourceID => $targetID));
		}
		return array("stringEdges" => $humanReadableEdges, "idEdges" => $humanReadableEdgesIDs);
	}
	
	public function removeProMLabelSuffix() {
		foreach ($this->functions as $id => $label) {
			$this->functions[$id] = substr($label, 0, -9);
		}
	
		foreach ($this->events as $id => $label) {
			$newLabel = str_replace("\\ncomplete", "", $label);
			$newLabel = str_replace("  ", " ", $newLabel);
			$newLabel = substr($newLabel, 0, -9);
			$this->events[$id] = $newLabel;
		}
	}
	
	/**
	 * getHash
	 * 
	 * calculates a hash code (md5) based on function and event label and the number of edges and connectors and the EPC name
	 * 
	 * @return string
	 */
	public function getHash() {
		$sFunctions = $this->functions;
		sort($sFunctions);
		$functionPart = implode("-", array_values($sFunctions));
		$sEvents = $this->events;
		sort($sEvents);
		$eventPart = implode("-", array_values($sEvents));
		$edgePart = count($this->edges);
		$xorPart = count($this->xor);
		$andPart = count($this->and);
		$orPart = count($this->or);
		return md5($this->name.$functionPart.$eventPart.$edgePart.$xorPart.$andPart.$orPart);
	}
	
	public function getIDsForLabel($label) {
		$result = array("functions" => array(), "events" => array());
		foreach ( $this->functions as $id => $functionLabel ) {
			if ( strcmp($label, $functionLabel) == 0 ) array_push($result["functions"], $id);
		}
		foreach ( $this->events as $id => $eventLabel ) {
			if ( strcmp($label, $eventLabel) == 0 ) array_push($result["events"], $id);
		}
		return $result;
	}
	
	public function getEPCName() {
		return $this->convertIllegalChars($this->name);
	}
	
	public function transformToBPMN() {
		$bpmn = new BPMN(null, $this->name, null);
		
		$participantID = $bpmn->getNextID();
		$bpmn->participants[$participantID] = $this->name;
		$bpmn->processParticipantAssignments[md5($this->name)] = $participantID;
		
		foreach ( $this->events as $id => $label ) {
			$bpmn->events[$id] = $label;
			
			$preds = $this->getPredecessor($id);
			$succs = $this->getSuccessor($id);
			if ( empty($preds) ) { array_push($bpmn->startEvents, $id); }
			elseif ( empty($succs) ) { array_push($bpmn->endEvents, $id); }
			else { array_push($bpmn->events, $id); }
			
			array_push($bpmn->reservedIDs, $id);
		}
		
		foreach ( $this->functions as $id => $label ) {
			$bpmn->activities[$id] = $label;
			array_push($bpmn->reservedIDs, $id);
		}
		
		foreach ( $this->xor as $id => $label ) {
			$bpmn->gateways[$id] = "";
			array_push($bpmn->exclusiveGateways, $id);
			array_push($bpmn->reservedIDs, $id);
		}
		
		foreach ( $this->or as $id => $label ) {
			$bpmn->gateways[$id] = "";
			array_push($bpmn->eventBasedGateways, $id);
			array_push($bpmn->reservedIDs, $id);
		}
		
		foreach ( $this->and as $id => $label ) {
			$bpmn->gateways[$id] = "";
			array_push($bpmn->parallelGateways, $id);
			array_push($bpmn->reservedIDs, $id);
		}
		
		foreach ( $this->edges as $dummy => $edge ) {
			foreach ( $edge as $source => $target ) {
				$arcID = $bpmn->getNextID();
				$bpmn->arcs[$arcID] = array("source" => $source, "target" => $target, "label" => "");
				array_push($bpmn->seqFlows, $arcID);
				array_push($bpmn->reservedIDs, $arcID);
			}
		}
		
		foreach ( $this->orgUnits as $id => $label ) {
			$bpmn->lanes[$id] = $label;
			array_push($bpmn->reservedIDs, $id);
			$bpmn->laneParticipantAssignments[$id] = $participantID;
		}
		
		foreach ( $this->functionOrgUnitAssignments as $funcID => $orgUnitID ) {
			$bpmn->nodeLaneAssignments[$funcID] = $orgUnitID;
		}
		
		// Add lane for not assigned nodes
		$nodesWithoutLane = array();
		$allNodes = $this->getAllNodes();
		foreach ( $allNodes as $id => $label ) {
			if ( !array_key_exists($id, $bpmn->nodeLaneAssignments) ) array_push($nodesWithoutLane, $id);
		}
		if ( !empty($nodesWithoutLane) ) {
			$laneID = $bpmn->getNextID();
			$bpmn->lanes[$laneID] = "unknown lane";
			$bpmn->laneParticipantAssignments[$laneID] = $participantID;
			
			foreach ( $nodesWithoutLane as $nodeID ) {
				$bpmn->nodeLaneAssignments[$nodeID] = $laneID;
			}
		}
		
		return $bpmn;
	}
	
	public function transformToPetriNet() {
		$pn = new PetriNet(null, $this->id, $this->name);
		$ignoreToArcs = array();
		$ignoreFromArcs = array();
	
		foreach ( $this->events as $id => $label ) {
			$pn->places[$id] = $label;
		}
	
		foreach ( $this->functions as $id => $label ) {
			$pn->transitions[$id] = $label;
		}
		
		foreach ( $this->and as $id => $label ) {		
			$pn->transitions[$id] = "IT_AND_".$id;
		}
	
		foreach ( $this->xor as $id => $label ) {
			$pn->places[$id] = "IP_XOR_".$id;
		}
		
		foreach ( $this->or as $id => $label ) {
			$pn->places[$id] = "IP_OR_".$id;
			
			$preds = $this->getPredecessor($id);
			$numPreds = count($preds);
			$succs = $this->getSuccessor($id);
			$numSuccs = count($succs);
			
			if ( $numPreds > 1 ) {
				array_push($ignoreToArcs, $id);
				
				// build entry places
				$fromReplacements = array();
				foreach ( $preds as $i => $pred) {
					$freeID = $this->getFreeNodeID();
					$pn->places[(string) $freeID] = "IP_OR_".$id."_1_".$i."_from_".$pred;
					$newArc = array("source" => (string) $pred, "target" => (string) $freeID);
					array_push($pn->arcs, $newArc);
					$fromReplacements[$pred] = $freeID;
				}
				
				// only one of the incoming paths is executed
				foreach ( $preds as $i => $pred) {
					$freeID = $this->getFreeNodeID();
					$pn->transitions[(string) $freeID] = "IT_OR_".$id."_1_".$i;
					$newArc = array("source" => (string) $fromReplacements[$pred], "target" => (string) $freeID);
					array_push($pn->arcs, $newArc);
					$newArc = array("source" => (string) $freeID, "target" => (string) $id);
					array_push($pn->arcs, $newArc);
				}
				
				// more than one of the incoming paths are executed
				for ( $i=2; $i<=$numPreds; $i++ ) {
					$permutations = StateExtractor::getSplitORPermutations($preds, $i);
					foreach ( $permutations as $index => $permutation ) {
						$showIndex = $index + 1;
						$freeID = $this->getFreeNodeID();
						$pn->transitions[(string) $freeID] = "IT_OR_".$id."_".$i."_".$showIndex;
						foreach ( $permutation as $pred ) {
							$newArc = array("source" => (string) $fromReplacements[$pred], "target" => (string) $freeID);
							array_push($pn->arcs, $newArc);
							$newArc = array("source" => (string) $freeID, "target" => (string) $id);
							array_push($pn->arcs, $newArc);
						}
					}
				}
			}
			
			if ( $numSuccs > 1 ) {

				array_push($ignoreFromArcs, $id);
				
				// build entry places
				$targetReplacements = array();
				foreach ( $succs as $i => $succ) {
					$freeID = $this->getFreeNodeID();
					$pn->places[(string) $freeID] = "IP_OR_".$id."_1_".$i."_to_".$succ;
					$newArc = array("source" => (string) $freeID, "target" => (string) $succ);
					array_push($pn->arcs, $newArc);
					$targetReplacements[$succ] = $freeID;
				}
				
				// only one of the following paths is executed
				foreach ( $succs as $i => $succ) {
					$freeID = $this->getFreeNodeID();
					$pn->transitions[(string) $freeID] = "IT_OR_".$id."_1_".$i;
					$newArc = array("source" => (string) $freeID, "target" => (string) $targetReplacements[$succ]);
					array_push($pn->arcs, $newArc);
					$newArc = array("source" => (string) $id, "target" => (string) $freeID);
					array_push($pn->arcs, $newArc);
				}
				
				// more than one of the following paths are executed
				for ( $i=2; $i<=$numSuccs; $i++ ) {
					$permutations = StateExtractor::getSplitORPermutations($succs, $i);
					foreach ( $permutations as $index => $permutation ) {
						$showIndex = $index + 1;
						$freeID = $this->getFreeNodeID();
						$pn->transitions[(string) $freeID] = "IT_OR_".$id."_".$i."_".$showIndex;
						foreach ( $permutation as $succ ) {
							$newArc = array("source" => (string) $freeID, "target" => (string) $targetReplacements[$succ]);
							array_push($pn->arcs, $newArc);
							$newArc = array("source" => (string) $id, "target" => (string) $freeID);
							array_push($pn->arcs, $newArc);
						}
					}
				}
			}
		}
	
		foreach ( $this->edges as $index => $edge ) {
			$sourceIDs = array_keys($edge);
			$sourceID = $sourceIDs[0];
			$targetID = $edge[$sourceID];
			 
			if ( in_array($sourceID, $ignoreFromArcs) ) continue;
			if ( in_array($targetID, $ignoreToArcs) ) continue;
			
			$newArc = array("source" => (string) $sourceID, "target" => (string) $targetID);
			array_push($pn->arcs, $newArc);
		}
		
		$pn->postProcessEPC2PetriNet();
		
		return $pn;
	}
	
	public function autocorrectSpelling() {
		foreach ( $this->functions as $id => $label ) {
			$suggestion_spell = NLP::generateSpellingCorrectionRecommendation($label);
			if ($suggestion_spell) {
				$this->functions[$id] = $suggestion_spell;
				array_push($this->spellingCorrections, "'".$label."' replaced by '".$suggestion_spell."'");
			}
		}
		
		foreach ( $this->events as $id => $label ) {
			$suggestion_spell = NLP::generateSpellingCorrectionRecommendation($label);
			if ($suggestion_spell) {
				$this->events[$id] = $suggestion_spell;
				array_push($this->spellingCorrections, "'".$label."' replaced by '".$suggestion_spell."'");
			}
		}
		
		return $this->spellingCorrections;
	}
	
	public function isNodeIDAssigned($id) {
		if ( isset($this->events[$id]) ) return true;
		if ( isset($this->functions[$id]) ) return true;
		if ( isset($this->edges[$id]) ) return true;
		if ( isset($this->orgUnits[$id]) ) return true;
		if ( isset($this->xor[$id]) ) return true;
		if ( isset($this->or[$id]) ) return true;
		if ( isset($this->and[$id]) ) return true;
	}
	
 	public function getFreeNodeID() {
 		while ( $this->isNodeIDAssigned($this->internalReservedID) ) $this->internalReservedID++;
 		return $this->internalReservedID++;
 	}
}
?>