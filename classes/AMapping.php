<?php
abstract class AMapping {
	
	public $epc1 = null;
	public $epc2 = null;
	public $matrix = array();
	public $mapping = array();
	public $matches = array();
	public $mappedNodesOfEPC1 = array();
	public $mappedNodesOfEPC2 = array();
	
	/**
	 * Gibt die Funktions-Mapping-Matrix zurueck
	 * 
	 * @return array:
	 */
	public function getMatrix() {
		return $this->matrix;
	}
	
	/**
	 * Generiert das Mapping
	 *
	 * @return void
	 */
	protected function generateMapping($algorithm) {
		eval("\$mapper = new ".$algorithm."Mapper(\$this->matrix);");
		$this->mapping = $mapper->getMapping();
		$this->matches = $this->getMatches();
		$this->getMappedNodes();
	}
	
	/**
	 * Gibt das Mapping als Array zur�ck
	 * @return Array
	 */
	public function getMapping() {
		return $this->mapping;
	}
	
	/**
	 * Pr�ft, ob zwei Knoten(-Labels) gemappt sind
	 *
	 * @param String $label1
	 * @param String $label2
	 *
	 * @return boolean
	 */
	public function isMapped($id1, $id2) {
		foreach ( $this->mapping as $pair ) {
			if (isset($pair[$id1]) && $pair[$id1] == $id2) {
				return true;
			}
		}
		return false;
	}
	
	protected function getMatches() {
		$matches = array();
		foreach ( $this->mapping as $pair ) {
			// Knotenpaar auslesen
			$keys = array_keys($pair);
			$id1 = $keys[0];
			$id2 = $pair[$id1];
			
			// Zugehoerige Matches suchen
			$foundMatches = array();
			foreach ( $matches as $matchID => $match ) {
				if ( $match->contains(1, $id1) || $match->contains(2, $id2) ) {
					array_push($foundMatches, $matchID);
				}
			}
			
			if ( !empty($foundMatches) ) {
				// Knoten zum Match hinzufuegen und ggf. Matches zusammenfuehren falls notwendig
				$matches[$foundMatches[0]]->add(1, $id1);
				$matches[$foundMatches[0]]->add(2, $id2);
				foreach ( $foundMatches as $matchID ) {
					$matches[$foundMatches[0]]->merge($matches[$matchID]);
					if ( $matchID != $foundMatches[0] ) unset($matches[$matchID]);
				}
			} else {
				// Neues Match erstellen
				$match = new Match();
				$match->add(1, $id1);
				$match->add(2, $id2);
				array_push($matches, $match);
			}
		}
		return $matches;
	}
	
	public function getMappedNodes() {
		$mappedNodesOfEPC1 = array();
		$mappedNodesOfEPC2 = array();
		foreach ( $this->mapping as $pair ) {
			// Knotenpaar auslesen
			$keys = array_keys($pair);
			$id1 = $keys[0];
			$id2 = $pair[$id1];
			//print("Pair: ".$id1." (EPC1) => ".$id2." (EPC2)\n");
			
			if ( !in_array($id1, $mappedNodesOfEPC1) && $this->epc1->isFunction($id1) ) array_push($mappedNodesOfEPC1, $id1);
			if ( !in_array($id2, $mappedNodesOfEPC2) && $this->epc2->isFunction($id2) ) array_push($mappedNodesOfEPC2, $id2);
		}
		$this->mappedNodesOfEPC1 = $mappedNodesOfEPC1;
		$this->mappedNodesOfEPC2 = $mappedNodesOfEPC2;
		
		//print_r(array("mappedNodesOfEPC1" => $mappedNodesOfEPC1, "mappedNodesOfEPC2" => $mappedNodesOfEPC2));
		
		return array("mappedNodesOfEPC1" => $mappedNodesOfEPC1, "mappedNodesOfEPC2" => $mappedNodesOfEPC2);
	}
	
	public function getNumOfMatches() {
		return count($this->matches);
	}
	
	public function getNumOfComplexMatches() {
		$numOfComplexMatches = 0;
		foreach ( $this->matches as $match ) {
			if ( $match->isComplex() ) $numOfComplexMatches++;
		}
		return $numOfComplexMatches;
	}
	
	public function getComplexMatches() {
		$complexMatches = array();
		foreach ( $this->matches as $match ) {
			if ( $match->isComplex() ) array_push($complexMatches, $match);
		}
		return $complexMatches;
	}
	
	public function getNumOfSimpleMatches() {
		$numOfComplexMatches = $this->getNumOfComplexMatches();
		$numOfAllMatches = $this->getNumOfMatches();
		return $numOfAllMatches - $numOfComplexMatches;
	}
	
	/**
	 * Pr�ft, ob das Mapping eindeutig ist
	 *
	 * @param String $label1
	 * @param String $label2
	 *
	 * @return boolean
	 */
	public function isMappedPrecisely($id1, $id2) {
		$node1Count = 0;
		$node2Count = 0;
		foreach ( $this->mapping as $pair ) {
			if ( isset($pair[$id1]) ) $node1Count++;
			if ( in_array($id2, $pair) ) $node2Count++;
		}
		if ( $node1Count > 1 || $node2Count > 1 ) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Prueft, ob ein Mapping ausgehend von einem bestimmten Knoten existiert
	 *
	 * @param mixed $id1
	 * @return mixed|boolean
	 */
	public function mappingExistsFrom($id1) {
		foreach ( $this->mapping as $pair ) {
			if ( array_key_exists($id1, $pair) ) {
				return $pair[$id1];
			}
		}
		return false;
	}
	
	/**
	 * Prueft, ob ein Mapping zu einem bestimmten knoten existiert
	 *
	 * @param mixed $id2
	 * @return mixed|boolean
	 */
	public function mappingExistsTo($id2) {
		foreach ( $this->mapping as $pair ) {
			$flipped = array_flip($pair);
			if ( array_key_exists($id2, $flipped) ) {
				return $flipped[$id2];
			}
		}
		return false;
	}
	
	/**
	 * Ermittelt alle nicht-gematchten Funktionen aus EPK1
	 *
	 * @return array
	 */
	public function getAllUnmappedFunctionsOfEPC1() {
		$unmappedFunctions = array();
		foreach ( $this->epc1->functions as $id => $label ) {
			if ( !$this->mappingExistsFrom($id) ) {
				$unmappedFunctions[$id] = $label;
			}
		}
		return $unmappedFunctions;
	}
	
	/**
	 * Ermittelt alle nicht-gematchten Funktionen aus EPK2
	 *
	 * @return array
	 */
	public function getAllUnmappedFunctionsOfEPC2() {
		$unmappedFunctions = array();
		foreach ( $this->epc2->functions as $id => $label ) {
			if ( !$this->mappingExistsTo($id) ) {
				$unmappedFunctions[$id] = $label;
			}
		}
		return $unmappedFunctions;
	}
	
	/**
	 * Ermittelt alle ungematchten Kanten aus EPK1
	 *
	 * @return array
	 */
	public function getAllUnmappedEdgesOfEPC1() {
		$unmappedEdges = $this->epc1->edges;
		foreach ( $this->epc2->edges as $edge ) {
			foreach ($edge as $sourceNodeID => $targetNodeID) {
				$node1 = $this->mappingExistsTo($sourceNodeID);
				$node2 = $this->mappingExistsTo($targetNodeID);
				if ( $node1 && $node2 && !empty($node1) && !empty($node2) ) {
					$index = $this->epc1->edgeExists($node1, $node2);
					if ( is_integer($index) ) {
						unset($unmappedEdges[$index]);
					}
				}
			}
		}
		return $unmappedEdges;
	}
	
	/**
	 * Ermittelt alle ungematchten Kanten aus EPK2
	 *
	 * @return array
	 */
	public function getAllUnmappedEdgesOfEPC2() {
		$unmappedEdges = $this->epc2->edges;
		foreach ( $this->epc1->edges as $edge ) {
			foreach ($edge as $sourceNodeID => $targetNodeID) {
				$node1 = $this->mappingExistsFrom($sourceNodeID);
				$node2 = $this->mappingExistsFrom($targetNodeID);
				if ( $node1 && $node2 && !empty($node1) && !empty($node2) ) {
					$index = $this->epc2->edgeExists($node1, $node2);
					if ( is_integer($index) ) {
						unset($unmappedEdges[$index]);
					}
				}
			}
		}
		return $unmappedEdges;
	}
	
	/**
	 * Ermittelt alle nicht-gematchten Konnektoren aus EPK1
	 *
	 * @return array
	 */
	public function getAllUnmappedConnectorsOfEPC1() {
		$unmappedConnectors = array();
		$connectors = $this->epc1->getAllConnectors();
		foreach ( $connectors as $id => $type ) {
			if ( !$this->mappingExistsFrom($id) ) {
				$unmappedConnectors[$id] = $type;
			}
		}
		return $unmappedConnectors;
	}
	
	/**
	 * Ermittelt alle nicht-gematchten Konnektoren aus EPK2
	 *
	 * @return array
	 */
	public function getAllUnmappedConnectorsOfEPC2() {
		$unmappedConnectors = array();
		$connectors = $this->epc2->getAllConnectors();
		foreach ( $connectors as $id => $type ) {
			if ( !$this->mappingExistsTo($id) ) {
				$unmappedConnectors[$id] = $type;
			}
		}
		return $unmappedConnectors;
	}
	
	/** 
	 * Entfernt Dummy-Funktionen, die bei der Konvertierung aus 
	 * Petri-Netzen nach EPK resultieren koennen. Hintergrund
	 * der Implementierung ist der Process Matching Contest
	 * im Rahmen der BPM2013.
	 */
	public function deleteDummyTransitions() {
		foreach ( $this->mapping as $index => $pair ) {
			$id1_arr = array_keys($pair);
			$id1 = $id1_arr[0];
			$id2 = $pair[$id1];
			$label1 = $this->epc1->getNodeLabel($id1);
			$label2 = $this->epc2->getNodeLabel($id2);
			if ( preg_match("/^t[0-9]*$/", $label1) || preg_match("/^t[0-9]*$/", $label2) ) unset($this->mapping[$index]);
		}
	}
	
	/**
	 * Removes antonym maps
	 * 
	 * @param array $antonymCache
	 * @param array $nonAntonymCache
	 */
	public function removeAntonymMaps($antonymCache=null, $nonAntonymCache=null) {
		//print("  Checking maps between \"".$this->epc1->name."\" and \"".$this->epc2->name."\" for antonyms... \n");
		$numMatches = count($this->mapping);
		$progressBar = new CLIProgressbar($numMatches, 0.1);
		$compareCounter = 0;
		
		$antonymCache = is_null($antonymCache) ? WordNet::loadAntonymLabelsFromPersistedFile() : $antonymCache;
		$nonAntonymCache = is_null($nonAntonymCache) ? WordNet::loadNonAntonymLabelsFromPersistedFile() : $nonAntonymCache;
				
		foreach ( $this->mapping as $index => $pair ) {
				
			// Checking for Antonyms
			$id1_arr = array_keys($pair);
			$id1 = $id1_arr[0];
			$id2 = $pair[$id1];
			$label1 = $this->epc1->getNodeLabel($id1);
			$label2 = $this->epc2->getNodeLabel($id2);
				
			if ( NLP::checkIfFunctionLabelsAreAntonyms($label1, $label2, $antonymCache, $nonAntonymCache) ) {
				unset($this->mapping[$index]);
				print("    map between \"".$label1."\" and \"".$label2."\" removed (antonym issue)\n");
			}
				
			$compareCounter++;
			$progressBar->run($compareCounter);
		}
	}
	
	/**
	 * removes maps which have no verb-object correspondency (either verb or object correspondency)
	 * 
	 * @param array $correspondencyCache
	 * @param array $nonCorrespondencyCache
	 */
	public function removeMissingVerbObjectCorrespondentMaps($correspondencyCache=null, $nonCorrespondencyCache=null) {

		//print("  Checking maps between \"".$this->epc1->name."\" and \"".$this->epc2->name."\" for missing verb-object correspondencies... \n");
		$numMatches = count($this->mapping);
		$progressBar = new CLIProgressbar($numMatches, 0.1);
		$compareCounter = 0;
		
		$correspondencyCache = is_null($correspondencyCache) ? NLP::loadCorrespondentLabelsFromPersistedFile() : $correspondencyCache;
		$nonCorrespondencyCache = is_null($nonCorrespondencyCache) ? NLP::loadNonCorrespondentLabelsFromPersistedFile() : $nonCorrespondencyCache;
		$matchingCorrespondenceCache = NLP::loadMatchingCorrespondentLabelsFromPersistedFile();
		$matchingNonCorrespondenceCache = NLP::loadMatchingNonCorrespondentLabelsFromPersistedFile();
		$labelElements = NLP::loadLabelElementsFromPersistedFile();
		
		foreach ( $this->mapping as $index => $pair ) {
		
			// Checking for Antonyms
			$id1_arr = array_keys($pair);
			$id1 = $id1_arr[0];
			$id2 = $pair[$id1];
			$label1 = $this->epc1->getNodeLabel($id1);
			$label2 = $this->epc2->getNodeLabel($id2);
		
			// Checking for Verb-Object-Comparability:
			$doLabelsCorrespond = NLP::checkVerbObjectCorrespondencyForTwoLabels($label1, $label2, $correspondencyCache, $nonCorrespondencyCache, $matchingCorrespondenceCache, $matchingNonCorrespondenceCache, $labelElements);
			
// 			if ( $this->epc1->name == "Muenster" && $this->epc2->name == "Potsdam" ) {
// 				print("\n          checking ".$label1." to ".$label2.": doLabelsCorrespond: ".$doLabelsCorrespond."\n\n");
// 			}
		
			if ( !$doLabelsCorrespond ) {
				$verb1 = NLP::getLabelVerb($label1);
				$verb2 = NLP::getLabelVerb($label2);
				
				// check if there is perhaps a spell issue in the verb
				if ( !is_null($verb1) && !is_null($verb2) && levenshtein($verb1, $verb2) > 1 ) {
					unset($this->mapping[$index]);
					print("    map between \"".$label1."\" and \"".$label2."\" removed (missing verb-object correspondencies)\n");
				}
			}
		
			$compareCounter++;
			$progressBar->run($compareCounter);
		}
	}
	
	public function removeMapsCausedByHarmonizedModel() {
		foreach ( $this->mapping as $index => $pair ) {
		
			$id1_arr = array_keys($pair);
			$id1 = $id1_arr[0];
			$id2 = $pair[$id1];
			$label1 = $this->epc1->getNodeLabel($id1);
			$label2 = $this->epc2->getNodeLabel($id2);
						
			if ($label1 == $label2) continue;
			
			/**
			 * Aehnlichkeit ueber Porter-Stems bestimmen
			 */
			
			$node1 = new FunctionOntologyWithSynonyms ($this->epc1, $id1, $label1);
			$node2 = new FunctionOntologyWithSynonyms ($this->epc2, $id2, $label2);
			
			$countWordstemsOfLabel1 = count ( $node1->wordstems );
			$countWordstemsOfLabel2 = count ( $node2->wordstems );
			
			if ($countWordstemsOfLabel1 > $countWordstemsOfLabel2) {
				// Label1 muss immer dasjenigen mit der geringeren Anzahl an Komponenten (Woertern) sein
				$node_temp = $node1;
				$node1 = $node2;
				$node2 = $node_temp;
			}
			
			$countWordstemMappings = 0;
			foreach ( $node1->wordstems as $wordstem1 ) {
				foreach ( $node2->wordstems as $wordstem2 ) {
					if ($wordstem1 == $wordstem2) {
						$countWordstemMappings ++;
						break;
					}
				}
			}
			
			$stemSimilarity = round ( (2 * $countWordstemMappings / ($countWordstemsOfLabel1 + $countWordstemsOfLabel2)) * 100, 2 );
			
			$naryMapping = new NAryWordstemMappingWithAntonyms2015();
			if ( $stemSimilarity < $naryMapping->threshold_ontology_quote ) {
				unset($this->mapping[$index]);
				print("    map between \"".$label1."\" and \"".$label2."\" removed (caused by harmonization degree)\n");
			}
			
		}
		
	}
	
	public function removeMatchesForWhichAnAdversingIdentyMatchIsAvailable() {
		
		//print("  Checking maps between \"".$this->epc1->name."\" and \"".$this->epc2->name."\" for which an adversing identity map are available ... \n");
		$numMatches = count($this->mapping);
		$progressBar = new CLIProgressbar($numMatches, 0.1);
		$compareCounter = 0;
		
		foreach ( $this->mapping as $index => $pair ) {
		
			// Checking for Antonyms
			$id1_arr = array_keys($pair);
			$id1 = $id1_arr[0];
			$id2 = $pair[$id1];
			$label1 = $this->epc1->getNodeLabel($id1);
			$label2 = $this->epc2->getNodeLabel($id2);
			
			if ( strtolower($label1) != strtolower($label2) ) {
			
				foreach ( $this->epc1->functions as $label ) {
					if ( strtolower($label) == strtolower($label2) ) {
						unset($this->mapping[$index]);
						print("    map between \"".$label1."\" and \"".$label2."\" removed (adversing identity map detected)\n");
					}
				}
				
				foreach ( $this->epc2->functions as $label ) {
					if ( strtolower($label) == strtolower($label1) ) {
						unset($this->mapping[$index]);
						print("    map between \"".$label1."\" and \"".$label2."\" removed (adversing identity map detected)\n");
					}
				}
			
			}
			
			$compareCounter++;
			$progressBar->run($compareCounter);
		}
	}
	
	/**
	 * iterates over the complex maps and choose the best combination
	 * as it is assumed, that highly harmonizated are on the same degree of details. 
	 * Thus, there are no n-ary maps
	 */
	public function improveComplexMatchesCausedByHarmonizedModels() {
		
		$correspondencyCache = NLP::loadCorrespondentLabelsFromPersistedFile();
		$nonCorrespondencyCache = NLP::loadNonCorrespondentLabelsFromPersistedFile();
		$matchingCorrespondenceCache = NLP::loadMatchingCorrespondentLabelsFromPersistedFile();
		$matchingNonCorrespondenceCache = NLP::loadMatchingNonCorrespondentLabelsFromPersistedFile();
		$labelElements = NLP::loadLabelElementsFromPersistedFile();
		
		$complexMatches = $this->getComplexMatches();
		foreach ( $complexMatches as $match ) {
			$bestSimValue = 0;
			$nodeID1ForMap = null;
			$nodeID2ForMap = null;
			$nodeLabel1ForMap = null;
			$nodeLabel2ForMap = null;
			foreach ( $match->nodeIDsOfModel1 as $nodeID1 ) {
				foreach ( $match->nodeIDsOfModel2 as $nodeID2 ) {
					
					$label1 = $this->epc1->getNodeLabel($nodeID1);
					$label2 = $this->epc2->getNodeLabel($nodeID2);
					
					if (strtolower($label1) == strtolower($label2)) {
						$bestSimValue = 1;
						$nodeID1ForMap = $nodeID1;
						$nodeID2ForMap = $nodeID2;
						$nodeLabel1ForMap = $label1;
						$nodeLabel2ForMap = $label2;
						continue;
					}
						
					/**
					 * Aehnlichkeit ueber Porter-Stems bestimmen
					 */
						
					$node1 = new FunctionOntologyWithSynonyms ($this->epc1, $nodeID1, $label1);
					$node2 = new FunctionOntologyWithSynonyms ($this->epc2, $nodeID2, $label2);
						
					$countWordstemsOfLabel1 = count ( $node1->wordstems );
					$countWordstemsOfLabel2 = count ( $node2->wordstems );
						
					if ($countWordstemsOfLabel1 > $countWordstemsOfLabel2) {
						// Label1 muss immer dasjenigen mit der geringeren Anzahl an Komponenten (Woertern) sein
						$node_temp = $node1;
						$node1 = $node2;
						$node2 = $node_temp;
					}
						
					$countWordstemMappings = 0;
					foreach ( $node1->wordstems as $wordstem1 ) {
						foreach ( $node2->wordstems as $wordstem2 ) {
							if ($wordstem1 == $wordstem2) {
								$countWordstemMappings ++;
								break;
							}
						}
					}
						
					$stemSimilarity = round ( (2 * $countWordstemMappings / ($countWordstemsOfLabel1 + $countWordstemsOfLabel2)) * 100, 2 );

					if ( $stemSimilarity >= $bestSimValue ) {
						$bestSimValue = $stemSimilarity;
						$nodeID1ForMap = $nodeID1;
						$nodeID2ForMap = $nodeID2;
						$nodeLabel1ForMap = $label1;
						$nodeLabel2ForMap = $label2;
					}
				}
			}
			
			foreach ( $match->nodeIDsOfModel1 as $nodeID1 ) {
				foreach ( $match->nodeIDsOfModel2 as $nodeID2 ) {
					
					if ( $nodeID1ForMap == $nodeID1 && $nodeID2ForMap == $nodeID2 ) continue;
					
					$verb1ForMap = NLP::getLabelVerb($nodeLabel1ForMap, $labelElements);
					
					foreach ( $this->mapping as $index => $pair ) {
						$id1_arr = array_keys($pair);
						$id1 = $id1_arr[0];
						$id2 = $pair[$id1];
						$label1 = $this->epc1->getNodeLabel($id1);
						$label2 = $this->epc2->getNodeLabel($id2);
						
						if ( strtolower($label1) == strtolower($label2) ) continue;
						
						if ( $nodeID1 == $id1 && $nodeID2 == $id2 ) {
							$verb1 = NLP::getLabelVerb($label1, $labelElements);
							
							if ( !NLP::areAntonymVerbs($verb1, $verb1ForMap) ) {
								unset($this->mapping[$index]);
								print("    map between \"".$label1."\" and \"".$label2."\" removed (complexity reduction caused by harmonization degree)\n");
								//print("    map between \"".$label1."\" and \"".$label2."\" removed (complexity reduction caused by harmonization degree, conflict: ".$nodeLabel1ForMap." => ".$nodeLabel2ForMap.")\n");
							}
						}
					}
				}
			}
		}
	}
	
	public function improveComplexMatchesBasedOnContext() {
		$complexMatches = $this->getComplexMatches();
		
		//if ( $this->epc1->name == "Muenster" && $this->epc2->name == "TU_Munich" ) {

			foreach ( $complexMatches as $match ) {
				
				// retrieve involved orgunits
				$orgUnits1 = array();
				$orgUnits1Names = array();
				$orgUnits1Bags = array();
				$orgUnits2 = array();
				$orgUnits2Names = array();
				$orgUnits2Bags = array();
					
				foreach ( $match->nodeIDsOfModel1 as $nodeID ) {
					$orgUnit = $this->epc1->getOrganizationUnit($nodeID);
					if ( !is_null($orgUnit) && !in_array($orgUnit, $orgUnits1) ) {
						array_push($orgUnits1, $orgUnit);
						$orgUnits1Names[$orgUnit] = $this->epc1->orgUnits[$orgUnit];
						$bag = $this->getBagOfWordsForOrgUnit($orgUnit, $this->epc1);
						$orgUnits1Bags[$this->epc1->orgUnits[$orgUnit]] = $bag;
					}
				}
					
				foreach ( $match->nodeIDsOfModel2 as $nodeID ) {
					$orgUnit = $this->epc2->getOrganizationUnit($nodeID);
					if ( !is_null($orgUnit) && !in_array($orgUnit, $orgUnits2) ) {
						array_push($orgUnits2, $orgUnit);
						$orgUnits2Names[$orgUnit] = $this->epc2->orgUnits[$orgUnit];
						$bag = $this->getBagOfWordsForOrgUnit($orgUnit, $this->epc2);
						$orgUnits2Bags[$this->epc2->orgUnits[$orgUnit]] = $bag;
					}
				}
				
				$string = $match->toString($this->epc1, $this->epc2);
				//print($string);
				
				//print("  OrgUnits1: ".implode(", ", $orgUnits1Names)."\n");
				//print("  OrgUnits2: ".implode(", ", $orgUnits2Names)."\n");
				
// 				print("  Bag similarities:\n");
// 				foreach ( $orgUnits1Bags as $orgUnitName1 => $bag1 ) {
// 					foreach ( $orgUnits2Bags as $orgUnitName2 => $bag2 ) {
// 						$sim = $this->compareWordBags($bag1, $bag2);
// 						print("    ".$orgUnitName1." | ".$orgUnitName2.": ".$sim."\n");
// 					}
// 				}
				
				if ( count($orgUnits1) > 1 || count($orgUnits2) > 1 ) {
					$orgMaps = array();
					if ( count($orgUnits1) < count($orgUnits2) ) {
						foreach ( $orgUnits1 as $orgUnit1ID ) {
							$orgUnit1Name = $orgUnits1Names[$orgUnit1ID];
							$bag1 = $orgUnits1Bags[$orgUnit1Name];
							$sim = 0;
							$mappedOrgUnit2ID = null;
							foreach ( $orgUnits2 as $orgUnit2ID ) {
								$orgUnit2Name = $orgUnits2Names[$orgUnit2ID];
								$bag2 = $orgUnits2Bags[$orgUnit2Name];
								$currSim = $this->compareWordBags($bag1, $bag2);
								if ( $currSim >= $sim ) {
									$sim = $currSim;
									$mappedOrgUnit2ID = $orgUnit2ID;
								}
							}
							array_push($orgMaps, array("orgUnitOfEPC1" => $orgUnit1ID, "orgUnitOfEPC2" => $mappedOrgUnit2ID));
						}
					} else {
						foreach ( $orgUnits2 as $orgUnit2ID ) {
							$orgUnit2Name = $orgUnits2Names[$orgUnit2ID];
							$bag2 = $orgUnits2Bags[$orgUnit2Name];
							$sim = 0;
							$mappedOrgUnit1ID = null;
							foreach ( $orgUnits1 as $orgUnit1ID ) {
								$orgUnit1Name = $orgUnits1Names[$orgUnit1ID];
								$bag1 = $orgUnits1Bags[$orgUnit1Name];
								$currSim = $this->compareWordBags($bag1, $bag2);
								if ( strlen($orgUnit1Name) > 3 && strlen($orgUnit2Name) > 3 && levenshtein(strtolower($orgUnit1Name), strtolower($orgUnit2Name)) <= 1 ) $currSim = 1;
								if ( $currSim >= $sim ) {
									$sim = $currSim;
									$mappedOrgUnit1ID = $orgUnit1ID;
								}
							}
							array_push($orgMaps, array("orgUnitOfEPC1" => $mappedOrgUnit1ID, "orgUnitOfEPC2" => $orgUnit2ID));
						}
					}

					foreach ( $orgMaps as $orgPair ) {
						//print("\nMapped OrgUnits: ".$orgUnits1Names[$orgPair["orgUnitOfEPC1"]]." => ".$orgUnits2Names[$orgPair["orgUnitOfEPC2"]]."\n");
					}
					
					foreach ( $this->mapping as $index => $pair ) {
					
						$id1_arr = array_keys($pair);
						$id1 = $id1_arr[0];
						$id2 = $pair[$id1];
						$label1 = $this->epc1->getNodeLabel($id1);
						$label2 = $this->epc2->getNodeLabel($id2);
						$orgUnitOfNode1 = $this->epc1->getOrganizationUnit($id1);
						$orgUnitOfNode2 = $this->epc2->getOrganizationUnit($id2);
						
						if ( in_array($id1, $match->nodeIDsOfModel1) && in_array($id2, $match->nodeIDsOfModel2) && !is_null($orgUnitOfNode1) && !is_null($orgUnitOfNode2)) {
							$matched = false;
							foreach ( $orgMaps as $orgPair ) {
								if ( $orgPair["orgUnitOfEPC1"] == $orgUnitOfNode1 && $orgPair["orgUnitOfEPC2"] == $orgUnitOfNode2 ) $matched = true;
							}
							if ( !$matched ) {
								unset($this->mapping[$index]);
								print("    map between \"".$label1."\" and \"".$label2."\" removed (organizational unit missmatch)\n");
							}
						}
						
					}
					
				}
				
				
			}
			
		//}
	}
	
	private function getBagOfWordsForOrgUnit($orgUnitID, EPC &$epc) {
		$bag = array();
		foreach ( $epc->functionOrgUnitAssignments as $funcID => $orgID ) {
			if ( $orgID == $orgUnitID ) {
				$label = $epc->functions[$funcID];
				$verb = is_null(NLP::getLabelVerb($label)) ? "" : NLP::getLabelVerb($label);
				$verbExploded = explode(" ", $verb);
				foreach ( $verbExploded as $token ) {
					$token = strtolower(trim($token));
					if ( !empty($token) && !in_array($token, $bag) ) array_push($bag, $token);
				}
				
				$object = is_null(NLP::getLabelObject($label)) ? "" : NLP::getLabelObject($label);
				$objectExploded = explode(" ", $object);
				foreach ( $objectExploded as $token ) {
					$token = strtolower(trim($token));
					if ( !empty($token) && !in_array($token, $bag) ) array_push($bag, $token);
				}
			}
		}
		return NLP::removeStopWordsInArray($bag);
	}
	
	private function compareWordBags($bag1, $bag2) {
		$intersection = array_intersect($bag1, $bag2);
		$numIntersection = count($intersection);
		$numWordsBag1 = count($bag1);
		$numWordsBag2 = count($bag2);
		$min = min($numWordsBag1, $numWordsBag2);
		$sim = $min==0 ? 0 : $numIntersection/$min;
		return $sim;
	}
	
	public function convertToGenericMapping() {
		$genericMapping = new GenericMapping();
		$genericMapping->addModel((string) $this->epc1->id, (string) $this->epc1->name);
		$genericMapping->addModel((string) $this->epc2->id, (string) $this->epc2->name);
		
		foreach ( $this->mapping as $pair ) {
			$id1_arr = array_keys($pair);
			$id1 = $id1_arr[0];
			$id2 = $pair[$id1];
			$genericMapping->addMap(array((string) $id1, (string) $id2), array((string) $this->epc1->id, (string) $this->epc2->id), 1);
		}
		
		return $genericMapping;
	}
	
	public function export() {
		$content = $this->epc1->name."\r\n";
		$content .= $this->epc2->name;
		
		foreach ( $this->mapping as $pair ) {			
			$id1_arr = array_keys($pair);
			$id1 = $id1_arr[0];
			$id2 = $pair[$id1];
			$label1 = $this->epc1->getNodeLabel($id1);
			$label2 = $this->epc2->getNodeLabel($id2);
			$content .= "\r\n".$label1.",".$label2;
		}
		
		$fileGenerator = new FileGenerator(trim($this->epc1->name)."_".trim($this->epc2->name).".txt", $content);
		$fileGenerator->setFilename(trim($this->epc1->name)."_".trim($this->epc2->name).".txt");
		$fileGenerator->setContent($content);
		$file = $fileGenerator->execute();
		return $file;
	}
	
	public function exportWithPipes() {
		$content = $this->epc1->name."\r\n";
		$content .= $this->epc2->name;
	
		foreach ( $this->mapping as $pair ) {
			$id1_arr = array_keys($pair);
			$id1 = $id1_arr[0];
			$id2 = $pair[$id1];
			$label1 = $this->epc1->getNodeLabel($id1);
			$label1 = str_replace("\r\n", " ", $label1);
			$label1 = str_replace("\n", " ", $label1);
			$label2 = $this->epc2->getNodeLabel($id2);
			$label2 = str_replace("\r\n", " ", $label2);
			$label2 = str_replace("\n", " ", $label2);
			$content .= "\r\n".$label1." | ".$label2;
		}
	
		$fileGenerator = new FileGenerator(trim($this->epc1->name)."_".trim($this->epc2->name).".txt", $content);
		$fileGenerator->setFilename(trim($this->epc1->name)."_".trim($this->epc2->name).".txt");
		$fileGenerator->setContent($content);
		$file = $fileGenerator->execute();
		return $file;
	}
	
	public function exportAndreasSonntag() {
		$content = "";
		
		foreach ( $this->mapping as $pair ) {
			$id1_arr = array_keys($pair);
			$id1 = $id1_arr[0];
			$id2 = $pair[$id1];
			$label1 = $this->epc1->getNodeLabel($id1);
			$label2 = $this->epc2->getNodeLabel($id2);
			$content .= "{".$label1."}{".$label2."}\r\n";
		}
		
		$fileGenerator = new FileGenerator(trim($this->epc1->name)."_".trim($this->epc2->name).".txt", $content);
		$fileGenerator->setFilename(trim($this->epc1->name)."_#_".trim($this->epc2->name).".txt");
		$fileGenerator->setContent($content);
		$file = $fileGenerator->execute();
		return $file;
	}
	
	/**
	 * Exportiert in eine andere Darstellung als beim Contest und ist damit robuster gegen Fehler und besser lesbar
	 * @return string
	 */
	public function export2($path = "", $prefix = true) {
		$content = $this->epc1->name."\r\n";
		$content .= $this->epc2->name;
	
		foreach ( $this->mapping as $pair ) {
			$id1_arr = array_keys($pair);
			$id1 = $id1_arr[0];
			$id2 = $pair[$id1];
			$label1 = $this->epc1->getNodeLabel($id1);
			$label2 = $this->epc2->getNodeLabel($id2);
			$content .= "\r\n".$label1." (".$id1.") | ".$label2." (".$id2.")";
		}
	
		$fileGenerator = new FileGenerator($path.trim($this->epc1->name)."_".trim($this->epc2->name).".txt", $content);
		$fileGenerator->setFilename(trim($this->epc1->name)."_".trim($this->epc2->name).".txt");
		$fileGenerator->setPath($path);
		$file = $fileGenerator->execute($prefix);
		return $file;
	}
	
}
?>