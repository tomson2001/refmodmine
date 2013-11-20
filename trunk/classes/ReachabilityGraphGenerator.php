<?php 
/**
 * EPKs duerfen nicht mit Konnektoren beginnen
 *
 * @author t.thaler
 *
 */
class ReachabilityGraphGenerator {

	public $epc;
	public $reachabilityGraph;
	public $joinStreams;
	public $max_execution_time;

	public $syntaxErrors = array();
	private $debugging = true;
	private $debugCounter = 1;
	private $debugLevel = "LOG"; // LOG / DEBUG / ALL

	public function __construct(EPC $epc, $max_execution_time = 0) {
		$this->reachabilityGraph = new ReachabilityGraph($epc);
		$this->epc = $epc;
		$this->max_execution_time = $max_execution_time;
	}

	public function execute() {
		$initialStates = $this->computeInitialStates();
		foreach ( $initialStates as $initialState ) {
			$this->computeNextStates($initialState);
		}
		if ( empty($this->syntaxErrors) ) {
			return $this->reachabilityGraph;
		} else {
			return implode("\n", $this->syntaxErrors);
		}
	}

	public function computeInitialStates() {
		$startNodes = $this->epc->getAllStartNodes();
		$this->computeJoinStreams($startNodes);
		$firstCommonJoin = $this->getFirstCommonJoin($startNodes);

		if ( count($startNodes) == 1 || $firstCommonJoin == "xor" || $firstCommonJoin == "or" ) {
			// Iteration ueber alle Startknoten
			foreach ( $startNodes as $startNodeId => $label ) {
				// Initial State erstellen
				$tokens = array();
				$successors = $this->epc->getSuccessor($startNodeId);
				$successor = $successors[0];
				array_push($tokens, array($startNodeId => $successor));
				$state = new State($tokens);
				$this->reachabilityGraph->addState($state);
			}
		}

		// Falls mehrere Startknoten, dann betrachte das
		if ( count($startNodes) > 1 ) {

			/**
			 * Annahme: Pfade fuehren ueber ein AND-Join zusammen
			 */
			if ( $firstCommonJoin == "and" || $firstCommonJoin == "or" ) {

				// Initial State erstellen
				$tokens = array();
				foreach ( $startNodes as $startNodeId => $label ) {
					$successors = $this->epc->getSuccessor($startNodeId);
					$successor = $successors[0];
					array_push($tokens, array($startNodeId => $successor));
				}
				$state = new State($tokens);
				$this->reachabilityGraph->addState($state);

			}

			/**
			 * Annahme: Pfade laufen ueber einen OR-Join zusammen -> Moeglichkeiten durchpermutieren
			 */
			$countStartNodes = count($startNodes);
			if ( $countStartNodes > 2 ) {
				// Alle Permutation der Laengen 2 bis Anzahl der Nachfolger-1 (bereits durch AND behandelt) berechnen und jeweils ein AND-Segement oeffnen
				for ( $i=2; $i<$countStartNodes; $i++ ) {
					$permutations = $this->getSplitORPermutations(array_keys($startNodes), $i);
					foreach ( $permutations as $permutation ) {
						$firstCommonJoin = $this->getFirstCommonJoin(array_flip($permutation));

						// Initial State erstellen
						$tokens = array();
						foreach ( $permutation as $permNodeId ) {
							$permNodeSuccs = $this->epc->getSuccessor($permNodeId);
							array_push($permutationSuccessors, $permNodeSucc);
							array_push($tokens, array($permNodeId => $permNodeSucc));
						}
						$state = new State($tokens);
						$this->reachabilityGraph->addState($state);
					}
				}
			}
		}
		return $this->reachabilityGraph->states;
	}

	public function computeNextStates(State $state) {
		//$this->debug($state, "Handle state...");
		foreach ( $state->tokens as $index => $token ) {
			foreach ( $token as $source => $target ) {

				// Typ des Zielknotens ermitteln
				$targetType = $this->epc->getType($target);

				// Nachfolgeknoten ermitteln
				$predecessors = $this->epc->getPredecessor($target);
				$successors = $this->epc->getSuccessor($target);

				// Wenn Keine Nachfolgeknoten vorhanden sind, dann handelt es sich um eine Senke. Extraktion kann beendet werden
				if ( empty($successors) ) {
					$this->debug($state, "End reached");
					continue;
				}

				// Wenn Zielknoten eine Funktion oder ein Ereignis, dann darf nur genau ein Nachfolgeknoten enthalten sein
				if ( $targetType == "function" || $targetType == "event" || $this->epc->isXORJoin($target) ) {
					// Neuen Zustand erstellen und rekursiv weitermachen
					$successor = $successors[0];
					$tokens = $state->tokens;
					$tokens[$index] = array($target => $successor);
					$newState = new State($tokens);
					if ( $this->reachabilityGraph->containsState($newState) ) {
						$this->debug($newState, "(Func/Event/XOR-Join) State already exists", "LOG");
						$this->reachabilityGraph->addTransition($state, $newState, $this->epc->getNodeLabel($target));
						continue;
					} else {
						$this->reachabilityGraph->addState($newState);
						$this->debug($newState, "(Func/Event/XOR-Join) State added", "LOG");
						$this->reachabilityGraph->addTransition($state, $newState, $this->epc->getNodeLabel($target));
						$this->computeNextStates($newState);
					}
				}

				// Wenn Zielknoten ein XOR-Split, dann fuer alle ausgehende Kanten einen Zustand erstellen
				if ( $this->epc->isXORSplit($target) ) {
					foreach ( $successors as $successor ) {
						// Neuen Zustand erstellen und rekursiv weitermachen
						$tokens = $state->tokens;
						$tokens[$index] = array($target => $successor);
						$newState = new State($tokens);
						if ( $this->reachabilityGraph->containsState($newState) ) {
							$this->debug($newState, "(XOR-Split) State already exists", "LOG");
							$this->reachabilityGraph->addTransition($state, $newState, $this->epc->getNodeLabel($target));
							continue;
						} else {
							$this->reachabilityGraph->addState($newState);
							$this->debug($newState, "(XOR-Split) State added", "LOG");
							$this->reachabilityGraph->addTransition($state, $newState, $this->epc->getNodeLabel($target));
							$this->computeNextStates($newState);
						}
					}
				}

				// Wenn Zielknoten ein AND-Split, dann aktiviere alle ausgehenden Kanten
				if ( $this->epc->isANDSplit($target) ) {
					$tokens = $state->tokens;
					unset($tokens[$index]);
					foreach ( $successors as $successor ) {
						array_push($tokens, array($target => $successor));
					}
					$newState = new State($tokens);
					if ( $this->reachabilityGraph->containsState($newState) ) {
						$this->debug($newState, "(AND-Split) State already exists", "LOG");
						$this->reachabilityGraph->addTransition($state, $newState, $this->epc->getNodeLabel($target));
						continue;
					} else {
						$this->reachabilityGraph->addState($newState);
						$this->debug($newState, "(AND-Split) State added", "LOG");
						$this->reachabilityGraph->addTransition($state, $newState, $this->epc->getNodeLabel($target));
						$this->computeNextStates($newState);
					}
				}

				// Wenn Zielknoten ein AND-Join, dann nur weitermachen, wenn alle eingehenden Kanten einen Token haben
				if ( $this->epc->isANDJoin($target) ) {
					$requiredTokens = array();
					foreach ( $predecessors as $predecessor ) {
						array_push($requiredTokens, array($predecessor => $target));
					}
					if ( $state->contains($requiredTokens) ) {
						// AND-Joins duerfen nur genau eine ausgehende Kante haben
						$successor = $successors[0];
						$tokens = $state->tokens;
						$newState = new State($tokens);
						$newState->removeTokens($requiredTokens);
						$newState->addToken(array($target => $successor));
						if ( $this->reachabilityGraph->containsState($newState) ) {
							$this->debug($newState, "(AND-Join) State already exists", "LOG");
							$this->reachabilityGraph->addTransition($state, $newState, $this->epc->getNodeLabel($target));
							continue;
						} else {
							$this->reachabilityGraph->addState($newState);
							$this->debug($newState, "(AND-Join) State added", "LOG");
							$this->reachabilityGraph->addTransition($state, $newState, $this->epc->getNodeLabel($target));
							$this->computeNextStates($newState);
						}
					}
				}

			}
		}
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

	private function error($nodeID, $description) {
		$nodeLabel = $this->epc->getNodeLabel($nodeID);
		array_push($this->syntaxErrors, "Syntax Error near ".$nodeLabel." (".$nodeID."): ".$description);
	}

	private function debug($state, $description, $level="LOG") {
		if ( $this->debugging && ($level == $this->debugLevel || $this->debugLevel == "ALL" )) {
			print("\n ".$this->debugCounter.". ".$state->id.": ".$description);
			$this->debugCounter++;
		}
	}

}
?>