<?php
class EPCExt {

	public $name;
	private $xml;

	public $functions = array();
	public $events = array();
	public $edges = array();
	public $xor = array();
	public $or = array();
	public $and = array();
	public $processInterfaces = array();
	public $processInterfaceLinks = array();
	public $seq = array();
	public $refinedFunctions = array();
	public $refinedFunctionLinks = array();
	
	public $traces = array();
	
	public $nodeClusters = array(); // Die hierin enthaltenen Cluster stellen zusammenhaengende EPKs dar. Mehrere Cluster innerhalb einer EPK (epml) koennen dadurch moeglich sein
	
	public $idConversion = array();
	
	public $autoCorrected = false;
	public $correctSplitJoinConnectors = false;
	public $correctSenselessConnectors = false;
	public $correctStandAndEndFunctions = false;
	
	// Metriken
	public $isCoherent;
	public $illegalEvents = 0;
	public $illegalEventLabels = array();
	public $startEvents = 0;
	public $endEvents = 0;
	public $illegalFunctions = 0;
	public $illegalFunctionLabels = array();
	public $startFunctions = 0;
	public $endFunctions = 0;
	public $undefinedConnectors = 0;
	public $errorConnectors = 0;
	public $startConnectors = 0;
	public $endConnectors = 0;
	public $startProcessInterfaces = 0;
	public $endProcessInterfaces = 0;
	public $intermediateProcessInterfaces = 0;

	public $warnings = array();

	/**
	 * SimpleXMLElement aus einer EPML-File
	 *
	 * @param SimpleXMLElement $epc
	*/
	public function __construct($xml, $modelID, $format="epml") {
		$this->xml = $xml;
		$this->name = $this->convertIllegalChars($modelID);
		if ( $format == "epml" ) {
			$this->loadEPML($xml, $modelID);
		}
		$this->cleanLabels();
		//print_r($this);
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
			$index = $this->getNextID();
			
			$row = simplexml_load_string($function->asXML()); 
			$typeAttribute = $row->xpath("//attribute[@typeRef='ST']");
			$type = isset($typeAttribute[0]) ? $typeAttribute[0]['value'] : "";
			
			// Prozessschnittstelle
			if ( $type == "ST_PRCS_IF" ) {
				$this->processInterfaces[$index] = rtrim(ltrim(utf8_decode($this->convertIllegalChars($function->name))));
				if ( isset($function->toProcess[1]["linkToEpcId"]) ) $this->processInterfaceLinks[$index] = rtrim(ltrim(utf8_decode($this->convertIllegalChars($function->toProcess[1]["linkToEpcId"]))));
			} elseif ( $function->toProcess ) {
				// Hinterlegung
				$this->refinedFunctions[$index] = rtrim(ltrim(utf8_decode($this->convertIllegalChars($function->name))));
				$this->refinedFunctionLinks[$index] = rtrim(ltrim(utf8_decode($this->convertIllegalChars($function->toProcess["linkToEpcId"]))));
				$this->functions[$index] = rtrim(ltrim(utf8_decode($this->convertIllegalChars($function->name))));
			} else {
				// Einfache Funktion
				$this->functions[$index] = rtrim(ltrim(utf8_decode($this->convertIllegalChars($function->name))));
			}
			$this->idConversion[utf8_decode((string) $function["id"])] = $index;
		}
		
		// SEQ-Operatoren laden
		foreach ( $xml->xpath("//epc[@name='".$modelID."']/HELPMEPLEASE") as $element ) {
			$name = rtrim(ltrim(utf8_decode($this->convertIllegalChars($element->name))));
			// es handelt sich tatsaechlich um einen SEQ-Operator
			if ( $name == "SEQ" ) {
				$index = $this->getNextID();
				$this->seq[$index] = "seq";
				$this->idConversion[utf8_decode((string) $element["id"])] = $index;
			}
		}

		// Ereignisse laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/event") as $event) {
			$index = $this->getNextID();
			$this->events[$index] = rtrim(ltrim(utf8_decode($this->convertIllegalChars($event->name))));
			$this->idConversion[utf8_decode((string) $event["id"])] = $index;
		}

		// XOR laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/xor") as $xor) {
			$index = $this->getNextID();
			$this->xor[$index] = "xor";
			$this->idConversion[utf8_decode((string) $xor["id"])] = $index;
		}

		// OR laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/or") as $or) {
			$index = $this->getNextID();
			$this->or[$index] = "or";
			$this->idConversion[utf8_decode((string) $or["id"])] = $index;
		}

		// AND laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/and") as $and) {
			$index = $this->getNextID();
			$this->and[$index] = "and";
			$this->idConversion[utf8_decode((string) $and["id"])] = $index;
		}

		// Kanten laden
		foreach ($xml->xpath("//epc[@name='".$modelID."']/arc") as $edge) {
			$flow = $edge->flow;
			$sourceIndex = $this->idConversion[(string) $flow['source']];
			$targetIndex = $this->idConversion[(string) $flow['target']];
			$edge = array($sourceIndex => $targetIndex);
			array_push($this->edges, $edge);
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
		} elseif ( array_key_exists($nodeID, $this->seq) ) {
			return "seq";	
		} elseif ( array_key_exists($nodeID, $this->processInterfaces) ) {
			return "processinterface";
		} elseif ( array_key_exists($nodeID, $this->refinedFunctions) ) {
			return "refinedfunction";
		} else {
			return false;
		}
	}
	
	public function isFunction($nodeID) {
		$nodeID = (string) $nodeID;
		return array_key_exists($nodeID, $this->functions);
	}
	
	public function isEvent($nodeID) {
		$nodeID = (string) $nodeID;
		return array_key_exists($nodeID, $this->events);
	}
	
	public function isProcessInterface($nodeID) {
		$nodeID = (string) $nodeID;
		return array_key_exists($nodeID, $this->processInterfaces);
	}
	
	public function isRefinedFunction($nodeID) {
		$nodeID = (string) $nodeID;
		return array_key_exists($nodeID, $this->refinedFunctions);
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
		$connectors = array("xor", "or", "and", "seq");
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
		} elseif ( array_key_exists($nodeID, $this->processInterfaces) ) {
			return $this->processInterfaces[$nodeID];
		} elseif (  array_key_exists($nodeID, $this->refinedFunctions) ) {
			return $this->refinedFunctions[$nodeID];
		} elseif ( array_key_exists($nodeID, $this->xor) ) {
			return "xor";
		} elseif ( array_key_exists($nodeID, $this->or) ) {
			return "or";
		} elseif ( array_key_exists($nodeID, $this->and) ) {
			return "and";
		} elseif (  array_key_exists($nodeID, $this->seq) ) {
			return "seq";
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
		return $this->xor + $this->or + $this->and + $this->seq;
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

	public function addEdge($sourceNodeID, $targetNodeID) {
		$sourceNodeID = (string) $sourceNodeID;
		$targetNodeID = (string) $targetNodeID;
		$edge = array($sourceNodeID => $targetNodeID);
		array_push($this->edges, $edge);
	}

	public function getAllNodes() {
		return $this->functions + $this->events + $this->processInterfaces + $this->refinedFunctions + $this->or + $this->xor + $this->and + $this->seq;
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
	public function getFreeNodeID() {
		$allNodes = $this->getAllNodes();
		return max(array_keys($allNodes)) + 1;
	}

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
	
	public function isSEQJoin($nodeID) {
		$nodeID = (string) $nodeID;
		$joinConnectors = $this->getAllJoinConnectors($this->seq);
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
	
	public function isSeq($nodeID) {
		$nodeID = (string) $nodeID;
		return array_key_exists($nodeID, $this->seq);
	}
	
	public function isXor($nodeID) {
		$nodeID = (string) $nodeID;
		return array_key_exists($nodeID, $this->xor);
	}
	
	public function isSEQSplit($nodeID) {
		$nodeID = (string) $nodeID;
		$splitConnectors = $this->getAllSplitConnectors($this->seq);
		return array_key_exists($nodeID, $splitConnectors);
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
		$content .= "  <epc EpcId=\"1\" name=\"".$this->convertIllegalChars($this->name)."\">\n";
		
		$maxID = 0;
		
		foreach ( $this->functions as $id => $label ) {
			$content .= "    <function id=\"".$id."\">\n";
			$content .= "      <name>".$this->convertIllegalChars($label);
			if ( $print_ids ) $content .= " (".$id.")";
			$content .= "</name>\n";
			$content .= "      <attribute typeRef=\"OT\" value=\"OT_FUNC\"/>\n";
			$content .= "      <attribute typeRef=\"ST\" value=\"ST_FUNC\"/>\n";
			$content .= "    </function>\n";
			if ( $id > $maxID ) $maxID = $id+1;
		}
		
		foreach ( $this->refinedFunctions as $id => $label ) {
			$content .= "    <function id=\"".$id."\">\n";
			$content .= "      <name>".$this->convertIllegalChars($label);
			if ( $print_ids ) $content .= " (".$id.")";
			$content .= "</name>\n";
			$content .= "      <toProcess linkToEpcId=\"".$this->refinedFunctionLinks[$id]."\"/>\n";
			$content .= "      <attribute typeRef=\"OT\" value=\"OT_FUNC\"/>\n";
			$content .= "      <attribute typeRef=\"ST\" value=\"ST_FUNC\"/>\n";
			$content .= "    </function>\n";
			if ( $id > $maxID ) $maxID = $id+1;
		}
		
		foreach ( $this->processInterfaces as $id => $label ) {
			$content .= "    <function id=\"".$id."\">\n";
			$content .= "      <name>".$this->convertIllegalChars($label);
			if ( $print_ids ) $content .= " (".$id.")";
			$content .= "</name>\n";
			$content .= "      <toProcess linkToEpcId=\"".$this->processInterfaceLinks[$id]."\"/>\n";
			$content .= "      <toProcess linkToEpcId=\"".$id."\"/>\n";
			$content .= "      <attribute typeRef=\"OT\" value=\"OT_FUNC\"/>\n";
			$content .= "      <attribute typeRef=\"ST\" value=\"ST_PRCS_IF\"/>\n";
			$content .= "    </function>\n";
			if ( $id > $maxID ) $maxID = $id+1;
		}
		
		foreach ( $this->events as $id => $label ) {
			$content .= "    <event id=\"".$id."\">\n";
			$content .= "      <name>".$this->convertIllegalChars($label);
			if ( $print_ids ) $content .= " (".$id.")";
			$content .= "</name>\n";
			$content .= "      <attribute typeRef=\"OT\" value=\"OT_EVT\"/>\n";
			$content .= "      <attribute typeRef=\"ST\" value=\"ST_EV\"/>\n";
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
			$content .= "      <attribute typeRef=\"OT\" value=\"CT_CRT_1\"/>\n";
			$content .= "    </arc>\n";
			
			$maxID++;
		}
		
		$content .= "  </epc>\n";
		$content .= "</epml:epml>";
		
		$fileGenerator = new FileGenerator(trim($this->name).".epml", $content);
		$file = $fileGenerator->execute();
		return $file;
	}
	
	public function convertIllegalChars($string) {
		$string = str_replace("Ä", "Ae", $string);
		$string = str_replace("ä", "ae", $string);
		$string = str_replace("Ö", "Oe", $string);
		$string = str_replace("ö", "oe", $string);
		$string = str_replace("Ü", "Ue", $string);
		$string = str_replace("ü", "ue", $string);
		$string = str_replace("ß", "ss", $string);
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
		
		return $this->isCoherence();
	}
	
	/**
	 * Prueft ob es sich um eine zusammenhaengende EPK handelt
	 * 
	 * Methode: Clusterbildung. Zunaechst werden alle Knoten als eigene Cluster betrachtet. 
	 * Wenn nun Kanten zwischen Knoten existieren, werden die entsprechenden Cluster zusammen-
	 * gefuert. Result als Ergebnis genau ein Cluster, so ist die EPK zusammenhaengend,
	 * andernfalls nicht.
	 */
	public function isCoherence() {
		$nodes = $this->getAllNodes();
		$clusters = array();
		
		// Cluster fuer jeden Knoten erstellen
		foreach ( $nodes as $nodeID => $label ) {
			$cluster = new SimpleNodeCluster();
			$cluster->addNode($nodeID);
			array_push($clusters, $cluster);
		}
		
		// Cluster anhand der Kanten verbinden
		foreach ( $this->edges as $edge ) {
			foreach ( $edge as $sourceID => $targetID ) {
				$clusterID1 = null;
				$clusterID2 = null;
				foreach ( $clusters as $index => $cluster ) {
					if ( $cluster->contains($sourceID) ) $clusterID1 = $index;
					if ( $cluster->contains($targetID) ) $clusterID2 = $index;
					if ( !is_null($clusterID1) && !is_null($clusterID2) ) break;
				}
				// Cluster verbinden und eins entfernen
				if ( $clusterID1 != $clusterID2 ) {
					$clusters[$clusterID1]->combine($clusters[$clusterID2]);
					unset($clusters[$clusterID2]);
				}
			}
		}
		
		$this->nodeClusters = $clusters;
		
		// Wenn genau ein Cluster resultiert, dann ist die EPK zusammenhaengend, andernfalls nicht
		if ( count($clusters) == 1 ) return true;
		return false;
	}
	
	public function calculateSyntaxMetrics() {
				
		$this->isCoherent = $this->isCoherence();
		
		// Ereignisse
		foreach ( $this->events as $eventID => $label ) {
			// Illegal Ereignisse haben max. eine eingehende und ausgehende Kante
			if ( count($this->getPredecessor($eventID)) > 1 || count($this->getSuccessor($eventID)) > 1 ) {
				array_push($this->illegalEventLabels, $label);
				$this->illegalEvents++;
			}
			
			// Startereignisse
			if ( count($this->getPredecessor($eventID)) == 0 ) $this->startEvents++;
			
			// Endereignisse
			if ( count($this->getSuccessor($eventID)) == 0 ) $this->endEvents++;
		}
		
		// Funktionen
		foreach ( $this->functions as $funcID => $label ) {
			// Illegale Funktionen: Funktion haben genau eine eingehende und ausgehende Kante
			if ( count($this->getPredecessor($funcID)) != 1 || count($this->getSuccessor($funcID)) != 1 ) {
				array_push($this->illegalFunctionLabels, $label);
				$this->illegalFunctions++;
			}
			
			// Startfunktionen
			if ( count($this->getPredecessor($funcID)) == 0 ) $this->startFunctions++;
			
			// Endfunktionen
			if ( count($this->getSuccessor($funcID)) == 0 ) $this->endFunctions++;
		}
		
		// Konnektoren
		$connectors = $this->getAllConnectors();
		foreach ( $connectors as $connID => $type ) {
			// Undefined Connectors (genau eine eingehende und eine ausgehende Kante
			if ( count($this->getPredecessor($connID)) == 1 && count($this->getSuccessor($connID)) == 1 ) $this->undefinedConnectors++;
			
			// Error Connectors (mehrere ein- und ausgehenden Kanten
			if ( count($this->getPredecessor($connID)) > 1 && count($this->getSuccessor($connID)) > 1 ) $this->errorConnectors++;
			
			// Startconnectors
			if ( count($this->getPredecessor($connID)) == 0 ) $this->startConnectors++;
			
			// Endconnectors
			if ( count($this->getSuccessor($connID)) == 0 ) $this->endConnectors++;
		}
		
		foreach ( $this->processInterfaces as $id => $label ) {
			// Intermediateprocessinterfaces
			if ( count($this->getPredecessor($id)) > 0 && count($this->getSuccessor($id)) > 0 ) $this->intermediateProcessInterfaces++;
			
			// Startprocessinterfaces
			if ( count($this->getPredecessor($id)) == 0 ) $this->startProcessInterfaces++;
			
			// Endprocessinterfaces
			if ( count($this->getSuccessor($id)) == 0 ) $this->endProcessInterfaces++;			
		}

	}
	
	/**
	 * Prueft, ob Funktionen und Ereignisse immer alternieren
	 */
	private function checkFunctionEventAlternation() {
		$nodes = $this->getAllNodes();
		foreach ( $nodes as $id => $label ) {
			if ( $this->isFunction($id) ) {
				$predecessors = $this->getPredecessorsWithoutConnectors($id);
				$successors = $this->getSuccessorWithoutConnectors($id);
			}
		}
	}
	
	/**
	 * Ermittelt die vorgelagerte Funktions- / Ereignisknoten bzw. Prozessschnittstellen zurueck. Ist nur fuer alle Knoten ausser Konnektoren definiert, dann wird null zurueckgegeben
	 * @param unknown_type $nodeID
	 */
	private function getPredecessorsWithoutConnectors($nodeID) {
		if ( $this->isConnector($nodeID) ) return null;
		$predecessors = $this->getPredecessor($nodeID);
		foreach ( $predecessors as $predID ) {
			// TODO
		}
	}
	
	private function getNextID() {
		return count($this->idConversion);
	}
	
	public function getNumOfNodes() {
		return count($this->events) + count($this->functions) + count($this->xor) + count($this->or) + count($this->and);
	}

}
?>