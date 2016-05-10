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
			if ( $node->id == $clusterNode->id && $node->epc->internalID == $clusterNode->epc->internalID ) return true;
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
		$internalIDOfEPC1 = $epc1->internalID;
		$internalIDOfEPC2 = $epc2->internalID;
		//print("\n\n".$internalIDOfEPC1."\n".$internalIDOfEPC2);
		$nodesOfEPC1InCluster = array();
		$nodesOfEPC2InCluster = array();
		foreach ( $this->nodes as $node ) {
			//print($epc1->internalID." --- ".$node->epc->internalID."\n");
			if ( $node->epc->internalID === $internalIDOfEPC1 ) { 
				//print("YES 1: ".$node->epc->internalID."\n");
				array_push($nodesOfEPC1InCluster, $node->id); 
			} else {
				//print("\n  No 1: ".$node->epc->internalID);
			} 
			if ( $node->epc->internalID === $internalIDOfEPC2 ) { 
				//print("YES 2: ".$node->epc->internalID."\n");
				array_push($nodesOfEPC2InCluster, $node->id);
			} else {
				//print("\n  No 2: ".$node->epc->internalID);
			}
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
		return array();
		$possibleEvents = array();
		foreach ( $this->nodes as $index => $node ) {
			if ( $node->couldBeEvent() ) {
				array_push($possibleEvents, $node);
				unset($this->nodes[$index]);
			}
		}
		return $possibleEvents;
	}

}
?>