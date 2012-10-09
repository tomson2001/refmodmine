<?php
class MappingChecker {
	
	public $mapping = null;
	
	// Beinhaltet problematischen Knoten
	public $problemNodes = array();

	public function __construct($mapping) {
		$this->mapping = $mapping;
	}
	
	/**
	 * Prüft, ob jede Funktion aus EPK1 auf maximal eine Funktion aus EPK2 gemappt wird
	 * und das jede Funktion aus EPK2 nur maximal einmal getroffen wird (1:1-Mapping)
	 */
	public function checkForOneToOneMapping() {
		// Prüft, ob jede Funktion aus EPK1 auf maximal eine Funktion aus EPK2 gemappt wird
		$maxVerticalValues = array();
		$maxVerticalValuesNodeCount = array();
		
		foreach ( $this->mapping as $label1 => $arr ) {
			$maxLevenshteinSimilarity = Tools::getMaxLevenshteinSimilarity($arr);
			$isNodePreciseMappableFromAtoB = Tools::isNodePreciseMappableFromAtoB($arr, $maxLevenshteinSimilarity);
			foreach ( $arr as $label2 => $value ) {
				// Horizontale
				if ( $value == $maxLevenshteinSimilarity && !$isNodePreciseMappableFromAtoB ) {
					$this->problemNodes[$label1] = $label2;
				}
				
				// Vertikale
				if ( isset($maxVerticalValues[$label2]) ) {
					if ( $value == $maxVerticalValues[$label2] ) {
						array_push($maxVerticalValuesEPC1Nodes[$label2], $label1);
					} elseif ( $value > $maxVerticalValues[$label2] ) {
						$maxVerticalValues[$label2] = $value;
						$maxVerticalValuesEPC1Nodes[$label2] = array($label1);
					}
				} else {
					$maxVerticalValues[$label2] = $value;
					$maxVerticalValuesEPC1Nodes[$label2] = array($label1);
				}
			}	
		}
		
		foreach ($maxVerticalValuesEPC1Nodes as $label2 => $label1Arr) {
			if ( count($label1Arr) > 1 ) {
				foreach ( $label1Arr as $label1 ) {
					$this->problemNodes[$label1] = $label2;
				}
			}
		}
		return $this->getProblemNodePairs();
	}
	
	public function getProblemNodePairs() {
		return $this->problemNodes;
	}
	
	public function getResultString() {
		if ( empty($this->problemNodes) ) {
			echo "<br/><font color=green>Mapping ist eindeutig und kann für alle Algorithmen verwendet werden.</font>";
		} else {
			echo "<br/><font color=red>Mapping ist nicht eindeutig und kann für bestimmte Algorithmen nicht verwendet werden!</font>";
		}
	}
	
}
?>