<?php
/**
 * Klasse zur Extraktion aller moeglichen Traces zu einer EPK.
 *
 * Zulaessig sind:
 * - EPKs mit mehreren Startknoten
 * - EPKs mit mehreren Endknoten
 *
 * Weitere Hinweise:
 * - Im Trace sind ausschliesslich Funktionen enthalten (Ereignisse werden verworfen, da nicht fuer die Ausfuehrung relevant)
 * - XOR Konnektor wird ordnungsgemaess beruecksichtigt
 * - Jeder Rueckschritt (Schleife) wird nur genau einmal durchlaufen
 * - AND-Joins werden als Synchronisationspunkte interpretiert
 * - OR wird nach XOR und AND konvertiert und korrekt behandelt
 *
 * @TODO
 * - Behandlung des Falls, wenn mehrere Startknoten existieren, die irgendwann in einen AND/OR-Join laufen (Erkennung der anfaenglichen parallelitaet)
 * - Behandlung planarer EPKs
 *
 * @author t.thaler
 *
 */
class TraceExtractor {

	public $epc;
	public $traceExtraction = array();
	public $traces;

	// Subtraces zwischen zwei ORs
	public $orSubTraces = array();

	// Subtraces zwischen zwei XORs
	public $xorSubTraces = array();

	/**
	 * Konstruktur
	 *
	 * Hier werden bereits alle Ereignisse aus der EPK entfernt,
	 * da diese nicht fuer die Ausfuehrung relevant sind.
	 *
	 * @param EPC $epc
	*/
	public function __construct(EPC $epc) {
		$transformer = new EPCTransformerNoEventsButEnds();
		$epc = $transformer->transform($epc);
		$epc->exportEPML();
		$this->epc = $epc;
		$this->getORSubtraces();
		$this->getXORSubtraces();

		//print("OsSubTraces");
		//print_r($this->orSubTraces);
		//print_r($this->xorSubTraces);
	}

	/**
	 * Stoesst die Extraktion der Traces an
	 *
	 * @param EPC $this->epc
	 * @return $traces / Error-Meldung
	 */
	public function execute() {
		// Startknoten suchen
		$startNodes = $this->epc->getAllStartNodes();
		if ( empty($startNodes) ) return false;

		$traces = array();

		// Iteration ueber alle Startknoten
		foreach ( $startNodes as $startNodeId => $label ) {

			// Falls Startknoten eine Funktion ist, dass schreib diese gleich in den Trace
			$trace = $this->epc->getType($startNodeId) == "function" ? array($startNodeId) : array();

			// Aufbau der benoetigten Extraktionsstruktur
			$traceExtraction = array( // detection
					array(
							'trace' => $trace,
							'currentNode' => $startNodeId,
							'backEdges' => array(),
							'connectorMemory' => array(),
							'todoMemory' => array(),
							'todoMemorySubTraces' => array()
					)
			);

			// Rekursive Trace-Extraktion
			$traceExtrationResult = $this->continueTraceExtraction($traceExtraction);
			if ( is_string($traceExtrationResult) ) return $traceExtrationResult;

			//print_r($this->traceExtraction);

			// Gefundene Traces herauslesen
			foreach ( $traceExtrationResult as $traceExtractionIndex => $detection ) {
				if ( !in_array($detection['trace'], $traces) ) array_push($traces, $detection['trace']);
			}

		}

		// Falls mehrere Startknoten, dann betrachte das
		if ( count($startNodes) > 1 ) {
			/**
			 * Annahme: Pfade fuehren ueber ein AND-Join zusammen
			 */
			$traceExtraction = array( // detection
					array(
							'trace' => array(),
							'currentNode' => null,
							'backEdges' => array(),
							'connectorMemory' => array(),
							'todoMemory' => array("start" => array_keys($startNodes)),
							'todoMemorySubTraces' => array("start" => array())
					)
			);

			// Rekursive Trace-Extraktion
			$traceExtrationResult = $this->continueTraceExtraction($traceExtraction);
			if ( is_string($traceExtrationResult) ) return $traceExtrationResult;

			//print_r($this->traceExtraction);

			// Gefundene Traces herauslesen
			foreach ( $traceExtrationResult as $traceExtractionIndex => $detection ) {
				if ( !in_array($detection['trace'], $traces) ) array_push($traces, $detection['trace']);
			}



			/**
			 * Annahme: Pfade laufen ueber einen OR-Join zusammen -> Moeglichkeiten durchpermutieren
			 */
			$countStartNodes = count($startNodes);
			if ( $countStartNodes > 2 ) {
				// Alle Permutation der Laengen 2 bis Anzahl der Nachfolger-1 (bereits durch AND behandelt) berechnen und jeweils ein AND-Segement oeffnen
				for ( $i=2; $i<$countStartNodes; $i++ ) {
					$possibleTodoMemories = $this->getSplitORPermutations(array_keys($startNodes), $i);
					foreach ( $possibleTodoMemories as $todoMemory ) {
						$traceExtraction = array( // detection
								array(
										'trace' => array(),
										'currentNode' => null,
										'backEdges' => array(),
										'connectorMemory' => array(),
										'todoMemory' => array("start" => $todoMemory),
										'todoMemorySubTraces' => array("start" => array()),
										'startTraceXORSplitAvailable' => array()
								)
						);
							
						// Rekursive Trace-Extraktion
						$traceExtrationResult = $this->continueTraceExtraction($traceExtraction);
						if ( is_string($traceExtrationResult) ) return $traceExtrationResult;
							
						//print_r($this->traceExtraction);
							
						// Gefundene Traces herauslesen
						foreach ( $traceExtrationResult as $traceExtractionIndex => $detection ) {
							if ( !in_array($detection['trace'], $traces) ) array_push($traces, $detection['trace']);
						}
					}
				}
			}
		}

		$onlyFuncTraces = $this->onlyFunctions($traces);
		$onlyFuncStringTraces = array();
		foreach ( $onlyFuncTraces as $trace ) {
			$stringTrace = implode(";", $trace);
			if ( !in_array($stringTrace, $onlyFuncStringTraces) ) array_push($onlyFuncStringTraces, $stringTrace);
		}

		unset($onlyFuncTraces);
		$onlyFuncTraces = array();
		foreach ( $onlyFuncStringTraces  as $stringTrace ) {
			array_push($onlyFuncTraces, explode(";", $stringTrace));
		}

		$this->traces = $onlyFuncTraces;
		//print("___ENDE___");
		//print_r($traces);
		return $this->traces;
	}

	/**
	 * Rekursive Trace-Extraktion
	 *
	 * @param Array $traceExtration
	 * @return Array
	 */
	private function continueTraceExtraction($traceExtration) {

		//print("\ncontinueTraceExtraction");
		//print_r($traceExtration);

		$somethingDone = false;
		$newTraceExtraction = array();

		$joinConnectors = $this->epc->getAllJoinConnectors($this->epc->getAllConnectors());

		// Iteration ueber bisher extrahierte Trace-Segemente
		foreach ( $traceExtration as $traceExtractionIndex => $detection ) {

			/**
			 * 0. Fall: Prozess durchlaeuft einen parallelen Ablauf (AND-Phase)
			 */
			if ( is_null($detection['currentNode']) && !empty($detection['todoMemory']) ) {
				//print("BEFORE\n\n");
				//print_r($detection);

				$newDetections = $this->handleInAND($detection);
				// Wenn $newDetections ein String ist, dann handelt es sich um eine Fehlermeldung.
				if ( is_string($newDetections) ) {
					//print($newDetections);
					return $newDetections;
				}

				foreach ( $newDetections as $newDetection ) {
					if ( !in_array($newDetection, $newTraceExtraction) ) {
						array_push($newTraceExtraction, $newDetection);
					}
				}

				//print_r("\n\n---!!!---Neu generiert: ".count($newDetections)."\n\n");
				$somethingDone = true;
				continue;
			}

			/**
			 * 1. Fall: Endknoten erreicht
			 *
			 * ==> Nichts tun
			 */
			if ( is_null($detection['currentNode']) ) {
				array_push($this->traceExtraction, $detection);
				continue;
			}

			// Letzten Knoten auslesen
			$currentNode = $detection['currentNode'];
			$lastTraceNode = end($detection['trace']);
			$successors = $this->epc->getSuccessor($currentNode);

			// Knotentyp ermitteln
			$currentNodeType = $this->epc->getType($currentNode);

			/**
			 * 2. Fall: Aktueller Knoten ist eine Funktion oder ein Ereignis oder ein XOR-Join
			 *
			 * ==> Syntax-Check: Es muss genau ein Nachfolgeknoten existieren
			 * ==> Auf Loop pruefen und Nachfolgeknoten dem Trace anhaengen
			*/
			if ( $currentNodeType == "event" || $currentNodeType == "function" ||
					(array_key_exists($currentNode, $joinConnectors) && ($currentNodeType == "xor" || $currentNodeType == "or"))
			) {
				// Ereignisse, Funktionen und Join-Konnektoren koennen nur max. einen Nachfolgeknoten haben
				if ( count($successors) == 1 ) {
					$successor = $successors[0];
					$successorType = $this->epc->getType($successor);

					$newDetection = $detection;
					$newDetection['currentNode'] = $successor;

					// Pruefen, ob Nachfolgefunktion bereits im Trace vorhanden ist
					if ( in_array($successor, $detection['trace']) ) {
						// Pruefen ob es sich um einen bereits durchlaufenen Rueckschritt handelt
						if ( in_array($currentNode."_".$successor, $detection['backEdges']) ) {
							continue;
						} else {
							// Rueckschritt merken
							array_push($newDetection['backEdges'], $currentNode."_".$successor);
						}
					}

					// Nachfolgeknoten dem Trace anhaengen
					array_push($newDetection['trace'], $successor);

					// geaenderten Trace zum naechsten Iterationsschritt weitergeben
					if ( !in_array($newDetection, $newTraceExtraction) ) {
						array_push($newTraceExtraction, $newDetection);
					}
					$somethingDone = true;
					continue;
					// Endknoten
				} elseif (count($successors) == 0) {
					$newDetection = $detection;
					$newDetection['currentNode'] = null;
					if ( !in_array($newDetection, $newTraceExtraction) ) {
						array_push($this->traceExtraction, $newDetection);
					}
					continue;
				} else {
					return "Syntax Error (1) in EPC \"".$this->epc->name."\" near \"".$this->epc->getNodeLabel($lastTraceNode)."\" (Node-ID: ".$lastTraceNode.")";
				}
			}

			/**
			 * 3. Fall: Aktueller Knoten ist ein XOR-Split
			 * 5.1 Fall: Aktueller Knoten ist OR-Split (XOR-Interpretation)
			 *
			 * @TODO or weg machen!
			 *
			 * ==> Fuer jede ausgehende Kante einen eigenen Trace erstellen
			 */
			if ( $currentNodeType == "xor" || $currentNodeType == "or") {

				// Iteration ueber die Nachfolgeknoten
				foreach ($successors as $successor) {

					$successorType = $this->epc->getType($successor);
					$newDetection = $detection;
					$newDetection['currentNode'] = $successor;

					/**
					 * Auf Loop pruefen
					 */
					// Pruefen, ob Nachfolgefunktion bereits im Trace vorhanden ist
					if ( in_array($successor, $detection['trace']) ) {
						// Pruefen ob es sich um einen bereits durchlaufenen Rueckschritt handelt
						if ( in_array($currentNode."_".$successor, $detection['backEdges']) ) {
							continue;
						} else {
							// Rueckschritt merken
							array_push($newDetection['backEdges'], $currentNode."_".$successor);
						}
					}

					// Nachfolgeknoten dem Trace anhaengen
					//if ( $currentNodeType == "xor" ) array_push($newDetection['trace'], $successor);
					array_push($newDetection['trace'], $successor);

					// geaenderten Trace zum naechsten Iterationsschritt weitergeben
					if ( !in_array($newDetection, $newTraceExtraction) ) {
						array_push($newTraceExtraction, $newDetection);
					}
					$somethingDone = true;
				}
			}

			/**
			 * 4. Fall: Aktueller Knoten ist ein AND-Split
			 * 5.2 Fall: Aktueller Knoten ist ein OR-Split (AND-Interpretation)
			 *
			 * ==> Es muessen alle moeglichen Abfolgen / Permutationen der ausgehenden Sub-Pfade, bis zum AND-Join
			 *     (oder zum Prozessende) beruecksichtigt werden.
			 */
			if ( $this->epc->isANDSplit($currentNode) || $currentNodeType == "or" ) {

				$newDetection = $detection;
				//$newDetection['todoMemory'][$currentNode] = $successors;

				$newDetection['todoMemory']["0_".$currentNode] = array();
				$abortOperation = false;
				foreach ($successors as $successor) {

					// Pruefen, ob Nachfolgefunktion bereits im Trace vorhanden ist
					if ( in_array($successor, $detection['trace']) ) {
						// Pruefen ob es sich um einen bereits durchlaufenen Rueckschritt handelt
						if ( in_array($currentNode."_".$successor, $detection['backEdges']) ) {
							$abortOperation = true;
							break;
						} else {
							// Rueckschritt merken
							array_push($newDetection['backEdges'], $currentNode."_".$successor);
						}
					}

					$todoEntry = $this->generateTodoEntry($successor);
					array_push($newDetection['todoMemory']["0_".$currentNode], $todoEntry);
				}
				if ( $abortOperation ) continue;

				$newDetection['todoMemorySubTraces']["0_".$currentNode] = array();
				$newDetection['currentNode'] = null;
				//if ( $currentNodeType == "and" ) array_push($newDetection['trace'], $currentNode);

				array_push($newTraceExtraction, $newDetection);
				$somethingDone = true;

			}

			/**
			 * 5.3 Fall: Aktueller Knoten ist ein OR-Split (Permutation exkl. XOR-/AND-Interpretation, da bereits oben behandelt)
			 */
			if ( $currentNodeType == "or" ) {
				// Wenn nur zwei Pfade auf das OR folgen, wurden bereits alle Moeglichkeit durch XOR und AND beruecksichtigt
				$countSucc = count($successors);
				if ( $countSucc > 2 ) {
					// Alle Permutation der Laengen 2 bis Anzahl der Nachfolger-1 (bereits durch AND behandelt) berechnen und jeweils ein AND-Segement oeffnen
					for ( $i=2; $i<$countSucc; $i++ ) {
						$possibleTodoMemories = $this->getSplitORPermutations($successors, $i);
						foreach ( $possibleTodoMemories as $todoMemory ) {
							$newDetection = $detection;

							$newDetection['todoMemory']["0_".$currentNode] = array();
							$abortOperation = false;
							foreach ($todoMemory as $node) {

								// Pruefen, ob Nachfolgefunktion bereits im Trace vorhanden ist
								if ( in_array($node, $detection['trace']) ) {
									// Pruefen ob es sich um einen bereits durchlaufenen Rueckschritt handelt
									if ( in_array($currentNode."_".$node, $detection['backEdges']) ) {
										$abortOperation = true;
										break;
									} else {
										// Rueckschritt merken
										array_push($newDetection['backEdges'], $currentNode."_".$node);
									}
								}

								$todoEntry = $this->generateTodoEntry($node);
								array_push($newDetection['todoMemory']["0_".$currentNode], $todoEntry);
							}
							if ( $abortOperation ) continue;

							//$newDetection['todoMemory'][$currentNode] = $todoMemory;
							$newDetection['todoMemorySubTraces']["0_".$currentNode] = array();
							$newDetection['currentNode'] = null;
							//array_push($newDetection['trace'], $currentNode);

							array_push($newTraceExtraction, $newDetection);
						}
					}
					$somethingDone = true;
				}
			}

		}

		// Wenn etwas getan wurde mache weiter, ansonsten hoere auf
		if ( $somethingDone ) {
			//print_r($newTraceExtraction);
			print(".".count($newTraceExtraction).".");
			return $this->continueTraceExtraction($newTraceExtraction);
		} else {
			//print_r($newTraceExtraction);
			return $this->traceExtraction;
		}
	}

	/**
	 * Behandelt den Fall, dass sich der Trace aktuell in einem Parallelen Ablauf (AND-Phase) befindet.
	 * Fuer alle moeglichen nachfolgenden Funktionen (todoMemory) muss ein neuer Trace erstellt werden
	 *
	 * @param array $detection
	 * @return detections
	 */
	private function handleInAND($detection) {
		//print("\n handleInAnd");
		$newDetections = array();
		$cleanAndSplits = array();
		$lastTraceNode = end($detection['trace']);
		$joinConnectors = $this->epc->getAllJoinConnectors($this->epc->getAllConnectors());

		/**
		 * Wenn genau ein AND geoeffnet ist und darin nur noch ein Nachfolgeelement vorhanden ist, dann geh aus dem AND Segment raus
		*/
		if ( count($detection['todoMemory']) == 1 ) {
			//print("JAAA");
			//print_r($detection);
			$keys = array_keys($detection['todoMemory']);
			$splitNodeID = $keys[0];

			if ( count($detection['todoMemory'][$splitNodeID]) <= 1 ) {
					
				$newDetection = $detection;
					
				if ( count($detection['todoMemory'][$splitNodeID]) == 1 ) {

					$node = end($detection['todoMemory'][$splitNodeID]);
					$tmpFlip = array_flip($detection['todoMemory'][$splitNodeID]);
					$index = $tmpFlip[$node];

					if ( substr($node, 0, 8) == 'stop_and' ) {

						$andJoinID = $this->extractNodeID($node);

						// Pruefen, ob alle Pfade die in den AND-Join eingehen muessen eingegangen sind
						$predecessors = $this->epc->getPredecessor($andJoinID);
						$countInputPathes = count($predecessors);
						$foundPathes = 0;
						foreach ( $predecessors as $predecessor ) {
							if ( in_array($predecessor, $detection['todoMemorySubTraces'][$splitNodeID]) ) $foundPathes++;
						}

						// Wenn alle Pfade eingegangen sind, dann ersetze das stop durch den Nachfolger (vorausgesetzt er ist noch nicht im Memory enthalten
						if ( $countInputPathes == $foundPathes ) {
							$newDetection = $detection;
							$successors = $this->epc->getSuccessor($andJoinID);
							// AND-Join duerfen nur einen Nachfolger haben
							if ( count($successors) == 1 ) {
								$successor = $successors[0];
								$newDetection['todoMemory'][$splitNodeID][$index] = $successor;

								array_push($newDetections, $newDetection);
							} elseif ( count($successors) == 0 ) {
								unset($newDetection['todoMemory'][$splitNodeID][$index]);
								array_push($newDetections, $newDetection);
							} else {
								return "Syntax Error (4) in EPC \"".$this->epc->name."\" near \"".$this->epc->getNodeLabel($lastTraceNode)."\" (Node-ID: ".$lastTraceNode.")";
							}
							//print_r($newDetection);
						}

					}

					if ( substr($node, 0, 8) == 'andsplit' ) return "Deadlock (1) in EPC \"".$this->epc->name."\" near \"".$this->epc->getNodeLabel($lastTraceNode)."\" (Node-ID: ".$lastTraceNode.")";
					if ( substr($node, 0, 7) == 'stop_or' ) {
						$node = $this->extractNodeID($node);
					}
					array_push($newDetection['trace'], $node);
					$newDetection['currentNode'] = $node;

				}
					
				unset($newDetection['todoMemory'][$splitNodeID]);
				unset($newDetection['todoMemorySubTraces'][$splitNodeID]);

				//print_r($newDetection);

				array_push($newDetections, $newDetection);
				return $newDetections;
					
			}
		}

		// Iteration ueber die geoeffneten Splits
		foreach ($detection['todoMemory'] as $splitNode => $possibleNodes) {

			$splitNodeID = $this->extractSplitNodeID($splitNode);
			$splitNodeIDIndex = $this->extractSplitNodeIDIndex($splitNodeID);

			/**
			 * Kein Nachfolgeknoten im TodoMemory bedeutet, dass nach dem AND-Split das Prozessende erreicht wurde.
			 *
			 * Genau ein moeglicher Nachfolgeknoten im TodoMemory kann folgende Szenarien bedeuten:
			 *
			 * 1. nur noch ein andsplit => warten bis Durchlauf abgeschlossen
			 * 2. AND-Segment abgeschlossen
			*/
			if ( count($possibleNodes) <= 1 ) {
				if ( count($possibleNodes) == 1 ) {
					$node = end($possibleNodes);
					if ( substr($node, 0, 8) == 'andsplit' ) continue;
				}
				$newDetection = $detection;
				unset($newDetection['todoMemory'][$splitNode]);

				$tmpDetection = $newDetection;

				// Hier werden abgeschlossene AND-Split/Joins im Memory bereinigt, damit weiter gemacht werden kann
				foreach ($tmpDetection['todoMemory'] as $splitNode2 => $possibleNodes2) {
					foreach ( $possibleNodes2 as $index => $nodeID ) {
						if ( $nodeID == "andsplit_".$splitNode ) {
							// Wenn EIN Knoten vorhanden ist, dann die andsplit-Verzweigung ersetzen, ansonsten entfernen
							if ( count($possibleNodes) == 1 ) {
								$newDetection['todoMemory'][$splitNode2][$index] = $node;
								// todoMemorySubTrace an den uebergeordneten dranhaengen
								foreach ($newDetection['todoMemorySubTraces'][$splitNode] as $subTraceElement) {
									array_push($newDetection['todoMemorySubTraces'][$splitNode2], $subTraceElement);
								}

							} else {
								unset($newDetection['todoMemory'][$splitNode2][$index]);
							}
						}
					}
				}
				unset($newDetection['todoMemorySubTraces'][$splitNode]);
				array_push($newDetections, $newDetection);
				continue;
			}

			// Gedaechtnis ueber die bereits behandelten Stops
			$handledStops = array();

			// Iteration ueber die moeglichen Nachfolgeknoten
			foreach ( $possibleNodes as $index => $nodeID ) {

				/**
				 * 1.1.1 Fall: Synchronisationspunkt erreicht (AND-Join)
				 *
				 * ==> Erst weitermachen, wenn alle in den AND-Join eingehenden Pfade den AND-Join auch erreicht haben
				 */
				if ( substr($nodeID, 0, 8) == 'stop_and' ) {

					$andJoinID = $this->extractNodeID($nodeID);
					if ( in_array($andJoinID, $handledStops) ) continue;

					// Pruefen, ob alle Pfade die in den AND-Join eingehen muessen eingegangen sind
					$predecessors = $this->epc->getPredecessor($andJoinID);
					$countInputPathes = count($predecessors);
					$foundPathes = 0;
					foreach ( $predecessors as $predecessor ) {
						if ( in_array($predecessor, $detection['todoMemorySubTraces'][$splitNode]) ) $foundPathes++;
					}
					//print("---JA---");

					// Wenn alle Pfade eingegangen sind, dann ersetze das stop durch den Nachfolger (vorausgesetzt er ist noch nicht im Memory enthalten
					if ( $countInputPathes == $foundPathes ) {
						//print("---JAAA---");
						$newDetection = $detection;
						$successors = $this->epc->getSuccessor($andJoinID);
						// AND-Join duerfen nur max. einen Nachfolger haben
						if ( count($successors) == 0 ) {
							unset($newDetection['todoMemory'][$splitNode][$index]);
						} elseif ( count($successors) == 1 ) {
							$successor = $successors[0];
							// Wenn Nachfolger des AND-Joins schon im TodoMemory enthalten dann entferne den stop,
							// andernfalls ersetze den stop durch den nachfolger
							if ( $this->epc->isANDJoin($successor) ) {
								if ( in_array("stop_and_".$successor, $detection['todoMemory'][$splitNode]) ) {
									unset($newDetection['todoMemory'][$splitNode][$index]);
								} else {
									$newDetection['todoMemory'][$splitNode][$index] = "stop_and_".$successor;
								}
							} elseif ( $this->epc->isANDSplit($successor) ) {
								if ( in_array("andsplit_".$successor, $detection['todoMemory'][$splitNode]) ) {
									unset($newDetection['todoMemory'][$splitNode][$index]);
								} else {
									// Berechnung eines neuen AND-Split Index
									// TODO Aktuell auf 10 Indizes beschraenkt. Mal drueber nachdenken, ob das problematisch ist, wenn nur Schleifen nur einmal durchlaufen werden
									$newAndSplitIndex = 0;
									while ( array_key_exists("andsplit_".$newAndSplitIndex."_".$successor, $newDetection['todoMemory']) ) {
										$newAndSplitIndex++;
									}
									$newDetection['todoMemory'][$splitNode][$index] = "andsplit_".$newAndSplitIndex."_".$successor;
								}
							} elseif ( $this->epc->isORJoin($successor) ) {
								if ( in_array("stop_or_".$successor, $detection['todoMemory'][$splitNode]) ) {
									unset($newDetection['todoMemory'][$splitNode][$index]);
								} else {
									$newDetection['todoMemory'][$splitNode][$index] = "stop_or_".$successor;
								}
							} else {
								if ( in_array($successor, $detection['todoMemory'][$splitNode]) ) {
									unset($newDetection['todoMemory'][$splitNode][$index]);
								} else {
									$newDetection['todoMemory'][$splitNode][$index] = $successor;
								}
							}

							// Alle anderen identischen stops entfernen
							$tmpDetection = $detection;
							foreach ( $tmpDetection['todoMemory'][$splitNode] as $tmpIndex => $tmpNodeID ) {
								if ( $tmpNodeID == "stop_and_".$andJoinID && $tmpIndex != $index ) unset($newDetection['todoMemory'][$splitNode][$tmpIndex]);
							}

							// And-Join in den Subtrace schreiben
							array_push($newDetection['todoMemorySubTraces'][$splitNode], $andJoinID);

							array_push($newDetections, $newDetection);
							array_push($handledStops, $andJoinID);
						} else {
							return "Syntax Error (4) in EPC \"".$this->epc->name."\" near \"".$this->epc->getNodeLabel($lastTraceNode)."\" (Node-ID: ".$lastTraceNode.")";
						}
					} else {
						$tmpTodoMemory = $detection['todoMemory'][$splitNode];
						$allNodesOfTodoMemorySplitAreEqual = true;
						foreach ( $tmpTodoMemory as $tmpIndex => $tmpNodeID ){
							if ( $tmpNodeID != $nodeID ) $allNodesOfTodoMemorySplitAreEqual = false;
						}
						if ( $allNodesOfTodoMemorySplitAreEqual ) {
							//return "Deadlock (2) in EPC \"".$this->epc->name."\" near \"".$this->epc->getNodeLabel($lastTraceNode)."\" (Node-ID: ".$lastTraceNode.")";
						}
					}
					continue;
				}

				/**
				 * 1.1.1 Fall: Synchronisationspunkt erreicht (OR-Join)
				 *
				 * ==> Wenn alle Pfade eingegangen sind, dann kann stop bedenkenlos ausgefloest werden, anstonsten
				 * ==> Erst weitermachen, wenn im todoMemory keine weitere Knoten mehr durchlaufen werden koennen.
				 */
				if ( substr($nodeID, 0, 7) == 'stop_or' ) {
					//print_r($detection);

					$orJoinID = $this->extractNodeID($nodeID);
					$newDetection = $detection;

					// Pruefen, ob alle Pfade die in den OR-Join eingehen eingegangen sind
					$predecessors = $this->epc->getPredecessor($orJoinID);
					$countInputPathes = count($predecessors);
					$foundPathes = 0;
					foreach ( $predecessors as $predecessor ) {
						if ( in_array($predecessor, $detection['todoMemorySubTraces'][$splitNode]) ) $foundPathes++;
					}

					$allPathesReached = ($countInputPathes == $foundPathes);

					// Pruefen, ob nur noch stops im Memory enthalten sind
					$onlyStops = true;
					$tmpDetection = $detection;
					foreach ( $tmpDetection['todoMemory'][$splitNode] as $tmpIndex => $tmpNodeID ) {
						if ( substr($tmpNodeID, 0, 4) != 'stop' ) $onlyStops = false;
					}

					// Pruefen, ob noch Pfade existieren, die in den OR-Join eingehen werden
					$activatedPathes = false;
					foreach ( $tmpDetection['todoMemory'][$splitNode] as $tmpIndex => $tmpNodeID ) {
						if ( $this->reachesOrWithoutRunningThrough($this->extractNodeID($tmpNodeID), $orJoinID, $splitNodeID) && $tmpIndex != $index ) {
							$activatedPathes = true;
							//print($tmpNodeID.": JA");
						} else {
							//print($tmpNodeID.": Nein");
						}
					}

					// Stop aufloesten, wenn nur noch stops im todoMemory enhalten sind oder alle Pfade zum OR erreicht wurden
					//if ( $onlyStops || $allPathesReached || !$activatedPathes ) {
					if ( $allPathesReached || !$activatedPathes ) {
						//if ( $allPathesReached ) print("pathesReached");
						//if ( !$activatedPathes ) print(" not activatedPathes");
						$successors = $this->epc->getSuccessor($orJoinID);
						// OR-Joins duerfen nur einen Nachfolgeknoten haben
						if ( count($successors) == 0 ) {
							unset($newDetection['todoMemory'][$splitNode][$index]);
						} elseif ( count($successors) == 1 ) {
							$successor = $successors[0];
							// Wenn Nachfolger des OR-Joins schon im TodoMemory enthalten dann entferne den stop,
							// andernfalls ersetze den stop durch den Nachfolger
							if ( $this->epc->isANDJoin($successor) ) {
								if ( in_array("stop_and_".$successor, $detection['todoMemory'][$splitNode]) ) {
									unset($newDetection['todoMemory'][$splitNode][$index]);
								} else {
									$newDetection['todoMemory'][$splitNode][$index] = "stop_and_".$successor;
								}
							} elseif ( $this->epc->isANDSplit($successor) ) {
								if ( in_array("andsplit_".$successor, $detection['todoMemory'][$splitNode]) ) {
									unset($newDetection['todoMemory'][$splitNode][$index]);
								} else {
									$newDetection['todoMemory'][$splitNode][$index] = "andsplit_".$successor;
								}
							} elseif ( $this->epc->isORJoin($successor) ) {
								if ( in_array("stop_or_".$successor, $detection['todoMemory'][$splitNode]) ) {
									unset($newDetection['todoMemory'][$splitNode][$index]);
								} else {
									$newDetection['todoMemory'][$splitNode][$index] = "stop_or_".$successor;
								}
							} else {
								if ( in_array($successor, $detection['todoMemory'][$splitNode]) ) {
									unset($newDetection['todoMemory'][$splitNode][$index]);
								} else {
									$newDetection['todoMemory'][$splitNode][$index] = $successor;
								}
							}
						} else {
							return "Syntax Error (7) in EPC \"".$this->epc->name."\" near \"".$this->epc->getNodeLabel($lastTraceNode)."\" (Node-ID: ".$lastTraceNode.")";
						}
							
						array_push($newDetections, $newDetection);
					} elseif ( $onlyStops && !$activatedPathes ) {
						//print_r($detection);
						return "Syntax Error (8) in EPC \"".$this->epc->name."\" near \"".$this->epc->getNodeLabel($lastTraceNode)."\" (Node-ID: ".$lastTraceNode."). OR-Join-ID ".$orJoinID.".";
					}

					continue;
				}

				/**
				 * 1.2 Fall: Hier wird ein weiterer AND-Split durchlaufen, sodass gewartet wird, bis entweder
				 * 			 ein korrespondierender AND-Join oder mehrere Endknoten gefunden werden.
				 */
				if ( substr($nodeID, 0, 8) == 'andsplit' ) {
					continue;
				}

				$newDetection = $detection;
				$nextNodeType = $this->epc->getType($nodeID);
				$successors = $this->epc->getSuccessor($nodeID);

				/**
				 * 2. Fall: Nachfolgeknoten ist Senke des Prozesses
				 *
				 * => Wenn Nachfolgeknoten eine Funktion, dann dem Trace anhaengen
				 * => aus dem TodoMemory entfernen, da kein moeglicher Nachfolgeknoten existiert
				*/
				if ( count($successors) == 0 )  {
					array_push($newDetection['todoMemorySubTraces'][$splitNode], $nodeID);
					array_push($newDetection['trace'], $nodeID);
					unset($newDetection['todoMemory'][$splitNode][$index]);
					array_push($newDetections, $newDetection);
					continue;
				}

				/**
				 * 3. Fall moeglicher Nachfolgeknoten ist ein Ereignis, eine Funktion oder ein Join-Konnektor
				 *
				 * ==> Syntax-Check: Es muss genau ein Nachfolgeknoten existieren
				 * ==> Wenn Nachfolgeknoten ein Ereignis oder ein Konnektor, dann weiter machen
				 * ==> Wenn Nachfolgeknoten eine Funktion, dann auf Loop pruefen, die Funktion dem Trace anhaengen und weiter machen
				 */
				if ( $nextNodeType == "event" || $nextNodeType == "function" ||	array_key_exists($nodeID, $joinConnectors) ) {
					// Ereignisse, Funktionen und Join-Konnektoren koennen nur max. einen Nachfolgeknoten haben
					if ( count($successors) == 1 ) {

						$tmpDetection = $newDetection;

						// Pruefen, ob noch Pfade existieren, die in den XOR-Join eingehen werden
						$activatedPathes = false;
						if ( $splitNode == "start" && $this->epc->isXORJoin($nodeID) ) {
							foreach ( $tmpDetection['todoMemory'][$splitNode] as $tmpIndex => $tmpNodeID ) {
								if ( $this->reachesXor($this->extractNodeID($tmpNodeID), $nodeID) && $tmpIndex != $index ) {
									$activatedPathes = true;
									//print($tmpNodeID.": JA");
								} else {
									//print($tmpNodeID.": Nein");
								}
							}
						}

						if ( !($splitNode == "start" && $this->epc->isXORJoin($nodeID)) && !$activatedPathes ) {

							$successor = $successors[0];
							$successorType = $this->epc->getType($successor);

							// Pruefen, ob Nachfolgefunktion bereits im Trace vorhanden ist
							if ( in_array($successor, $detection['trace']) ) {
								// Pruefen ob es sich um einen bereits durchlaufenen Rueckschritt handelt
								if ( in_array($nodeID."_".$successor, $detection['backEdges']) ) {
									continue;
								} else {
									// Rueckschritt merken
									array_push($newDetection['backEdges'], $nodeID."_".$successor);
								}
							}

							// Nachfolgeknoten dem Trace anhaengen
							array_push($newDetection['trace'], $nodeID);

							// SubTracesMemory aktualisieren
							// ALLE durchlaufenen Knoten merken, nicht nur die Funktionen! Wichtig, wenn z.B. AND-Join auf AND-Join folgt
							array_push($newDetection['todoMemorySubTraces'][$splitNode], $nodeID);

							// AND-Join ==> Syncpoint setzen (stop)
							if ( $this->epc->isANDJoin($successor) ) {
								$newDetection['todoMemory'][$splitNode][$index] = "stop_and_".$successor;
							} elseif ( $this->epc->isORJoin($successor) ) {
								// Wenn dieser ot stop schon im todoMemory enthalten ist, dann werf den Index raus, ansonsten schreib ihn rein
								if ( in_array("stop_or_".$successor, $newDetection['todoMemory'][$splitNode]) ) {
									unset($newDetection['todoMemory'][$splitNode][$index]);
								} else {
									$newDetection['todoMemory'][$splitNode][$index] = "stop_or_".$successor;
								}
							} else {
								$newDetection['todoMemory'][$splitNode][$index] = $successor;
							}

							// geaenderten Trace zum naechsten Iterationsschritt weitergeben
							array_push($newDetections, $newDetection);


						}
						continue;
					} else {
						return "Syntax Error (5) in EPC \"".$this->epc->name."\" near \"".$this->epc->getNodeLabel($lastTraceNode)."\" (Node-ID: ".$lastTraceNode.")";
					}
				}

				/**
				 * 4. Fall: Moeglicher Nachfolgeknoten ist XOR-Split
				 * 6.1 Fall: XOR-Behandlung von OR
				 *
				 * => Fuer jeden Nachfolgeknoten einen neuen Trace erstellen
				 *
				 * @TODO or raus nehmen
				 */
				if ( $this->epc->isXORSplit($nodeID) || $nextNodeType == "or" ) {
					foreach ( $successors as $successor ) {
						$newDetection = $detection;

						$newTodoEntry = $this->generateTodoEntry($successor);

						if ( $this->epc->isORJoin($successor) && in_array($newTodoEntry, $newDetection['todoMemory'][$splitNode]) ) {
							unset($newDetection['todoMemory'][$splitNode][$index]);
						} else {
							$newDetection['todoMemory'][$splitNode][$index] = $newTodoEntry;
						}

						// Pruefen, ob Kante bereits im Trace vorhanden ist
						if ( in_array($successor, $detection['trace']) ) {
							// Pruefen ob es sich um einen bereits durchlaufenen Rueckschritt handelt
							if ( in_array($nodeID."_".$successor, $detection['backEdges']) ) {
								continue;
							} else {
								// Rueckschritt merken
								array_push($newDetection['backEdges'], $nodeID."_".$successor);
							}
						}

						// Nachfolgeknoten dem Trace anhaengen
						if ( $nextNodeType == "xor" ) array_push($newDetection['trace'], $nodeID);

						array_push($newDetection['todoMemorySubTraces'][$splitNode], $nodeID);
						array_push($newDetections, $newDetection);
					}
				}

				/**
				 * 5. Fall: Moeglicher Nachfolgeknoten ist ein AND-Split
				 * 6.2 Fall: AND-Behandlung von OR
				 *
				 * ==> Es muessen alle moeglichen Abfolgen / Permutationen der ausgehenden Sub-Pfade, bis zum AND-Join
				 *     (oder zum Prozessende) beruecksichtigt werden.
				 */
				if ( $nextNodeType == "and" || $nextNodeType == "or" ) {
					$newDetection = $detection;

					// Berechnung eines neuen AND-Split Index
					// TODO Aktuell auf 10 Indizes beschraenkt. Mal drueber nachdenken, ob das problematisch ist, wenn nur Schleifen nur einmal durchlaufen werden
					$newAndSplitIndex = 0;
					while ( array_key_exists($newAndSplitIndex."_".$nodeID, $newDetection['todoMemory']) ) {
						$newAndSplitIndex++;
					}

					// Neues AND-Memory
					$newDetection['todoMemory'][$newAndSplitIndex."_".$nodeID] = array();
					$abortOperation = false;
					foreach ($successors as $successor) {

						// Pruefen, ob Nachfolgefunktion bereits im Trace vorhanden ist
						if ( in_array($successor, $detection['trace']) ) {
							// Pruefen ob es sich um einen bereits durchlaufenen Rueckschritt handelt
							if ( in_array($nodeID."_".$successor, $detection['backEdges']) ) {
								$abortOperation = true;
								break;
							} else {
								// Rueckschritt merken
								array_push($newDetection['backEdges'], $nodeID."_".$successor);
							}
						}

						$todoEntry = $this->generateTodoEntry($successor);
						array_push($newDetection['todoMemory'][$newAndSplitIndex."_".$nodeID], $todoEntry);
					}
					if ( $abortOperation ) continue;

					$newDetection['todoMemorySubTraces'][$newAndSplitIndex."_".$nodeID] = array();

					// vorhandenes AND-Memory aktualisieren
					$newDetection['todoMemory'][$splitNode][$index] = "andsplit_".$newAndSplitIndex."_".$nodeID;
					array_push($newDetection['todoMemorySubTraces'][$splitNode], $nodeID);
					if ( $nextNodeType == "and" ) array_push($newDetection['trace'], $nodeID);

					array_push($newDetections, $newDetection);
				}

				/**
				 * 6.3 Fall: Alle weiteren durch OR erzeugten Kombinationen, die nicht durch XOR und AND behandelt werden
				 */
				if ( $nextNodeType == "or" ) {
					// Wenn nur zwei Pfade auf das OR folgen, wurden bereits alle Moeglichkeit durch XOR und AND beruecksichtigt
					$countSucc = count($successors);
					if ( $countSucc > 2 ) {

						// Berechnung eines neuen AND-Split Index
						// TODO Aktuell auf 10 Indizes beschraenkt. Mal drueber nachdenken, ob das problematisch ist, wenn nur Schleifen nur einmal durchlaufen werden
						$newAndSplitIndex = 0;
						while ( array_key_exists($newAndSplitIndex."_".$nodeID, $newDetection['todoMemory']) ) {
							$newAndSplitIndex++;
						}

						// Alle Permutation der Laengen 2 bis Anzahl der Nachfolger-1 (bereits durch AND behandelt) berechnen und jeweils ein AND-Segement oeffnen
						for ( $i=2; $i<$countSucc; $i++ ) {
							$possibleTodoMemories = $this->getSplitORPermutations($successors, $i);
							foreach ( $possibleTodoMemories as $todoMemory ) {
								$newDetection = $detection;

								//print_r(array_keys($newDetection['todoMemory']));
								//print("\nandsplit_".$newAndSplitIndex."_".$nodeID."\n");

								$newDetection['todoMemory'][$newAndSplitIndex."_".$nodeID] = array();
								$abortOperation = false;
								foreach ($todoMemory as $node) {

									// Pruefen, ob Nachfolgefunktion bereits im Trace vorhanden ist
									if ( in_array($successor, $detection['trace']) ) {
										// Pruefen ob es sich um einen bereits durchlaufenen Rueckschritt handelt
										if ( in_array($nodeID."_".$successor, $detection['backEdges']) ) {
											$abortOperation = true;
											break;
										} else {
											// Rueckschritt merken
											array_push($newDetection['backEdges'], $nodeID."_".$successor);
										}
									}

									$todoEntry = $this->generateTodoEntry($node);
									array_push($newDetection['todoMemory'][$newAndSplitIndex."_".$nodeID], $todoEntry);
								}
								if ( $abortOperation ) continue;

								//$newDetection['todoMemory'][$nodeID] = $todoMemory;
								$newDetection['todoMemorySubTraces'][$newAndSplitIndex."_".$nodeID] = array();

								// vorhandenes Memory aktualisieren
								$newDetection['todoMemory'][$splitNode][$index] = "andsplit_".$newAndSplitIndex."_".$nodeID;
								array_push($newDetection['todoMemorySubTraces'][$splitNode], $nodeID);
								array_push($newDetection['trace'], $nodeID);

								//print("params 1: ");
								//print_r($newDetection);
								array_push($newDetections, $newDetection);
							}
						}
					}
				}

			}

		}
		//print("after\n");
		//print_r($newDetections);
		return $newDetections;
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
	 * Prueft, ob eine Funktion einen OR-Konnektor erreichen kann
	 *
	 * @param mixed $nodeID ID der Funktion, fuer welche geprueft werden soll, ob Sie einen OR-Konn. erreicht
	 * @param mixed $orId   ID des OR-Knoten der erreicht werden soll
	 *
	 * @return bool
	 */
	private function reachesOr($nodeID, $orId) {
		if ( $this->epc->isORJoin($nodeID) ) {
			if ( $this->continueReachOr($orId, $nodeID) ) return true;
		} else {
			foreach ( $this->orSubTraces as $startOR => $subtraces ) {
				foreach ( $subtraces as $index => $subtrace ) {
					if ( in_array($nodeID, $subtrace) ) {
						$endNodeID = end($subtrace);
						if ( $endNodeID == $orId ) return true;
						if ( $this->epc->isOr($endNodeID) ) {
							if ( $this->continueReachOr($orId, $endNodeID) ) return true;
						}
					}
				}
			}
		}
		return false;
	}

	/**
	 * Rekursive durchfuehrung von reachesOr
	 *
	 * @param mixed $searchOr  Der gesuchte OR-Knoten
	 * @param mixed $currentOr Der aktuelle OR-Knoten
	 *
	 * @return boolean
	 */
	private function continueReachOr($searchOr, $currentOr) {
		foreach ( $this->orSubTraces[$currentOr] as $index => $subtrace ) {
			$endNodeID = end($subtrace);
			if ( $endNodeID == $searchOr ) return true;
			if ( $this->epc->isOr($endNodeID) ) {
				if ( $this->continueReachOr($searchOr, $endNodeID) ) return true;
			}
		}
		return false;
	}

	/**
	 * Prueft, ob eine Funktion einen XOR-Konnektor erreichen kann
	 *
	 * @param mixed $nodeID ID der Funktion, fuer welche geprueft werden soll, ob Sie einen OR-Konn. erreicht
	 * @param mixed $orId   ID des XOR-Knoten der erreicht werden soll
	 *
	 * @return bool
	 */
	private function reachesXor($nodeID, $xorId) {
		if ( $this->epc->isXORJoin($nodeID) ) {
			if ( $this->continueReachXor($xorId, $nodeID) ) return true;
		} else {
			//print_r($this->xorSubTraces);
			foreach ( $this->xorSubTraces as $startXOR => $subtraces ) {
				foreach ( $subtraces as $index => $subtrace ) {
					if ( in_array($nodeID, $subtrace) ) {
						$endNodeID = end($subtrace);
						if ( $endNodeID == $xorId ) return true;
						if ( $this->epc->isXor($endNodeID) ) {
							if ( $this->continueReachXor($xorId, $endNodeID) ) return true;
						}
					}
				}
			}
		}
		return false;
	}

	/**
	 * Rekursive durchfuehrung von reachesOr
	 *
	 * @param mixed $searchXor  Der gesuchte XOR-Knoten
	 * @param mixed $currentXor Der aktuelle XOR-Knoten
	 *
	 * @return boolean
	 */
	private function continueReachXor($searchXor, $currentXor) {
		//print("|");
		foreach ( $this->xorSubTraces[$currentXor] as $index => $subtrace ) {
			$endNodeID = end($subtrace);
			if ( $endNodeID == $searchXor ) return true;
			if ( $this->epc->isXor($endNodeID) ) {
				if ( $this->continueReachXor($searchXor, $endNodeID) ) return true;
			}
		}
		return false;
	}

	private function reachesOrWithoutRunningThrough($nodeID, $orId, $notRunningNodeId) {
		if ( $this->epc->isORJoin($nodeID) ) {
			if ( $this->continueReachOrWithoutRunningThrough($orId, $nodeID, $notRunningNodeId) ) return true;
		} else {
			foreach ( $this->orSubTraces as $startOR => $subtraces ) {
				foreach ( $subtraces as $index => $subtrace ) {
					if ( in_array($nodeID, $subtrace) ) {
						$endNodeID = end($subtrace);
						if ( $endNodeID == $orId ) return true;
						if ( $this->epc->isOr($endNodeID) && $endNodeID != $notRunningNodeId ) {
							if ( $this->continueReachOrWithoutRunningThrough($orId, $endNodeID, $notRunningNodeId) ) return true;
						}
					}
				}
			}
		}
		return false;
	}

	private function continueReachOrWithoutRunningThrough($searchOr, $currentOr, $notRunningNodeId) {
		foreach ( $this->orSubTraces[$currentOr] as $index => $subtrace ) {
			$endNodeID = end($subtrace);
			if ( $endNodeID == $searchOr ) return true;
			if ( $this->epc->isOr($endNodeID) && $endNodeID != $notRunningNodeId ) {
				if ( $this->continueReachOr($searchOr, $endNodeID) ) return true;
			}
		}
		return false;
	}

	/**
	 * Ermittelt alle Pfade, die zwischen zwei aufeinanderfolgenden OR-Konnektoren liegen, nach folgender Methode (z.B.)
	 *  OR1 -> F1 -> F2 -> OR2
	 *  OR2 -> F3 -> OR4
	 *  OR2 -> OR5
	 *  OR4 -> F4
	 *  OR5 -> F5
	 *
	 * @return array
	 */
	private function getORSubtraces() {
		if ( count($this->epc->or) == 0 ) {
			return array();
		}
		$subtraces = array();

		// Iteration ueber alle ORs
		foreach ( $this->epc->or as $orNodeID => $label ) {
			$successors = $this->epc->getSuccessor($orNodeID);
			foreach ( $successors as $successor ) {
				$this->calculateORSubtraces($orNodeID, $successor, array());
			}
		}

		// Betrachtung des Fallen, wenn die EPK mehrere Startknoten hat, die evtl. in einen OR-Join laufen koennten
		$startNodes = $this->epc->getAllStartNodes();
		if ( $this->epc->hasORJoin() && count($startNodes) > 1 ) {
			$i = 0;
			foreach ( $startNodes as $startNodeID => $label ) {
				$this->calculateORSubtraces("start_".$i, $startNodeID, array());
				$i++;
			}
		}
		return $this->orSubTraces;
	}

	/**
	 * Rekursive Berechnung der Subtraces zwischen zwei OR-Konnektoren
	 *
	 * @param mixed $orNodeID
	 * @param mixed $currentNode
	 * @param array $subtrace
	 */
	private function calculateORSubtraces($orNodeID, $currentNode, $subtrace) {
		// 1. Fall: Aktueller Knoten ist ein OR => schreib or in den Trace und hoere auf
		if ( $this->epc->isOr($currentNode) ) {
			array_push($subtrace, $currentNode);
			$this->addOrSubtrace($orNodeID, $subtrace);
			return;
		}

		// 2. Fall: Aktueller Knoten ist eine Funktion => Schreib in den Trace und mache weiter
		//if ( $this->epc->isFunction($currentNode) ) {
		array_push($subtrace, $currentNode);
		//}

		// 3. Fall: Knoten hat keinen Nachfolger => Hoere auf
		$successors = $this->epc->getSuccessor($currentNode);
		if ( count($successors) == 0 ) {
			$this->addOrSubtrace($orNodeID, $subtrace);
			return;
		}

		// 4. Fall: Knoten hat Nachfolger => mach fuer jeden Nachfolger einen eigenen subtrace
		foreach ( $successors as $successor ) {
			$this->calculateORSubtraces($orNodeID, $successor, $subtrace);
		}
	}

	/**
	 * Fuegt einen Subtrace dem Subtrace Array hinzu
	 *
	 * @param mixed $orId
	 * @param array $subtrace
	 */
	private function addOrSubtrace($orId, $subtrace) {
		if ( !isset($this->orSubTraces[$orId]) ) {
			$this->orSubTraces[$orId] = array();
		}
		array_push($this->orSubTraces[$orId], $subtrace);
	}

	/**
	 * Ermittelt alle Pfade, die zwischen zwei aufeinanderfolgenden OR-Konnektoren liegen, nach folgender Methode (z.B.)
	 *  XOR1 -> F1 -> F2 -> XOR2
	 *  XOR2 -> F3 -> XOR4
	 *  XOR2 -> XOR5
	 *  XOR4 -> F4
	 *  XOR5 -> F5
	 *
	 * @return array
	 */
	private function getXORSubtraces() {
		if ( count($this->epc->xor) == 0 ) {
			return array();
		}
		$subtraces = array();

		// Iteration ueber alle ORs
		foreach ( $this->epc->xor as $xorNodeID => $label ) {
			$successors = $this->epc->getSuccessor($xorNodeID);
			foreach ( $successors as $successor ) {
				$this->calculateXORSubtraces($xorNodeID, $successor, array());
			}
		}

		// Betrachtung des Fallen, wenn die EPK mehrere Startknoten hat, die evtl. in einen OR-Join laufen koennten
		$startNodes = $this->epc->getAllStartNodes();
		if ( $this->epc->hasXORJoin() && count($startNodes) > 1 ) {
			$i = 0;
			foreach ( $startNodes as $startNodeID => $label ) {
				$this->calculateXORSubtraces("start_".$i, $startNodeID, array());
				$i++;
			}
		}
		return $this->xorSubTraces;
	}

	/**
	 * Rekursive Berechnung der Subtraces zwischen zwei OR-Konnektoren
	 *
	 * @param mixed $orNodeID
	 * @param mixed $currentNode
	 * @param array $subtrace
	 */
	private function calculateXORSubtraces($xorNodeID, $currentNode, $subtrace) {
		// 1. Fall: Aktueller Knoten ist ein OR => schreib or in den Trace und hoere auf
		if ( $this->epc->isXor($currentNode) ) {
			array_push($subtrace, $currentNode);
			$this->addXorSubtrace($xorNodeID, $subtrace);
			return;
		}

		// 2. Fall: Aktueller Knoten ist eine Funktion => Schreib in den Trace und mache weiter
		//if ( $this->epc->isFunction($currentNode) ) {
		array_push($subtrace, $currentNode);
		//}

		// 3. Fall: Knoten hat keinen Nachfolger => Hoere auf
		$successors = $this->epc->getSuccessor($currentNode);
		if ( count($successors) == 0 ) {
			$this->addXorSubtrace($xorNodeID, $subtrace);
			return;
		}

		// 4. Fall: Knoten hat Nachfolger => mach fuer jeden Nachfolger einen eigenen subtrace
		foreach ( $successors as $successor ) {
			$this->calculateXORSubtraces($xorNodeID, $successor, $subtrace);
		}
	}

	/**
	 * Fuegt einen Subtrace dem Subtrace Array hinzu
	 *
	 * @param mixed $orId
	 * @param array $subtrace
	 */
	private function addXorSubtrace($xorId, $subtrace) {
		if ( !isset($this->xorSubTraces[$xorId]) ) {
			$this->xorSubTraces[$xorId] = array();
		}
		array_push($this->xorSubTraces[$xorId], $subtrace);
	}

	/**
	 * Extrahier die Knoten-ID aus einem String, der in der Extraktion erzeugt wird (also mit praefix stop_ etc.)
	 *
	 * @param string $node
	 *
	 * @return mixed Knoten-ID
	 */
	private function extractNodeID($node) {
		if ( substr($node, 0, 8) == 'stop_and' ) return substr($node, 9);
		if ( substr($node, 0, 7) == 'stop_or' ) return substr($node, 8);
		if ( substr($node, 0, 8) == 'andsplit' ) return substr($node, 11);
		return $node;
	}

	private function extractSplitNodeID($node) {
		return substr($node, 2);
	}

	private function extractSplitNodeIDIndex($node) {
		return substr($node, 0, 1);
	}

	/**
	 * Generiert den Namen fuer den Eintrag im TodoMemory in Abhaengigkeit des Knotens
	 *
	 * @param mixed $node
	 * @return string
	 */
	private function generateTodoEntry($node) {
		if ( $this->epc->isANDJoin($node) ) {
			return "stop_and_".$node;
		} elseif ( $this->epc->isORJoin($node) ) {
			return "stop_or_".$node;
		} else {
			return $node;
		}
	}

	private function onlyFunctions($traces) {
		$tmpTraces = $traces;
		foreach ($tmpTraces as $traceIndex => $trace) {
			foreach ($trace as $index => $nodeID) {
				if ( !$this->epc->isFunction($nodeID) ) unset($traces[$traceIndex][$index]);
			}
		}
		return $traces;
	}

}
?>