<?php
/**
 * Ein N-Aeres Mapping, basierend auf der Ontology und WordNet
 * Im Wesentlichen handelt es sich hier um ein Clustering
 *
 * @author Tom Thaler
 */
class NAryWordstemMapping extends ANAryMapping implements INAryMapping {
	
	// Best Value
	private $threshold_ontology_quote = 60;


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
					$nodeSimilarity = $this->compare($node1, $node2);
					//print("\n ".$node1->label." <=> ".$node2->label." | ".$nodeSimilarity);
					if ( $nodeSimilarity >= $this->threshold_ontology_quote ) {
						$this->cluster($node1, $node2);
					}
				}
				$j++;
			}
			$i++;
			$j = $i + 1;
		}
		$this->exportDebug("_complete");
		//print("\nEs werden folgende Knoten ignoriert:  ");
		$this->cleanClusters();
		$this->exportDebug("_reduced");
		//$this->printDebug();
	}

	private function compare(FunctionOntologyWithSynonyms $node1, FunctionOntologyWithSynonyms $node2) {

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
	 * Fuehrt das Clustering der beiden Knoten durch. Dabei wird auch sichergestellt, dass jeder Knoten
	 * in genau einem Cluster ist-
	 *
	 * @param FunctionOntologyWithSynonyms $node1
	 * @param FunctionOntologyWithSynonyms $node2
	 */
	private function cluster(FunctionOntologyWithSynonyms $node1, FunctionOntologyWithSynonyms $node2) {
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
				$this->mergeCluster($clusterIndexOfNode1, $clusterIndexOfNode2);
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