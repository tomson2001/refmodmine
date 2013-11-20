<?php 
/**
 * EPKs duerfen nicht mit Konnektoren beginnen
 *
 * @author t.thaler
 *
 */
class InitialMarkingGenerator {

	public $epc;
	public $initialMarkings = array();
	public $maxStartNodes = 11;
	
	// Traces von JOIN-Konnektoren, ausgehend von jedem Startknoten
	public $joinStreams;

	public function __construct(EPC &$epc) {
		$this->epc = &$epc;
	}

	public function execute() {
		$startNodes = $this->epc->getAllStartNodes();
		$startNodeIDs = array_keys($startNodes);
		$numOfStartNodes = count($startNodeIDs);
		if ( $numOfStartNodes > $this->maxStartNodes ) return "Calculation abort because of state explosion (".pow(2, $this->maxStartNodes)." start markings)";
		
		for ( $i=1; $i<=$numOfStartNodes; $i++ ) {
			$permutations = $this->getSplitORPermutations($startNodeIDs, $i);
			foreach ( $permutations as $permutation ) {
				//print("Length = ".$i.": ".implode("|", $permutation)."\n");
		
				$state = array();
				$context = array();
				
				// Positive und negative Token auf Start-Kanten legen und Kontext setzen
				foreach ( $startNodeIDs as $nodeID ) {
					$successors = $this->epc->getSuccessor($nodeID);
					$successor = $successors[0];
					$startEdgeID = $this->epc->edgeExists($nodeID, $successor);
					if ( in_array($nodeID, $permutation) ) {
						$state[$startEdgeID] = 1;
						$context[$startEdgeID] = "wait";
					} else {
						$state[$startEdgeID] = -1;
						$context[$startEdgeID] = "dead";
					}
				}
		
				$newMarking = new Marking($this->epc, $state, $context);
				
				// Es handelt sich um ein gueltiges Initial Marking, wenn es nachfolgezustand gibt
				$markingTmp = $newMarking;
				$subsequentMarkings = $markingTmp->computeNextMarkings();
				if ( count($subsequentMarkings) > 0 ) array_push($this->initialMarkings, $newMarking);
			}
		}
		return $this->initialMarkings;
	}
	
	public function execute_old() {
		$startNodes = $this->epc->getAllStartNodes();
		$startNodesTmp = $startNodes;
		$this->computeJoinStreams($startNodes);
		$firstCommonJoin = $this->getFirstCommonJoin($startNodes);

		if ( count($startNodes) == 1 || $firstCommonJoin == "xor" || $firstCommonJoin == "or" ) {
			// Iteration ueber alle Startknoten
			foreach ( $startNodes as $startNodeId => $label ) {
				// Initial Marking erstellen
				$successors = $this->epc->getSuccessor($startNodeId);
				$successor = $successors[0];
				$edge = array($startNodeId => $successor);
				$edgeID = $this->epc->edgeExists($startNodeId, $successor);
				$state[$edgeID] = 1; // State im Sinne von Mendling

				foreach ( $startNodesTmp as $startNodeIdTmp => $labelTmp ) {
					if ( $startNodeIdTmp != $startNodeId ) {
						$successors = $this->epc->getSuccessor($startNodeId);
						$successor = $successors[0];
						$edgeID = $this->epc->edgeExists($startNodeIdTmp, $successor);
						$state[$edgeID] = -1;
					}
				}

				$context = $this->extractContextFromState($state);
				$marking = new Marking($this->epc, $state, $context);
				array_push($this->initialMarkings, $marking);

			}
		}

		// Falls mehrere Startknoten, dann betrachte das
		if ( count($startNodes) > 1 ) {

			/**
			 * Annahme: Pfade fuehren ueber ein AND-Join zusammen
			 */
			if ( $firstCommonJoin == "and" || $firstCommonJoin == "or" ) {

				// Initial State erstellen
				$state = array(); // State im Sinne von Mendling
				foreach ( $startNodes as $startNodeId => $label ) {
					$successors = $this->epc->getSuccessor($startNodeId);
					$successor = $successors[0];
					$edgeID = $this->epc->edgeExists($startNodeId, $successor);
					$state[$edgeID] = 1;
				}
				$context = $this->extractContextFromState($state);
				$marking = new Marking($this->epc, $state, $context);
				
				// Es handelt sich um ein gueltiges Initial Marking, wenn es nachfolgezustand gibt
				$markingTmp = $marking;
				$subsequentMarkings = $markingTmp->computeNextMarkings();
				if ( count($subsequentMarkings) > 0 ) array_push($this->initialMarkings, $marking);
			}

			/**
			 * Annahme: Pfade laufen ueber einen OR-Join zusammen -> Moeglichkeiten durchpermutieren
			 */
			$countStartNodes = count($startNodes);
			if ( $countStartNodes > 2 && $firstCommonJoin == "or" ) {
				// Alle Permutation der Laengen 2 bis Anzahl der Nachfolger-1 (bereits durch AND behandelt) berechnen
				for ( $i=2; $i<$countStartNodes; $i++ ) {
					$permutations = $this->getSplitORPermutations(array_keys($startNodes), $i);
					foreach ( $permutations as $permutation ) {
						$state = array();
						foreach ( $permutation as $permNodeId ) {
							$permNodeSuccs = $this->epc->getSuccessor($permNodeId);
							$successor = $permNodeSuccs[0];
							$edgeID = $this->epc->edgeExists($permNodeId, $successor);
							$state[$edgeID] = 1;
						}

						foreach ( $startNodes as $startNodeId => $label ) {
							if ( !in_array($startNodeId, $permutation) ) {
								$successors = $this->epc->getSuccessor($startNodeId);
								$successor = $successors[0];
								$edgeID = $this->epc->edgeExists($startNodeId, $successor);
								$state[$edgeID] = -1;
							}
						}
						$context = $this->extractContextFromState($state);
						$marking = new Marking($this->epc, $state, $context);
						array_push($this->initialMarkings, $marking);
					}
				}
			}
		}
		return $this->initialMarkings;
	}

	/**
	 * Ermittelt die Moeglichen Permutationen (Kombinationen der nachfolgenden Pfade) der Laenge $length
	 *
	 * @param array $nodes  IDs aller moeglichen Nachfolgeknoten
	 * @param int   $length Gewuenschte Anzahl der Nachfolgeknoten
	 *
	 * @return array von arrays der moeglichen Nachfolgeknoten
	 */
	private function getSplitORPermutations($nodes, $length) {
		$countNodes = count($nodes);
		if ( $countNodes < $length ) {
			return false;
		} elseif ( $countNodes == $length ) {
			return array($nodes);
		} elseif ( $length == 1 ) {
			$permutations = array();
			foreach ( $nodes as $node ) {
				array_push($permutations, array($node));
			}
			return $permutations;
		} else {
			$permutations = array();
			$startIndex = min(array_keys($nodes));
			for ( $i=$startIndex; $i<=$startIndex+$countNodes-$length; $i++ ) {
				$nodeSubset = $nodes;
				for ( $j=$startIndex; $j<=$i; $j++) {
					unset($nodeSubset[$j]);
				}
				$possibleSubPermutations = $this->getSplitORPermutations($nodeSubset, $length-1);
				foreach ( $possibleSubPermutations as $subPermutation ) {
					$permutation = array($nodes[$i]);
					foreach ( $subPermutation as $node ) {
						array_push($permutation, $node);
					}
					array_push($permutations, $permutation);
				}
			}
			return $permutations;
		}
	}

	/**
	 * Berechnet ausgehend von den Startknoten einer EPK die moeglichen JOIN-Konnektor Pfade
	 * Dadurch wird geprueft, ob Pfade ineinanderlaufen, und falls ja, wie
	 *
	 * @param array $startNodes
	 * @return array
	 */
	private function computeJoinStreams($startNodes) {
		$joinStreams = array();
		foreach ( $startNodes as $nodeID => $label ) {
			$detection = array(
					"currentNode" => $nodeID,
					"joinStream"  => array(),
					"nodeMemory"  => array()
			);
			$joinStreamDetection = array();
			array_push($joinStreamDetection, $detection);
			$result = $this->getJoinStream($joinStreamDetection);
			//print_r($result);
			$joinStreams[$nodeID] = array();
			foreach ( $result as $index => $detection ) {
				if ( !in_array($detection["joinStream"], $joinStreams[$nodeID]) ) array_push($joinStreams[$nodeID], $detection["joinStream"]);
			}
		}
		$this->joinStreams = $joinStreams;
		return $joinStreams;
	}

	/**
	 * Rekursive Detection von Join-Konnektor Traces
	 *
	 * @param array $joinStreamDetection
	 * @return array
	 */
	private function getJoinStream($joinStreamDetection) {
		//print_r($joinStreamDetection);
		$newJoinStreamDetection = array();
		$somethingDone = false;
		foreach ( $joinStreamDetection as $index => $detection ) {
			$nodeID = $detection["currentNode"];
			if ( !is_null($nodeID) ) {
				$joinStream = $detection["joinStream"];
				$memory = $detection["nodeMemory"];
				if ( $this->epc->isJoin($nodeID) ) {
					array_push($joinStream, $nodeID);
				}
				array_push($memory, $nodeID);
				$succ = $this->epc->getSuccessor($nodeID);
				if ( count($succ) == 0 || in_array($nodeID, $detection["nodeMemory"]) ) {
					$newDetection = array(
							"currentNode" => null,
							"joinStream"  => $joinStream,
							"nodeMemory"  => $memory
					);
					array_push($newJoinStreamDetection, $newDetection);
					$somethingDone = true;
				} else {
					foreach ( $succ as $succID ) {
						$newDetection = array(
								"currentNode" => $succID,
								"joinStream"  => $joinStream,
								"nodeMemory"  => $memory
						);
						array_push($newJoinStreamDetection, $newDetection);
						$somethingDone = true;
					}
				}
			} else {
				array_push($newJoinStreamDetection, $detection);
			}
		}
		if ( $somethingDone ) {
			return $this->getJoinStream($newJoinStreamDetection);
		} else {
			return $newJoinStreamDetection;
		}
	}

	/**
	 * Ermittelt den Typ des JOIN-Konnektor, in welchen die Pfade zusammenlaufen (falls dieser existiert)
	 *
	 * @param array $startNodes
	 * @return or, xor, and, null
	 */
	private function getFirstCommonJoin($startNodes) {
		$startNodeIDs = array_keys($startNodes);
		$nodeID = end($startNodeIDs);
		//print("---".$nodeID."---");
		unset($startNodes[$nodeID]);
		//print_r($startNodes);
		foreach ( $this->joinStreams[$nodeID] as $index => $joinStream ) {
			foreach ( $joinStream as $joinNodeID ) {
				$allPathesReach = true;
				foreach ( $startNodes as $startNodeID => $label ) {
					if ( !$this->startNodeReachesJoin($startNodeID, $joinNodeID) ) {
						//print("Not Reaches: ".$startNodeID." ".$joinNodeID."\n");
						$allPathesReach = false;
					} else {
						//print("Reaches: ".$startNodeID." ".$joinNodeID."\n");
					}
				}
				if ( $allPathesReach ) return $this->epc->getType($joinNodeID);
			}
		}
		return null;
	}
	
	private function startNodeReachesJoin($nodeID, $searchJoinNodeID) {
		foreach ( $this->joinStreams[$nodeID] as $index => $joinStream ) {
			if ( in_array($searchJoinNodeID, $joinStream) ) return true;
		}
		return false;
	}

	private function extractContextFromState($state) {
		$context = array();
		foreach ( $state as $edgeID => $value ) {
			if ( $value == 1 ) $context[$edgeID] = "wait";
			if ( $value == -1 ) $context[$edgeID] = "dead";
		}
		return $context;
	}

}
?>