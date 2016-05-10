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
		if ( $format == "pnml" && !is_null($xml) ) {
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
	
	public function deleteArc($sourceID, $targetID) {
		foreach ( $this->arcs as $id => $arc ) {
			if ( $arc["source"] == $sourceID && $arc["target"] == $targetID ) unset($this->arcs[$id]);
		}
	}
	
	public function exportPNML() {
		$content =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$content .= "<pnml>\n";
		$pnmlCodePart = $this->getPNMLCodePart();
		$content .= $pnmlCodePart;
		$content .= "</pnml>";
	
		$fileGenerator = new FileGenerator(trim($this->name).".pnml", $content);
		$fileGenerator->setFilename(trim($this->name).".pnml");
		$file = $fileGenerator->execute();
		return $file;
	}
	
	public function getPNMLCodePart($print_ids = false) {
		$content = "  <net id=\"".EPC::convertIllegalChars($this->name)."\" type=\"http://www.informatik.hu-berlin.de/top/pnml/basicPNML.rng\">\n";
	
		foreach ( $this->places as $id => $label ) {
			$content .= "    <place id=\"".$id."\">\n";
			$content .= "       <name>\n";
			$content .= "          <text>".htmlspecialchars(EPC::convertIllegalChars($label))."</text>\n";
			$content .= "       </name>\n";
			$content .= "    </place>\n";
		}
	
		foreach ( $this->transitions as $id => $label ) {
			$content .= "    <transition id=\"".$id."\">\n";
			$content .= "       <name>\n";
			$content .= "          <text>".htmlspecialchars(EPC::convertIllegalChars($label))."</text>\n";
			$content .= "       </name>\n";
			$content .= "    </transition>\n";
		}
	
		foreach ( $this->arcs as $id => $arc ) {
			$content .= "    <arc id=\"".$id."\" source=\"".$arc["source"]."\" target=\"".$arc["target"]."\">\n";
			$content .= "    </arc>\n";
		}
	
		$content .= "  </net>\n";
		return $content;
	}
	
	public function postProcessEPC2PetriNet() {		
		$operationCounter = 0;
		foreach ( $this->transitions as $id => $label ) {
			// remove dummy transitions
			if ( Tools::startsWith($label, "IT_AND_") || Tools::startsWith($label, "IT_OR_") ) {
				$preds = $this->getPredecessors($id);
				$succs = $this->getSuccessors($id);
				
				// transition is an AND-split
				if ( count($preds) == 1 ) {
					
					// the node before is also a transition. thus, dummy-transition can be removed
					if ( isset($this->transitions[$preds[0]]) ) {
						$this->deleteArc($preds[0], $id);
						unset($this->transitions[$id]);
						//print("\ndelete transition ".$label." (".$id.")");
						
						foreach ( $succs as $succ ) {
							$this->deleteArc($id, $succ);
							//print("\n  add arc from ".$this->getLabel($preds[0])." to ".$this->getLabel($succ));
							$newArc = array("source" => $preds[0], "target" => $succ);
							array_push($this->arcs, $newArc);
						}
						
						$operationCounter++;
						break;
					}

				}
				
				// add dummy places, if the succeeding nodes are transitions
				foreach ( $succs as $succ ) {
					if ( isset($this->transitions[$succ]) ) {
						$newPlaceID = $this->getFreeNodeID();
						$this->places[$newPlaceID] = "IP_".$newPlaceID;
				
						//print("\nadd place (1) "."IT_".$newPlaceID);
						//print("\n  add arc from ".$this->getLabel($correctID)." to ".$this->getLabel($newTransID));
						//print("\n  add arc from ".$this->getLabel($newTransID)." to ".$this->getLabel($succ));
				
						$this->deleteArc($id, $succ);
						$newArc = array("source" => $id, "target" => $newPlaceID);
						array_push($this->arcs, $newArc);
						$newArc = array("source" => $newPlaceID, "target" => $succ);
						array_push($this->arcs, $newArc);
				
						$operationCounter++;
					}
				}
				
				if ( $operationCounter > 0 ) break;
				
				// transition is an AND-join
				if ( count($succs) == 1 ) {
					
					// the node after is also a transition. thus, dummy-transition can be removed
					if ( isset($this->transitions[$succs[0]]) ) {
						$this->deleteArc($id, $succs[0]);
						unset($this->transitions[$id]);
						//print("\ndelete transition ".$label." (".$id.")");
							
						foreach ( $preds as $pred ) {
							$this->deleteArc($pred, $id);
							//print("\n  add arc from ".$this->getLabel($pred)." to ".$this->getLabel($succ[0]));
							$newArc = array("source" => $pred, "target" => $succs[0]);
							array_push($this->arcs, $newArc);
						}
						
						$operationCounter++;
						break;
					}

				}
				
				// add dummy places, if the preceeding nodes are transitions
				foreach ( $preds as $pred ) {
					if ( isset($this->transitions[$pred]) ) {
						$newPlaceID = $this->getFreeNodeID();
						$this->places[$newPlaceID] = "IP_".$newPlaceID;
				
						//print("\nadd place (2) "."IT_".$newPlaceID);
						//print("\n  add arc from ".$this->getLabel($pred)." to ".$this->getLabel($newTransID));
						//print("\n  add arc from ".$this->getLabel($newTransID)." to ".$this->getLabel($id));
				
						$this->deleteArc($pred, $id);
						$newArc = array("source" => $pred, "target" => $newPlaceID);
						array_push($this->arcs, $newArc);
						$newArc = array("source" => $newPlaceID, "target" => $id);
						array_push($this->arcs, $newArc);
						$operationCounter++;
					}
				}
					
				if ( $operationCounter > 0 ) break;
			}
		}
		
		// remove dummy places
		foreach ( $this->places as $id => $label ) {
			
			if ( Tools::startsWith($label, "IP_XOR_") || Tools::startsWith($label, "IP_OR_") ) {
				$preds = $this->getPredecessors($id);
				$succs = $this->getSuccessors($id);
				
				$correctID = $id;
				
				if ( count($preds) == 1 ) {
					
					// place is an (X)OR-split, the node before is also a place. thus, dummy-place can be removed
					if ( isset($this->places[$preds[0]]) ) {
						$this->deleteArc($preds[0], $id);
						unset($this->places[$id]);
						//print("\ndelete place ".$label." (".$id.")");
						
						foreach ( $succs as $succ ) {
							$this->deleteArc($id, $succ);
							//print("\n  add arc from ".$this->getLabel($preds[0])." to ".$this->getLabel($succ));
							$newArc = array("source" => $preds[0], "target" => $succ);
							array_push($this->arcs, $newArc);
						}
						
						$correctID = $preds[0];
						$operationCounter++;
						break;
					}

				}
				
				// add dummy transitions, if the succeeding nodes are places
				foreach ( $succs as $succ ) {
					if ( isset($this->places[$succ]) ) {

						$newTransID = $this->getFreeNodeID();
						$this->transitions[$newTransID] = "IT_".$newTransID;
						
// 						if ( $this->name == "KL_03" && $id == "34" ) {
// 							print("\n\nPreds;");
// 							print_r($preds);
// 							print("Succs;");
// 							print_r($succs);
// 							print("\nadd transition (1) "."IT_".$newTransID." from ".$this->getLabel($id)." to ".$this->getLabel($succ)."\n");
// 							print("\n  add arc from ".$this->getLabel($id)." to ".$this->getLabel($newTransID));
// 							print("\n  add arc from ".$this->getLabel($newTransID)." to ".$this->getLabel($succ));
// 							print("\n");
// 						}
							
						//print("\nadd transition (1) "."IT_".$newTransID);
						//print("\n  add arc from ".$this->getLabel($id)." to ".$this->getLabel($newTransID));
						//print("\n  add arc from ".$this->getLabel($newTransID)." to ".$this->getLabel($succ));
							
						$this->deleteArc($correctID, $succ);
						$newArc = array("source" => $id, "target" => $newTransID);
						array_push($this->arcs, $newArc);
						$newArc = array("source" => $newTransID, "target" => $succ);
						array_push($this->arcs, $newArc);
							
						$operationCounter++;
					}
				}
					
				if ( $operationCounter > 0 ) break;
				
				if ( count($succs) == 1 ) {
					
// 					if ( $this->getLabel($succs[0]) == "mileage has not been booked" && $this->name == "KL_03") print("\nFOUND");
// 					print ("\n ". $this->getLabel($succs[0]) );
					
					if ( isset($this->places[$succs[0]]) ) {
						
						// place is an (X)OR-join, the node after is also a place. thus, dummy-place can be removed
						$this->deleteArc($id, $succs[0]);
						unset($this->places[$id]);
						//print("\ndelete place ".$label." (".$id.")");
					
						foreach ( $preds as $pred ) {
							$this->deleteArc($pred, $id);
							//print("\n  add arc from ".$this->getLabel($pred)." to ".$this->getLabel($succs[0]));
							$newArc = array("source" => $pred, "target" => $succs[0]);
							array_push($this->arcs, $newArc);
						}
					
						$correctID = $succs[0];
						$operationCounter++;
						break;
					}

				}
				
				// add dummy transitions, if the preceeding nodes are places
				foreach ( $preds as $pred ) {
					if ( isset($this->places[$pred]) ) {
						$newTransID = $this->getFreeNodeID();
						$this->transitions[$newTransID] = "IT_".$newTransID;
							
						//print("\nadd transition (2) "."IT_".$newTransID." (".$operationCounter.") Pred: (".$this->places[$pred].")");
						//print("\n  add arc from ".$this->getLabel($pred)." to ".$this->getLabel($newTransID));
						//print("\n  add arc from ".$this->getLabel($newTransID)." to ".$this->getLabel($id));
							
						$this->deleteArc($pred, $id);
						$newArc = array("source" => $pred, "target" => $newTransID);
						array_push($this->arcs, $newArc);
						$newArc = array("source" => $newTransID, "target" => $id);
						array_push($this->arcs, $newArc);
						$operationCounter++;
					}
				}
			}
		}
		
		if ( $operationCounter > 0 ) return $this->postProcessEPC2PetriNet();
		
		// ensure alternation of transitions and places
		foreach ( $this->transitions as $id => $label ) {
			$preds = $this->getPredecessors($id);
			$succs = $this->getSuccessors($id);
			
			// add dummy places, if the preceeding nodes are transitions
			foreach ( $preds as $pred ) {
				if ( isset($this->transitions[$pred]) ) {
					$newPlaceID = $this->getFreeNodeID();
					$this->places[$newPlaceID] = "IP_".$newPlaceID;
			
					$this->deleteArc($pred, $id);
					$newArc = array("source" => $pred, "target" => $newPlaceID);
					array_push($this->arcs, $newArc);
					$newArc = array("source" => $newPlaceID, "target" => $id);
					array_push($this->arcs, $newArc);
					$operationCounter++;
				}
			}
			
			// add dummy places, if the succeeding nodes are transitions
			foreach ( $succs as $succ ) {
				if ( isset($this->transitions[$succ]) ) {
					$newPlaceID = $this->getFreeNodeID();
					$this->places[$newPlaceID] = "IP_".$newPlaceID;

					$this->deleteArc($id, $succ);
					$newArc = array("source" => $id, "target" => $newPlaceID);
					array_push($this->arcs, $newArc);
					$newArc = array("source" => $newPlaceID, "target" => $succ);
					array_push($this->arcs, $newArc);
					$operationCounter++;
				}
			}
			
			// add start place if not available
			if ( count($preds) == 0 ) {
				$newPlaceID = $this->getFreeNodeID();
				$this->places[$newPlaceID] = "IP_start_".$newPlaceID;
				$newArc = array("source" => $newPlaceID, "target" => $id);
				array_push($this->arcs, $newArc);
				$operationCounter++;
			}
			
			// add end place if not available
			if ( count($succs) == 0 ) {
				$newPlaceID = $this->getFreeNodeID();
				$this->places[$newPlaceID] = "IP_end_".$newPlaceID;
				$newArc = array("source" => $id, "target" => $newPlaceID);
				array_push($this->arcs, $newArc);
				$operationCounter++;
			}
		}
		
		if ( $operationCounter > 0 ) return $this->postProcessEPC2PetriNet();
		
		// add missing transition before start and end places, if necessary
		foreach ( $this->places as $id => $label ) {
			$preds = $this->getPredecessors($id);
			$succs = $this->getSuccessors($id);
			
			// add dummy transitions, if the succeeding nodes are places
			foreach ( $succs as $succ ) {
				if ( isset($this->places[$succ]) ) {
					$newTransID = $this->getFreeNodeID();
					$this->transitions[$newTransID] = "IT_".$newTransID;
						
					$this->deleteArc($correctID, $succ);
					$newArc = array("source" => $id, "target" => $newTransID);
					array_push($this->arcs, $newArc);
					$newArc = array("source" => $newTransID, "target" => $succ);
					array_push($this->arcs, $newArc);
						
					$operationCounter++;
				}
			}
			
			// add dummy transitions, if the preceeding nodes are places
			foreach ( $preds as $pred ) {
				if ( isset($this->places[$pred]) ) {
					$newTransID = $this->getFreeNodeID();
					$this->transitions[$newTransID] = "IT_".$newTransID;
						
					$this->deleteArc($pred, $id);
					$newArc = array("source" => $pred, "target" => $newTransID);
					array_push($this->arcs, $newArc);
					$newArc = array("source" => $newTransID, "target" => $id);
					array_push($this->arcs, $newArc);
					$operationCounter++;
				}
			}
		}
		
		if ( $operationCounter == 0 ) return null;
		return $this->postProcessEPC2PetriNet();
	}
	
	public function getFreeNodeID() {
		$nodes = $this->places + $this->transitions;
		$id = 15325;
 		while ( isset($nodes[$id]) || isset($nodes[(string) $id])) $id++;
 		return (string) $id;
	}
	
	public function getLabel($nodeID) {
		$nodes = $this->transitions + $this->places;
		return $nodes[(string) $nodeID];
	}
	
}
?>