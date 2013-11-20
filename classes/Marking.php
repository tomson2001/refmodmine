<?php
/**
 * Beschreibt ein Marking entsprechend der Definition aus der Mendling-Diss, S. 69
 * - State besteht aus Token auf Kanten {-1,0,+1}
 * - context weist jeder Kante {wait,dead} zu
 *
 * @author t.thaler
 */
class Marking {

	public $epc;
	public $state;
	public $context;

	public $label; // Enthaelt das Label des Knoten, durch welchen diese Markierung erreicht wurde

	public function __construct(EPC &$epc, $state, $context) {
		$this->epc = &$epc;
		$this->state = $state;
		$this->context = $context;
	}

	public function computeNextMarkings() {
		$this->propagateDeadContext();
		$this->propagateWaitContext();
		$this->propagateNegativeTokens();
		return $this->propagatePositiveTokens();
	}

	private function propagateDeadContext() {
		foreach ($this->context as $edgeID => $value) {
			if ( $value == "dead" ) $this->recursiveDeadContextPropagation($edgeID);
		}
	}

	/**
	 * Mendling Diss, S. 81
	 * @param unknown_type $edgeID
	 */
	private function recursiveDeadContextPropagation($edgeID) {
		$edge = $this->epc->edges[$edgeID];
		foreach ( $edge as $sourceID => $targetID ) { // genau eine Iteration
			$successors = $this->epc->getSuccessor($targetID);
			foreach ( $successors as $successorID ) { // Iteration ueber alle Folgekanten
				$subsequentEdgeID = $this->epc->edgeExists($targetID, $successorID);
				if ( array_key_exists($subsequentEdgeID, $this->context) && $this->context[$subsequentEdgeID] != "dead" && !array_key_exists($subsequentEdgeID, $this->state) ) {
					$this->context[$subsequentEdgeID] = "dead";
					$this->recursiveDeadContextPropagation($subsequentEdgeID);
				}
			}
		}
	}

	private function propagateWaitContext() {
		foreach ($this->context as $edgeID => $value) {
			if ( $value == "wait" ) $this->recursiveWaitContextPropagation($edgeID);
		}
	}

	private function recursiveWaitContextPropagation($edgeID) {
		$edge = $this->epc->edges[$edgeID];
		foreach ( $edge as $sourceID => $targetID ) { // genau eine Iteration

			/**
			 * 1. Fall: Mendling Diss, S. 82
			 *
			 * Wenn der Zeilknoten zur Kategorie Funktion, Ereignis, Split oder AND-Join gehoert und alle
			 * eingehenden Kanten im wait-Kontext sind, dann wird der Wait-Kontext auf alle nachfolgenden
			 * Kanten uebertragen, die einen dead-Kontext haben und keinen State-Token tragen.
			 */
			if ( $this->epc->isFunction($targetID) || $this->epc->isEvent($targetID) || $this->epc->isSplit($targetID) || $this->epc->isANDJoin($targetID) ) {

				// Pruefen, ob alle eingehenden Kanten im wait-Kontext sind
				$areAllIngoingEdgesInWaitContext = true;
				$predecessors = $this->epc->getPredecessor($targetID);
				foreach ( $predecessors as $predecessor ) {
					$subsequentEdgeID = $this->epc->edgeExists($predecessor, $targetID);
					if ( !(array_key_exists($subsequentEdgeID, $this->context) && $this->context[$subsequentEdgeID] == "wait") ) {
						$areAllIngoingEdgesInWaitContext = false;
						break;
					}
				}

				// Auf alle nachfolgenden Kanten im dead-Kontext uebertragen, die keinen State-Token tragen
				if ( $areAllIngoingEdgesInWaitContext ) {
					$successors = $this->epc->getSuccessor($targetID);
					foreach ( $successors as $successorID ) { // Iteration ueber alle Folgekanten
						$subsequentEdgeID = $this->epc->edgeExists($targetID, $successorID);
						if ( !array_key_exists($subsequentEdgeID, $this->state) ) {
							$this->context[$subsequentEdgeID] = "wait";
							$this->recursiveWaitContextPropagation($subsequentEdgeID);
						}
					}
				}

			}

			/**
			 * 2. Fall: Mendling Diss, S. 83
			 *
			 * Wenn der Zielknoten zur Kategorie XOR-Join oder OR-Join gehoert und eine der eigehenden Kanten
			 * wait-Kontext hat, dann wird dieser auf die ausgehende Kante im dead-Kontext uebertragen
			 */
			if ( $this->epc->isXORJoin($targetID) || $this->epc->isORJoin($targetID) ) {

				// Pruefen, ob eine der eingehenden Kanten im wait-Kontext ist
				$isOneIngoingEdgeInWaitContext = false;
				$predecessors = $this->epc->getPredecessor($targetID);
				foreach ( $predecessors as $predecessor ) {
					$edgeID = $this->epc->edgeExists($predecessor, $targetID);
					if ( !array_key_exists($edgeID, $this->context) || (array_key_exists($edgeID, $this->context) && $this->context[$edgeID] == "wait" ) ) {
						$isOneIngoingEdgeInWaitContext = true;
						break;
					}
				}

				// Auf alle nachfolgenden Kanten uebertragen im Dead-Kontext uebertragen
				if ( $isOneIngoingEdgeInWaitContext ) {
					$successors = $this->epc->getSuccessor($targetID);
					$successorID = $successors[0];
					$subsequentEdgeID = $this->epc->edgeExists($targetID, $successorID);
					if ( array_key_exists($subsequentEdgeID, $this->context) && $this->context[$subsequentEdgeID] == "dead" ) {
						$this->context[$subsequentEdgeID] = "wait";
						$this->recursiveWaitContextPropagation($subsequentEdgeID);
					}
				}

			}
		}
	}

	private function propagateNegativeTokens() {
		foreach ( $this->state as $edgeID => $tokenValue ) {
			if ( $tokenValue == -1 ) $this->recursiveNegativeTokenPropagation($edgeID);
		}
	}

	/**
	 * Mendling Diss., S. 84
	 *
	 * Wenn alle zu einem Knoten eigehenden Kanten einen negativen Token tragen und alle ausgehenden
	 * Kanten keinen negativen oder positiven Token tragen, dann konsumiere alle negative Token der
	 * eingehenden Kanten und produziere negative Token auf allen ausgehenden Kanten.
	 *
	 * @param unknown_type $edgeID
	 */
	private function recursiveNegativeTokenPropagation($edgeID) {
		$edge = $this->epc->edges[$edgeID];
		foreach ( $edge as $sourceID => $targetID ) { // genau eine Iteration
			$doAllIngoingEdgesHaveNegativeToken = true;
			$doAllOutgoingEdgesHaveNoNegativeOrPositiveToken = true;

			$ingoingEdgeIDs = array();
			$outgoingEdgeIDs = array();

			// Pruefen, ob alle eingehenden Kanten einen negativen Token tragen
			$predecessors = $this->epc->getPredecessor($targetID);
			foreach ( $predecessors as $predecessor ) {
				$edgeID = $this->epc->edgeExists($predecessor, $targetID);
				array_push($ingoingEdgeIDs, $edgeID);
				if ( !(array_key_exists($edgeID, $this->state) && $this->state[$edgeID] == -1) ) {
					$doAllIngoingEdgesHaveNegativeToken = false;
					break;
				}
			}

			// Pruefen, ob alle ausgehenden Kanten unbelegt sind
			if ( $doAllIngoingEdgesHaveNegativeToken ) {
				$successors = $this->epc->getSuccessor($targetID);
				foreach ( $successors as $successorID ) { // Iteration ueber alle Folgekanten
					$subsequentEdgeID = $this->epc->edgeExists($targetID, $successorID);
					array_push($outgoingEdgeIDs, $subsequentEdgeID);
					if ( array_key_exists($subsequentEdgeID, $this->state) ) {
						$doAllOutgoingEdgesHaveNoNegativeOrPositiveToken = false;
						break;
					}
				}
			}

			// Wenn alles ok, dann schalten
			if ( $doAllIngoingEdgesHaveNegativeToken && $doAllOutgoingEdgesHaveNoNegativeOrPositiveToken ) {
					
				// Negative Token auf eingehenden Kanten konsumieren
				foreach ( $ingoingEdgeIDs as $edgeID ) {
					unset($this->state[$edgeID]);
				}
					
				// Negative Token auf allen ausgehenden Kanten produzierten
				foreach ( $outgoingEdgeIDs as $edgeID ) {
					$this->state[$edgeID] = -1;
					$this->recursiveNegativeTokenPropagation($edgeID);
				}
			}
		}

	}

	/**
	 * Mendling Diss., S. 86f
	 */
	private function propagatePositiveTokens() {
		$propagatedMarkings = array();
		foreach ( $this->state as $edgeID => $tokenValue ) {
			if ( $tokenValue == 1 ) {
				$edge = $this->epc->edges[$edgeID];
				foreach ( $edge as $sourceID => $targetID ) { // genau eine Iteration

					/**
					 * 1. Fall: Funktion, Ereignis, AND-Konnektor
					 *
					 * Positive Token auf allen eingehenden Kanten werden konsumiert und es wird jeweils
					 * ein Marking fuer jede Ausgangskante erstellt, wenn alle davon unbelegt sind.
					 * Der Input-Kontext wird jeweils auf dead und der Outputkontext auf wait gesetzt.
					 */
					if ( $this->epc->isFunction($targetID) || $this->epc->isEvent($targetID) || $this->epc->isANDJoin($targetID) || $this->epc->isANDSplit($targetID) ) {
						$doAllIngoingEdgesHavePositiveToken = true;
						$doAllOutgoingEdgesAreEmpty = true;

						$ingoingEdgeIDs = array();
						$outgoingEdgeIDs = array();

						// Pruefen, ob alle eingehenden Kanten einen positiven Token tragen
						$predecessors = $this->epc->getPredecessor($targetID);
						foreach ( $predecessors as $predecessor ) {
							$edgeID = $this->epc->edgeExists($predecessor, $targetID);
							array_push($ingoingEdgeIDs, $edgeID);
							if ( !(array_key_exists($edgeID, $this->state) && $this->state[$edgeID] == 1) ) {
								$doAllIngoingEdgesHavePositiveToken = false;
								break;
							}
						}

						// Pruefen, ob alle ausgehenden Kanten unbelegt sind
						if ( $doAllIngoingEdgesHavePositiveToken ) {
							$successors = $this->epc->getSuccessor($targetID);
							foreach ( $successors as $successorID ) { // Iteration ueber alle Folgekanten
								$subsequentEdgeID = $this->epc->edgeExists($targetID, $successorID);
								array_push($outgoingEdgeIDs, $subsequentEdgeID);
								if ( array_key_exists($subsequentEdgeID, $this->state) ) {
									$doAllOutgoingEdgesAreEmpty = false;
									break;
								}
							}
						}

						// Wenn alles ok, dann schalten
						if ( $doAllIngoingEdgesHavePositiveToken && $doAllOutgoingEdgesAreEmpty && !$this->epc->isEndNode($targetID) ) {

							$newMarking = new Marking($this->epc, $this->state, $this->context);

							// Positive Token von allen eingehenden Kanten konsumieren
							foreach ( $ingoingEdgeIDs as $inEdgeID ) {
								unset($newMarking->state[$inEdgeID]);
								$newMarking->context[$inEdgeID] = "dead";
							}

							// Positive Token auf allen ausgehenden Kanten produzierten
							foreach ( $outgoingEdgeIDs as $outEdgeID ) {
								$newMarking->state[$outEdgeID] = 1;
								$newMarking->context[$outEdgeID] = "wait";
							}

							$newMarking->label = $this->epc->getNodeLabel($targetID);

							array_push($propagatedMarkings, $newMarking);
						}
					}

					/**
					 * 2. Fall: XOR-Konnektor
					 *
					 * Ein Input-Token wird von einer Eingangskante konsumiert und auf einer Ausgangskante
					 * generiert, sofern alle Ausgangskanten unbelegt sind. Die entsprechende Eingangskante wird
					 * auf dead gesetzt, ebenso wie die Ausgangskanten, die den Token nicht erhalten haben.
					 * Die Ausgangskante mit dem positiven Token erhaelt einen wait-Kontext.
					 */
					if ( $this->epc->isXor($targetID) ) {

						$doAllOutgoingEdgesAreEmpty = true;

						$outgoingEdgeIDs = array();

						// Pruefen, ob alle ausgehenden Kanten unbelegt sind
						$successors = $this->epc->getSuccessor($targetID);
						foreach ( $successors as $successorID ) { // Iteration ueber alle Folgekanten
							$subsequentEdgeID = $this->epc->edgeExists($targetID, $successorID);
							array_push($outgoingEdgeIDs, $subsequentEdgeID);
							if ( array_key_exists($subsequentEdgeID, $this->state) ) {
								$doAllOutgoingEdgesAreEmpty = false;
								break;
							}
						}

						// Wenn alles ok, fuer jede moegliche Schaltung ein neues Marking erstellen
						if ( $doAllOutgoingEdgesAreEmpty ) {
							$outgoingEdgeIDs_tmp = $outgoingEdgeIDs;
							foreach ( $outgoingEdgeIDs as $outEdgeID ) {
								$newMarking = new Marking($this->epc, $this->state, $this->context);

								// Positiven Token von der eigenden Kante konsumieren
								unset($newMarking->state[$edgeID]);
								$newMarking->context[$edgeID] = "dead";

								// Positiven Token auf die zu aktivierende ausgehende Kante setzen
								$newMarking->state[$outEdgeID] = 1;
								$newMarking->context[$outEdgeID] = "wait";

								// Alle anderen ausgehenden Kanten auf dead-Kontext setzen
								foreach ( $outgoingEdgeIDs_tmp as $outEdgeID_tmp ) {
									if ( $outEdgeID != $outEdgeID_tmp ) $newMarking->context[$outEdgeID_tmp] = "dead";
								}

								$newMarking->label = $this->epc->getNodeLabel($targetID);

								array_push($propagatedMarkings, $newMarking);
							}
						}

					}

					/**
					 * 3. Fall: OR-Splits
					 *
					 * Der positive Knoten der Eingangskante wird konsumiert und es werden die entsprechenden
					 * Kombinationen aus positiven und negativen Token auf den Ausgangskanten produziert, sodass mindestens
					 * ein positiver Token vorhanden ist. Jede ausgehende Kante mit positivem Token erhaelt einen
					 * wait-Kontext, waehrend alle anderen einen dead-kontext bekommen.
					 */
					if ( $this->epc->isORSplit($targetID) ) {

						$doAllOutgoingEdgesAreEmpty = true;
						
						$outgoingEdgeIDs = array();

						// Pruefen, ob alle ausgehenden Kanten unbelegt sind
						$successors = $this->epc->getSuccessor($targetID);
						foreach ( $successors as $successorID ) { // Iteration ueber alle Folgekanten
							$subsequentEdgeID = $this->epc->edgeExists($targetID, $successorID);
							array_push($outgoingEdgeIDs, $subsequentEdgeID);
							if ( array_key_exists($subsequentEdgeID, $this->state) ) {
								$doAllOutgoingEdgesAreEmpty = false;
								break;
							}
						}

						if ( $doAllOutgoingEdgesAreEmpty ) {
							// Alle moeglichen Nachfolgekombinationen berechnen und entsprechendes Marking erstellen
							$successors = $this->epc->getSuccessor($targetID);
							$numOfSuccessors = count($successors);

							for ( $i=1; $i<=$numOfSuccessors; $i++ ) {
								$permutations = $this->getSplitORPermutations($successors, $i);
								foreach ( $permutations as $permutation ) {
									//print("Length = ".$i.": ".implode("|", $permutation)."\n");
									$newMarking = new Marking($this->epc, $this->state, $this->context);

									// Positiven Token von der Eingangskante konsumieren und dead-Kontext setzen
									unset($newMarking->state[$edgeID]);
									$newMarking->context[$edgeID] = "dead";

									// Positive und negative Token auf nachfolgende Kanten legen und Kontext setzen
									foreach ( $successors as $nodeID ) {
										$subsequentEdgeID = $this->epc->edgeExists($targetID, $nodeID);
										if ( in_array($nodeID, $permutation) ) {
											$newMarking->state[$subsequentEdgeID] = 1;
											$newMarking->context[$subsequentEdgeID] = "wait";
										} else {
											$newMarking->state[$subsequentEdgeID] = -1;
											$newMarking->context[$subsequentEdgeID] = "dead";
										}
									}
										
									$newMarking->label = $this->epc->getNodeLabel($targetID);

									array_push($propagatedMarkings, $newMarking);
								}
							}
						}

					}

					/**
					 * 4. Fall: OR-Joins
					 *
					 * OR-Joins feuern, wenn entweder alle Eingangskanten nicht leer sind und einer von ihnen einen
					 * positiven Token hat, oder wenn keine unbelegte Eingangskante mit wait-Kontext existiert und
					 * zugleich mindestens ein positiver Token auf den Eingangsknoten vorhanden ist. Es werden alle
					 * Token auf den Eingangskanten konsumiert. Die Eingangsknoten werden auf dead gesetzt. Es wird
					 * ein positiver Token auf der Ausgangskante produziert und und diese Kante auf wait gesetzt.
					 */
					if ( $this->epc->isORJoin($targetID) ) {
						$doAllIngoingEdgesHaveToken = true;
						$doOneIngoingEdgeHasPositiveToken = false;
						$doNoIngoingEdgeWithoutTokenHasWaitContext = true;

						$ingoingEdgeIDs = array();

						/**
						 * 1. Pruefen, ob alle eingehenden Kanten einen Token tragen
						 * 2. Pruefen, ob eine eingehende Kante mit positiven Token existiert
						 * 3. Pruefen, ob eine eingehende unbelegte Kante mit wait-Kontext existiert
						*/
						$predecessors = $this->epc->getPredecessor($targetID);
						foreach ( $predecessors as $predecessor ) {
							$predEdgeID = $this->epc->edgeExists($predecessor, $targetID);
							array_push($ingoingEdgeIDs, $predEdgeID);
							if ( !array_key_exists($predEdgeID, $this->state) ) $doAllIngoingEdgesHaveToken = false;
							if ( array_key_exists($predEdgeID, $this->state) && $this->state[$predEdgeID] == 1 ) $doOneIngoingEdgeHasPositiveToken = true;
							if ( !array_key_exists($predEdgeID, $this->state) && array_key_exists($predEdgeID, $this->context) && $this->context[$predEdgeID] == "wait" ) $doNoIngoingEdgeWithoutTokenHasWaitContext = false;
						}

						// Wenn o.g. Bedingung erfüllt dann feuern
						$firstCondition = $doAllIngoingEdgesHaveToken && $doOneIngoingEdgeHasPositiveToken;
						$secondCondition = $doNoIngoingEdgeWithoutTokenHasWaitContext && $doOneIngoingEdgeHasPositiveToken;
						if (  $secondCondition ) {
							
// 							print("\nvorher\n");
// 							foreach ( $this->state as $tokenID => $tokenValue ) {
// 								$edge = $this->epc->edges[$tokenID];
// 								foreach ( $edge as $source => $target ) {
// 									print("\n".$this->epc->getNodeLabel($source)." => ".$this->epc->getNodeLabel($target).": ".$tokenValue);
// 								}
// 							}
// 							print("\nContext\n");
// 							foreach ( $this->context as $tokenID => $tokenValue ) {
// 								$edge = $this->epc->edges[$tokenID];
// 								foreach ( $edge as $source => $target ) {
// 									print("\n".$this->epc->getNodeLabel($source)." => ".$this->epc->getNodeLabel($target).": ".$tokenValue);
// 								}
// 							}
								
							$newMarking = new Marking($this->epc, $this->state, $this->context);
								
							// Alle Token auf den eingehenden Kanten konsumieren und dead-Kontext setzen
							foreach ( $ingoingEdgeIDs as $inEdgeID ) {
								if ( array_key_exists($inEdgeID, $newMarking->state) ) {
									unset($newMarking->state[$inEdgeID]);
									$newMarking->context[$inEdgeID] = "dead";
								}
							}
								
							// Positiver Token auf Ausgangskante produzieren und auf wait setzen
							$successors = $this->epc->getSuccessor($targetID);
							$successorID = $successors[0];
							$subsequentEdgeID = $this->epc->edgeExists($targetID, $successorID);
							$newMarking->state[$subsequentEdgeID] = 1;
							$newMarking->context[$subsequentEdgeID] = "wait";
								
							$newMarking->label = $this->epc->getNodeLabel($targetID);
// 							print("\nnachher\n");
// 							print_r($newMarking->state);
// 							print_r($newMarking->context);
								
							array_push($propagatedMarkings, $newMarking);
						}
					}

				}
			}
		}
		return $propagatedMarkings;
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
	 * Konvertiert ein Marking zu einem Zustand eines Erreichbarkeitsgraphen.
	 * Ein solcher Zustand ist ausschließlich ueber aktivierte Kanten definiert.
	 * Siehe dazu Mendling Diss., Seite 79
	 *
	 * @return State
	 */
	public function convertToState() {
		//print("\n----\n");
		//print_r($this->state);
		//print_r($this->context);
		$tokens = array();
		foreach ( $this->state as $edgeID => $value ) {
			if ( $value == 1 ) array_push($tokens, $this->epc->edges[$edgeID]);
		}
		return new State($tokens);
	}
	
	public function equals(Marking $marking) {
		return $marking->state == $this->state && $marking->context == $this->context;
	} 

}
?>