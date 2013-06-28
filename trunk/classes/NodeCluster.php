<?php
class NodeCluster {

	public $nodes = array();

	public function __construct() {

	}

	public function addNode(FunctionOntologyWithSynonyms $node) {
		if ( !$this->contains($node) ) array_push($this->nodes, $node);
	}

	public function contains(FunctionOntologyWithSynonyms $node) {
		foreach ( $this->nodes as $clusterNode ) {
			if ( $node->id == $clusterNode->id && $node->epc->name == $clusterNode->epc->name ) return true;
		}
		return false;
	}

	/**
	 * Prueft, ob Knoten beider EPKs im Cluster enthalten sind. Ist dies der Fall
	 * wird ein Array mit den IDs der Knoten der beiden EPKs zurueckgegeben.
	 * (also bspw. array(epk1 => knoten1, knoten2; epk2 => knoten1)
	 *
	 * Andersfalls, also wenn nur Knoten der einer der beiden EPKs oder keine Knoten
	 * der beiden EPKs enthalten sind, wie ein leeres Array zurueckgegeben
	 *
	 * @param EPC $epc1
	 * @param EPC $epc2
	 * @return array
	 */
	public function checkForNodesOfBothEPCs(&$epc1, &$epc2) {
		$nameOfEPC1 = $epc1->name;
		$nameOfEPC2 = $epc2->name;
		$nodesOfEPC1InCluster = array();
		$nodesOfEPC2InCluster = array();
		foreach ( $this->nodes as $node ) {
			if ( $node->epc->name == $nameOfEPC1 ) array_push($nodesOfEPC1InCluster, $node->id);
			if ( $node->epc->name == $nameOfEPC2 ) array_push($nodesOfEPC2InCluster, $node->id);
		}
		if ( count($nodesOfEPC1InCluster) > 0 && count($nodesOfEPC2InCluster) > 0 ) {
			return array(0 => $nodesOfEPC1InCluster, 1 => $nodesOfEPC2InCluster);
		} else {
			return array();
		}
	}
	
	/** 
	 * Entfernt alle Funktionsknoten aus dem Cluster, welche wahrscheinlich Ereignisse repraesentieren
	 */
	public function removePossibleEvents() {
		foreach ( $this->nodes as $index => $node ) {
			if ( $node->couldBeEvent() ) {
				//print("\n  ".$node->label);
				unset($this->nodes[$index]);
			}
		}
	}

}
?>