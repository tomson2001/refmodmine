<?php
/**
 * Ein N-Aeres Mapping, basierend auf der Ontology und WordNet
 * Im Wesentlichen handelt es sich hier um ein Clustering
 *
 * @author Tom Thaler
 */
class NAryOntologyMappingContestGoldV2 extends ANAryMapping implements INAryMapping {

	// Best Value
	//private $threshold_ontology_quote = 60;
	
	private $threshold_ontology_quote = 75; // 29 Cluster, Precision:1, Recall 0,19

	//private $threshold_ontology_quote = 55;
	
	private $threshold_wordstem = 60;
	private $threshold_additional_cluster = 75;
	
	private $threshold_component_quote = 20;

	/**
	 * Setzt den Threshold-Parameter
	 *
	 * @param array $params muss dan Threshold-Parameter [0;100] in der Form array('threshold' => 50) enthalten
	 * @return boolean
	 */
	public function setParams(Array $params) {
		$paramSet = false;
		if ( isset($params['threshold_ontology_quote']) ) {
			$this->threshold_ontology_quote = $params['threshold_ontology_quote'];
			$paramSet = true;
		}

		return $paramSet;
	}

	public function map() {
		// Funktionsontologien bauen und alles in ein Array schreiben
		$allFunctions = array();
		foreach ( $this->epcs as $epc ) {
			foreach ( $epc->functions as $id => $label ) {
				array_push($allFunctions, new FunctionOntologyWithSynonyms($epc, $id, $label));
			}
		}
		print("Synonymextraktion abgeschlossen");
				
		// Clusterbildung durch Vergleich aller Funktionspaare
		$i = 0;
		$j = 1;
		$numOfAllFuncs = count($allFunctions);
		while ( $i < $numOfAllFuncs ) {
			$node1 = $allFunctions[$i];
			while ( $j < $numOfAllFuncs ) {
				//print(".".$i."-".$j.".");
				$node2 = $allFunctions[$j];
				// Similarity nur dann berechnen, wenn es sich um Knoten aus verschiedenen EPKs handelt
				if ( $node1->epc->name != $node2->epc->name ) {
					$wordstemSimilarity = $this->compareWordstems($node1, $node2);
					//$nodeSimilarity = $this->compare($node1, $node2);
					//print("\n ".$node1->label." <=> ".$node2->label." | ".$nodeSimilarity);
					//if ( $nodeSimilarity >= $this->threshold_ontology_quote || $wordstemSimilarity >= $this->threshold_wordstem ) {
					if ( $wordstemSimilarity >= $this->threshold_wordstem ) {
						$this->cluster($node1, $node2);
					}
				}
				$j++;
			}
			$i++;
			$j = $i + 1;
		}
		
		// Schauen ob durch die bisher nicht gematchten Knoten auf semantische Weise weitere Cluster ermittelt werden koennen
		foreach ( $allFunctions as $node1 ) {
			foreach ( $allFunctions as $node2 ) {
				if ( $node1->epc->name != $node2->epc->name && !$this->isClustered($node1) && !$this->isClustered($node2) ) {
					$nodeSimilarity = $this->compare($node1, $node2);
					if ( $nodeSimilarity >= $this->threshold_ontology_quote ) {
						$stemSim = $this->compareWordstems($node1, $node2);
						print("\nAdd Cluster: <".$node1->epc->name."> ".$node1->label." <=> <".$node2->epc->name."> ".$node2->label." [Sem. ".$nodeSimilarity."][Stem. ".$stemSim."]");
						$this->cluster($node1, $node2, true);
					}
				}
			}
		}
		
		// Nochmal schauen, ob die Cluster durch Semantische Verfahren angereichert werden koenen
		foreach ( $allFunctions as $node1 ) {
			if ( !$this->isClustered($node1) ) {
				foreach ( $this->clusters as $cluster ) {
					foreach ( $cluster->nodes as $node2 ) {
						$nodeSimilarity = $this->compare($node1, $node2);
						if ( $node1->epc->name != $node2->epc->name && $nodeSimilarity >= $this->threshold_ontology_quote ) {
							print("\nCluster: <".$node1->epc->name."> ".$node1->label." <=> <".$node2->epc->name."> ".$node2->label." [Sem. ".$nodeSimilarity."][Stem. ".$stemSim."]");
							$this->cluster($node1, $node2, true);
						}
					}
				}				
			}
		}
		
		$this->exportDebug("_complete");
		//print("\nEs werden folgende Knoten ignoriert:  ");
		$this->cleanClusters();
		print("\nAnzahl Cluster: ".count($this->clusters)."\n");
		$this->exportDebug("_reduced");
		//$this->printDebug();
	}

	private function compare(FunctionOntologyWithSynonyms $node1, FunctionOntologyWithSynonyms $node2) {

		// Dummy-Transitionen nicht matchen
		if ( preg_match("/^t[0-9]*$/", $node1->label) || preg_match("/^t[0-9]*$/", $node2->label) ) return 0;

		// Wenn ein Label die Verneinung des anderen Labels ist, dann nicht matchen (Antonym)
		if ( $this->areAntonyms($node1, $node2) ) return 0;

		// auf Ereignisse pruefen
		if ( $node1->couldBeEvent() || $node2->couldBeEvent() ) return 0;

		$numWordsOfNode1 = count($node1->synonyms);
		$numWordsOfNode2 = count($node2->synonyms);
		$countWordstemsOfLabel1 = count($node1->wordstems);
		$countWordstemsOfLabel2 = count($node2->wordstems);

		$nodeSwitched = false;

		/**
		 * Aehnlichkeit ueber Synonyme bestimmen
		 */
			

		$sumWords = $numWordsOfNode1 + $numWordsOfNode2;
		if ( $sumWords > 0 ) {

			// Die kleinere Menge an die erste Stelle setzen
			if ( $numWordsOfNode1 > $numWordsOfNode2 ) {
				$node_temp = $node1;
				$node1 = $node2;
				$node2 = $node_temp;
				$nodeSwitched = true;
			}
			$matchedWords = 0;
			foreach ( $node1->synonyms as $syns1 ) {
				foreach ( $node2->synonyms as $syns2 ) {
					// gleiche Elemente
					$numIntersection = count(array_intersect($syns1, $syns2));
					if ( $numIntersection > 0 ) {
						$matchedWords++;
						break;
					}
				}
			}
			$synSimilarity = round((2*$matchedWords / ($numWordsOfNode1 + $numWordsOfNode2))*100, 2);
		} else {
			$synSimilarity = 0;
		}
			
			
		/**
		 * Neues Vorgehen: Pruefe, ob ein Verb aus dem ersten Knoten auf ein Verb aus dem zweiten Knoten gematcht werden kann.
		 * Das gleiche wird dann noch fuer Nomen gemacht. Ist das der Fall, dann ist Verb- bzw. NomenSimilarity = 1, andernfalls
		 * = 0. Nomen und Verben werden dabei gleich, also mit jeweils 50% Gewichtet. Ist eines der beiden Dinge in einem der
		 * beiden Knoten nicht vorhanden, also beispielsweise enthaelt der erste Knoten nur ein Verb und kein Nomen, so wird
		 * dem Verb-Matching die volle Gewichtung zugesprochen.
		 */

		// Verb-Matching
		$numVerbsOfNode1 = count($node1->verbs);
		$numVerbsOfNode2 = count($node2->verbs);
		$sumVerbs = $numVerbsOfNode1 + $numVerbsOfNode2;

		if ( $sumVerbs > 0 ) {
			$verbs1 = $node1->verbs;
			$verbs2 = $node2->verbs;
			if ( $numVerbsOfNode1 > $numVerbsOfNode2 ) {
				// Label1 muss immer dasjenigen mit der geringeren Anzahl an Komponenten (Woertern) sein
				$verbs2 = $node1->verbs;
				$verbs1 = $node2->verbs;
			}
			$matchedVerbs = 0;
			foreach ( $verbs1 as $syns1 ) {
				foreach ( $verbs2 as $syns2 ) {
					// gleiche Elemente
					$numIntersection = count(array_intersect($syns1, $syns2));
					if ( $numIntersection > 0 ) {
						$matchedVerbs++;
						break;
					}
				}
			}
			$verbSimilarity = round((2*$matchedVerbs / ($sumVerbs))*100, 2);
		} else {
			$verbSimilarity = 0;
		}

		// Nomen-Matching
		$numNounsOfNode1 = count($node1->nouns);
		$numNounsOfNode2 = count($node2->nouns);
		$sumNouns = $numNounsOfNode1 + $numNounsOfNode2;

		if ( $sumNouns > 0 ) {
			$nouns1 = $node1->nouns;
			$nouns2 = $node2->nouns;
			if ( $numVerbsOfNode1 > $numVerbsOfNode2 ) {
				// Label1 muss immer dasjenigen mit der geringeren Anzahl an Komponenten (Woertern) sein
				$nouns2 = $node1->nouns;
				$nouns1 = $node2->nouns;
			}
			$matchedNouns = 0;
			foreach ( $nouns1 as $syns1 ) {
				foreach ( $nouns2 as $syns2 ) {
					// gleiche Elemente
					$numIntersection = count(array_intersect($syns1, $syns2));
					if ( $numIntersection > 0 ) {
						$matchedNouns++;
						break;
					}
				}
			}
			$nounSimilarity = round((2*$matchedNouns / ($sumNouns))*100, 2);
		} else {
			$nounSimilarity = 0;
		}

		// Semantische Aehnlichkeit
		if ( $sumNouns == 0 ) {
			$semSimilarity = $verbSimilarity;
		} else {
			$semSimilarity = round((0.5*$verbSimilarity) + (0.5*$nounSimilarity), 2);
		}
			
		/**
		 * Aehnlichkeit ueber Porter-Stems bestimmen
		 */

		if ( $nodeSwitched ) {
			// Wenn Knoten getauscht wurden, dann rueckgaengig machen
			$node_temp = $node1;
			$node1 = $node2;
			$node2 = $node_temp;
		}
		if ( $countWordstemsOfLabel1 > $countWordstemsOfLabel2 ) {
			// Label1 muss immer dasjenigen mit der geringeren Anzahl an Komponenten (Woertern) sein
			$node_temp = $node1;
			$node1 = $node2;
			$node2 = $node_temp;
		}
		$countWordstemMappings = 0;
		foreach ( $node1->wordstems as $wordstem1 ) {
			foreach ( $node2->wordstems as $wordstem2 ) {
				if ( $wordstem1 == $wordstem2 ) {
					$countWordstemMappings++;
					break;
				}
			}
		}

		$stemSimilarity = round((2*$countWordstemMappings / ($countWordstemsOfLabel1 + $countWordstemsOfLabel2))*100, 2);

		// Quote der Woerter, zu denen Synonyme erzeugt werden konnten
		$componentQuoteOfLabel1 = $numWordsOfNode1 / $countWordstemsOfLabel1;
		$componentQuoteOflabel2 = $numWordsOfNode2 / $countWordstemsOfLabel2;
		$overallQuote = ($numWordsOfNode1 + $numWordsOfNode2) / ($countWordstemsOfLabel1 + $countWordstemsOfLabel2);
		if ( $componentQuoteOfLabel1*100 >= $this->threshold_component_quote && $componentQuoteOflabel2*100 >= $this->threshold_component_quote ) {
			//$synWeight = ($componentQuoteOfLabel1+$componentQuoteOflabel2) / 2;
			$synWeight = $overallQuote;
			$stemWeight = 1 - $synWeight;
		} else {
			$synWeight = 0;
			$stemWeight = 1;
		}

		//$similarity = round(($stemWeight*$stemSimilarity)+($synWeight*$semSimilarity), 2);
		//$similarity = round(($semSimilarity+$stemSimilarity)/2, 2);
		$similarity = $semSimilarity;
		//$similarity = round(($synSimilarity+$semSimilarity+$stemSimilarity)/3, 2);

		//if ( $node1->label == "matriculate" && $node2->label == "Send Documents by Post") {
		if ( $similarity >= $this->threshold_ontology_quote) {
			//print("\nSim: ".$similarity." (".$node1->label." <=> ".$node2->label.")");
			//print("\nAnzahl Woerter mit Synonymen in Label 1: ".$numWordsOfNode1." | Label 2: ".$numWordsOfNode2." | SynSim: ".round($synSimilarity,2));
			//print("\nVerbSim: ".$verbSimilarity." | NounSim: ".$nounSimilarity." (".$sumNouns." Nouns) | SemSim: ".$semSimilarity);
			//print("\nAnzahl Woerter in Label 1: ".$countWordstemsOfLabel1." | Label 2: ".$countWordstemsOfLabel2." | StemSim: ".round($stemSimilarity,2)."\n");
		}

		/**
		 if ( $node2->label == "assessment of application" && $node1->label == "Evaluate") {
			print("\nAnzahl Woerter mit Synonymen in Label 1: ".$numWordsOfNode1);
			print("\nAnzahl Woerter mit Synonymen in Label 2: ".$numWordsOfNode2);
			print("\nSynSim: ".$synSimilarity."\n");
			print("\nAnzahl Woerter in Label 1: ".$countWordstemsOfLabel1);
			print("\nAnzahl Woerter in Label 2: ".$countWordstemsOfLabel2);
			print("\nStemSim: ".$stemSimilarity."\n");
			print("\nSim: ".$similarity."\n");
			}
			*/

		return $similarity;
	}
	
	private function compareWordstems(FunctionOntologyWithSynonyms $node1, FunctionOntologyWithSynonyms $node2) {
	
		// Dummy-Transitionen nicht matchen
		if ( preg_match("/^t[0-9]*$/", $node1->label) || preg_match("/^t[0-9]*$/", $node2->label) ) return 0;
	
		// Wenn ein Label die Verneinung des anderen Labels ist, dann nicht matchen
		if ( in_array("not", $node1->wordstems) && !in_array("not", $node2->wordstems) ) return 0;
		if ( !in_array("not", $node1->wordstems) && in_array("not", $node2->wordstems) ) return 0;
			
		/**
		 * Aehnlichkeit ueber Porter-Stems bestimmen
		 */
	
		$countWordstemsOfLabel1 = count($node1->wordstems);
		$countWordstemsOfLabel2 = count($node2->wordstems);
		if ( $countWordstemsOfLabel1 > $countWordstemsOfLabel2 ) {
			// Label1 muss immer dasjenigen mit der geringeren Anzahl an Komponenten (Woertern) sein
			$node_temp = $node1;
			$node1 = $node2;
			$node2 = $node_temp;
		}
		$countWordstemMappings = 0;
		foreach ( $node1->wordstems as $wordstem1 ) {
			foreach ( $node2->wordstems as $wordstem2 ) {
				if ( $wordstem1 == $wordstem2 ) {
					$countWordstemMappings++;
					break;
				}
			}
		}
	
		$stemSimilarity = round((2*$countWordstemMappings / ($countWordstemsOfLabel1 + $countWordstemsOfLabel2))*100, 2);
		return $stemSimilarity;
	
	}

	/**
	 * Prueft zwei Knoten auf Antonyme (Gegensaetze)
	 *
	 * @param unknown_type $node1
	 * @param unknown_type $node2
	 *
	 * @return boolean
	 */
	private function areAntonyms(&$node1, &$node2) {
		if ( in_array("not", $node1->wordstems) && !in_array("not", $node2->wordstems) ) {
			//print("\nAntonym manual: ".$node1->label." <=> ".$node2->label);
			return true;
		}
		if ( !in_array("not", $node1->wordstems) && in_array("not", $node2->wordstems) ) {
			//print("\nAntonym manual: ".$node1->label." <=> ".$node2->label);
			return true;
		}
		foreach ( $node1->synonyms as $syns ) {
			foreach ( $node2->antonyms as $ants ) {
				// gleiche Elemente
				$numIntersection = count(array_intersect($syns, $ants));
// 				if ( ($node2->label == "receive rejection" && $node1->label == "receive acceptance") || ($node1->label == "receive rejection" && $node2->label == "receive acceptance") ) {
// 					print("\n1: ".$numIntersection."\n");
// 					print_r($ants);
// 					print_r($syns);
// 				}
				if ( $numIntersection > 0
						//	&& ((!in_array("not", $node1->wordstems) && !in_array("not", $node2->wordstems)) || (in_array("not", $node1->wordstems) && in_array("not", $node2->wordstems)))
				) {
					//print("\nAntonym automatic 1: ".$node1->label." <=> ".$node2->label);
					return true;
				}
			}
		}
		foreach ( $node1->antonyms as $ants ) {
			foreach ( $node2->synonyms as $syns ) {
				// gleiche Elemente
				$numIntersection = count(array_intersect($syns, $ants));
// 				if ( ($node2->label == "receive rejection" && $node1->label == "receive acceptance") || ($node1->label == "receive rejection" && $node2->label == "receive acceptance") ) {
// 					print("\n2: ".$numIntersection."\n");
// 					print_r($ants);
// 					print_r($syns);
// 				}
				if ( $numIntersection > 0
						//	&& ((!in_array("not", $node1->wordstems) && !in_array("not", $node2->wordstems)) || (in_array("not", $node1->wordstems) && in_array("not", $node2->wordstems)))
				) {
					//print("\nAntonym automatic 2: ".$node1->label." <=> ".$node2->label);
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Fuehrt das Clustering der beiden Knoten durch. Dabei wird auch sichergestellt, dass jeder Knoten
	 * in genau einem Cluster ist-
	 *
	 * @param FunctionOntologyWithSynonyms $node1
	 * @param FunctionOntologyWithSynonyms $node2
	 */
	private function cluster(FunctionOntologyWithSynonyms $node1, FunctionOntologyWithSynonyms $node2, $abortMerges = false) {
		$clusterIndexOfNode1 = $this->searchClusterForNode($node1);
		$clusterIndexOfNode2 = $this->searchClusterForNode($node2);

		if ( is_null($clusterIndexOfNode1) && is_null($clusterIndexOfNode2) ) {
			// Keiner der Knoten befindet sich in einem Cluster
			$clusterIndex = $this->addCluster();
			$this->clusters[$clusterIndex]->addNode($node1);
			$this->clusters[$clusterIndex]->addNode($node2);
		} elseif ( !is_null($clusterIndexOfNode1) && !is_null($clusterIndexOfNode2) ) {
			// Beide Knoten befinden sich in einem Cluster
			// Wenn unterschiedliche Cluster, dann fuege diese zusammen
			if ( $clusterIndexOfNode1 != $clusterIndexOfNode2 ) {
				if ( !$abortMerges ) {
					$this->mergeCluster($clusterIndexOfNode1, $clusterIndexOfNode2);
				} else {
					$sim = $this->compare($node1, $node2);
					print("\nCluster-Merge von C".$clusterIndexOfNode1." und C".$clusterIndexOfNode2." unterbunden. (<".$node1->epc->name."> ".$node1->label." <=> <".$node2->epc->name."> ".$node2->label." [".$sim."])");
				}
			}
		} else {
			// Genau einer der beiden Knoten befindet sich in einem Cluster
			$clusterIndex = is_null($clusterIndexOfNode1) ? $clusterIndexOfNode2 : $clusterIndexOfNode1;
			$this->clusters[$clusterIndex]->addNode($node1);
			$this->clusters[$clusterIndex]->addNode($node2);
		}

	}

	/**
	 * Sucht nach einem Cluster, dass den uebergebenen Funktionsknoten enthaelt und gib den Index des Cluster
	 * im Cluster-Array zurueck. Wenn kein Cluster gefunden wird, wird null zurueckgegeben.
	 *
	 * @param FunctionOntologyWithSynonyms $node
	 * @return int|NULL
	 */
	private function searchClusterForNode(FunctionOntologyWithSynonyms $node) {
		foreach ( $this->clusters as $clusterIndex => $cluster ) {
			if ( $cluster->contains($node) ) return $clusterIndex;
		}
		return null;
	}

	/**
	 * Erstellt ein neues Cluster und gibt den Index des Clusters
	 * aus dem Cluster-Array ($clusters) zurueck
	 *
	 * @return int
	 */
	private function addCluster() {
		$nextClusterIndex = empty($this->clusters) ? 0 : max(array_keys($this->clusters))+1;
		$this->clusters[$nextClusterIndex] = new NodeCluster();
		return $nextClusterIndex;
	}

	/**
	 * Fuegt zwei Cluster zusammen, indem es alle Knoten aus dem zweiten Cluster
	 * in das erste Cluster einfuegt und das zweite Cluster dann entfernt.
	 *
	 * @param int $clusterIndex1 Index des ersten Clusters
	 * @param int $clusterIndex2 Index des zweiten Clusters
	 */
	private function mergeCluster($clusterIndex1, $clusterIndex2) {
		foreach ( $this->clusters[$clusterIndex2]->nodes as $node ) {
			$this->clusters[$clusterIndex1]->addNode($node);
		}
		unset($this->clusters[$clusterIndex2]);
	}
	
	private function isClustered(&$node) {
		foreach ( $this->clusters as $clusterIndex => $cluster ) {
			if ( $cluster->contains($node) ) {
				return true;
			}
		}
	}

	public function extractBinaryMapping(&$epc1, &$epc2) {
		$binaryMapping = new BinaryMapping($epc1, $epc2);
		$binaryMapping->setParams(array('clusters' => $this->clusters));
		return $binaryMapping;
	}

	public function printDebug() {
		$output = $this->generateDebug();
		print($output);
	}

	public function exportDebug($filename_suffix="") {
		$output = $this->generateDebug();
		$fileGenerator = new FileGenerator("clusters".$filename_suffix.".txt", $output);
		$file = $fileGenerator->execute();
		return $file;
	}

	private function generateDebug() {
		$text = "\r\n\r\nAnzahl Cluster: ".count($this->clusters);
		foreach ( $this->clusters as $index => $cluster ) {
			$text .= "\r\n\r\n Cluster ".$index." enhaelt ".count($cluster->nodes)." Knoten." ;
			foreach ( $cluster->nodes as $node ) {
				$text .= "\r\n          ".$node->label." (".$node->epc->name.")";
			}
		}
		return $text;
	}

	/**
	 * Bereinigt die Cluster. Es werden dabei Funktionen entfernt, deren Labels fuer Ereignisse sprechen
	 */
	private function cleanClusters() {
		foreach ( $this->clusters as $index => $cluster ) {
			//print("\n Cluster ".$index."\n");
			$cluster->removePossibleEvents();
		}
	}

	// 	private function compare(FunctionOntologyWithSynonyms $node1, FunctionOntologyWithSynonyms $node2) {
	// 		$numNounsOfNode1 = count($node1->nouns);
	// 		$numVerbsOfNode1 = count($node1->verbs);
	// 		$numMixedOfNode1 = count($node1->mixed);

	// 		$numNounsOfNode2 = count($node2->nouns);
	// 		$numVerbsOfNode2 = count($node2->verbs);
	// 		$numMixedOfNode2 = count($node2->mixed);

	// 		// Maechtigkeit der Schnittmenge der Nomen berechnen
	// 		$matchedNouns = 0;
	// 		foreach ( $node1->nouns as $nounSyns1 ) {
	// 			foreach ( $node2->nouns as $nounSyns2 ) {
	// 				// gleiche Elemente
	// 				$numIntersection = count(array_intersect($nounSyns1, $nounSyns2));
	// 				if ( $numIntersection > 0 ) $matchedNouns++;
	// 				break;
	// 			}
	// 		}

	// 		// Maechtigkeit der Schnittmenge der Verben berechnen
	// 		$matchedVerbs = 0;
	// 		foreach ( $node1->verbs as $verbSyns1 ) {
	// 			foreach ( $node2->verbs as $verbSyns2 ) {
	// 				// gleiche Elemente
	// 				$numIntersection = count(array_intersect($verbSyns1, $verbSyns2));
	// 				if ( $numIntersection > 0 ) $matchedVerbs++;
	// 				break;
	// 			}
	// 		}

	// 		// Maechtigkeit der Schnittmenge der Mixed Komponenten berechnen
	// 		$matchedMixed = 0;
	// 		foreach ( $node1->mixed as $mixedSyns1 ) {
	// 			foreach ( $node2->mixed as $mixedSyns2 ) {
	// 				// gleiche Elemente
	// 				$numIntersection = count(array_intersect($mixedSyns1, $mixedSyns2));
	// 				if ( $numIntersection > 0 ) $matchedMixed++;
	// 				break;
	// 			}
	// 		}

	// 	}

}
?>