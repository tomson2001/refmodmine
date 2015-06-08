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