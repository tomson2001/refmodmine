<?php
abstract class AMapping {
	
	public $epc1 = null;
	public $epc2 = null;
	public $matrix = array();
	public $mapping = array();
	
	/**
	 * Gibt die Funktions-Mapping-Matrix zurueck
	 * 
	 * @return array:
	 */
	public function getMatrix() {
		return $this->matrix;
	}
	
	/**
	 * Gibt das Mapping als Array zurück
	 * @return Array
	 */
	public function getMapping() {
		return $this->mapping;
	}
	
	/**
	 * Prüft, ob zwei Knoten(-Labels) gemappt sind
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
	
	/**
	 * Prüft, ob das Mapping eindeutig ist
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
	
}
?>