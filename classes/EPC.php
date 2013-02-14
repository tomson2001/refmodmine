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
	
	public $traces;

	public $warnings = array();

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
	
	public function isFunction($nodeID) {
		return array_key_exists($nodeID, $this->functions);
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
		$successors = $this->getSuccessor($nodeID);
		$predecessors = $this->getPredecessor($nodeID);
		return count($successors) == 1 && $predecessors >= 1;
	}

	/**
	 * Prueft, ob es sich bei einem Knoten um einen AND-Join handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isANDJoin($nodeID) {
		$joinConnectors = $this->getAllJoinConnectors($this->and);
		return array_key_exists($nodeID, $joinConnectors);
	}
	
	/**
	 * Prueft, ob es sich bei einem Knoten um einen XOR-Join handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isXORJoin($nodeID) {
		$joinConnectors = $this->getAllJoinConnectors($this->xor);
		return array_key_exists($nodeID, $joinConnectors);
	}
	
	/**
	 * Prueft, ob es sich bei einem Knoten um einen OR-Join handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isORJoin($nodeID) {
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
		return array_key_exists($nodeID, $this->or);
	}
	
	public function isXor($nodeID) {
		return array_key_exists($nodeID, $this->xor);
	}

	/**
	 * Prueft, ob es sich bei einem Knoten um einen AND-Split handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isANDSplit($nodeID) {
		$splitConnectors = $this->getAllSplitConnectors($this->and);
		return array_key_exists($nodeID, $splitConnectors);
	}
	
	/**
	 * Prueft, ob es sich bei einem Knoten um einen AND-Split handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isORSplit($nodeID) {
		$splitConnectors = $this->getAllSplitConnectors($this->or);
		return array_key_exists($nodeID, $splitConnectors);
	}
	
	/**
	 * Prueft, ob es sich bei einem Knoten um einen XOR-Split handelt
	 * @param mixed $nodeID
	 * @return boolean
	 */
	public function isXORSplit($nodeID) {
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
	
	public function exportEPML() {
		$content =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$content .= "<epml:epml xmlns:epml=\"http://www.epml.de\"\n";
		$content .= "  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"epml_1_draft.xsd\">\n";
		$content .= "  <epc EpcId=\"1\" name=\"".$this->name."\">\n";
		
		$maxID = 0;
		
		foreach ( $this->functions as $id => $label ) {
			$content .= "    <function id=\"".$id."\">\n";
			$content .= "      <name>".$label." (".$id.")</name>\n";
			$content .= "    </function>\n";
			if ( $id > $maxID ) $maxID = $id+1;
		}
		
		foreach ( $this->events as $id => $label ) {
			$content .= "    <event id=\"".$id."\">\n";
			$content .= "      <name>".$label." (".$id.")</name>\n";
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
		
		$content .= "  </epc>\n";
		$content .= "</epml:epml>";
		
		$fileGenerator = new FileGenerator(trim($this->name).".epml", $content);
		$file = $fileGenerator->execute();
		return $file;
	}

}
?>