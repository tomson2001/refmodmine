<?php
/**
 * Diese Klasse generiert aus einer EPK einen Causal Footprint Graph gemaess:
 * 
 * Gongen, Dijkman, Mendling: Measuring similarity between business process models
 *  
 * (insbesondere Seite 48/49)
 */
class CausalFootprintGraph {
	
	private $epc;
	
	public $functions = array();
	public $lookBackLinks = array();
	public $lookAheadLinks = array();
	
	// Gedaechtnis, um Endlosschleifen zu verhindern, welche bei Loops im Modell auftreten wuerden
	public $lookBackMemory = array();
	public $lookAheadMemory = array();
	
	public function __construct(EPC $epc) {
		$this->epc = $epc;
		$this->loadGraph();
	}
	
	/**
	 * Laedt den Causal Footprint Graphen
	 */
	private function loadGraph() {
		foreach ( $this->epc->functions as $id => $label ) {
			array_push($this->functions, $id);
		}
		$this->loadLookBack();
		$this->loadLookAhead();
	}
	
	/**
	 * Stoesst die Rekursion fuer die Generierung der LookBackLinks an
	 */
	private function loadLookBack() {
		foreach ( $this->functions as $id ) {
			$this->addLookBack($id, $id);
		}
	}
	
	/**
	 * Erzeugt LookBackLinks
	 * 
	 * @param mixed $functionID Der Identifier des Knoten, fuer den die LookBackLinks generiert werden
	 * @param mixed $currentID  Der Identifier des aktuellen Knotens in der Rekursion
	 */
	private function addLookBack($functionID, $currentID) {
		if ( $this->alreadyCheckedLookBack($functionID, $currentID) ) {
			return;
		}
		array_push($this->lookBackMemory, array($functionID => $currentID));
		$predecessors = $this->epc->getPredecessor($currentID);
		if ( !empty($predecessors) ) {
			$lookBackLink = array($functionID => $predecessors, "type" => "lookBack");
			array_push($this->lookBackLinks, $lookBackLink);
			foreach ( $predecessors as $predecessorID ) {
				$this->addLookBack($functionID, $predecessorID);
			}
		}
	}
	
	public function alreadyCheckedLookBack($functionID, $currentID) {
		foreach ( $this->lookBackMemory as $pair ) {
			foreach ( $pair as $funcID => $currID ) {
				if ( $funcID == $functionID && $currID == $currentID ) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Stoesst die Rekursion fuer die Generierung der LookBackLinks an
	 */
	private function loadLookAhead() {
		foreach ( $this->functions as $id ) {
			$this->addLookAhead($id, $id);
		}
	}
	
	/**
	 * Erzeugt LookAheadLinks
	 *
	 * @param mixed $functionID Der Identifier des Knoten, fuer den die LookAheadLinks generiert werden
	 * @param mixed $currentID  Der Identifier des aktuellen Knotens in der Rekursion
	 */
	private function addLookAhead($functionID, $currentID) {	
		if ( $this->alreadyCheckedLookAhead($functionID, $currentID) ) {
			return;
		}
		array_push($this->lookAheadMemory, array($functionID => $currentID));
		$successors = $this->epc->getSuccessor($currentID);
		if ( !empty($successors) ) {
			$lookAheadLink = array($functionID => $successors, "type" => "lookAhead");
			array_push($this->lookAheadLinks, $lookAheadLink);
			foreach ( $successors as $successorID ) {
				$this->addLookAhead($functionID, $successorID);
			}
		}
	}
	
	public function alreadyCheckedLookAhead($functionID, $currentID) {
		foreach ( $this->lookAheadMemory as $pair ) {
			foreach ( $pair as $funcID => $currID ) {
				if ( $funcID == $functionID && $currID == $currentID ) {
					return true;
				}
			}
		}
		return false;
	}

}
?>