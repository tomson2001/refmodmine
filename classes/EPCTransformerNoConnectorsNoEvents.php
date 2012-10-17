<?php
class EPCTransformerNoConnectorsNoEvents {

	protected $epc;

	public function __construct() {

	}

	public function transform(EPC $epc) {
		$this->epc = $epc;
		$this->removeEvents();
		$this->removeConnectors();
		$this->removeDuplicatedEdges();
		return $this->getTransformedEpc();
	}

	/**
	 * Entfernt alle Ereignisse in den Kanten
	 */
	protected function removeEvents() {
		foreach ( $this->epc->events as $eventID => $eventLabel ) {
			foreach ( $this->epc->edges as $index => $edge ) {
				// Event ist Quelle
				if ( array_key_exists($eventID, $edge) ) {
					$target = $edge[$eventID];
					$source = $this->epc->getPredecessor($eventID);
					if ( !empty($source) ) {
						$source = $source[0];
						$newEdge = array($source => $target);
						$this->epc->edges[$index] = $newEdge;
					} else {
						unset($this->epc->edges[$index]);
					}
				}
				// Event ist Ziel
				$flipped = array_flip($edge);
				if ( array_key_exists($eventID, $flipped) ) {
					$source = $flipped[$eventID];
					$target = $this->epc->getSuccessor($eventID);
					if ( !empty($target) ) {
						$target = $target[0];
						$newEdge = array($source => $target);
						$this->epc->edges[$index] = $newEdge;
					} else {
						unset($this->epc->edges[$index]);
					}
				}
			}
			unset($this->epc->events[$eventID]);
		}
	}

	/**
	 * Entfernt alle Konnektoren
	 */
	protected function removeConnectors() {
		$connectors = $this->epc->getAllConnectors();
		foreach ( $connectors as $connectorID => $connectorLabel ) {
			$sources = $this->epc->getPredecessor($connectorID);
			$targets = $this->epc->getSuccessor($connectorID);
			foreach ( $sources as $sourceNodeID ) {
				foreach ( $targets as $targetNodeID ) {
					$newEdge = array($sourceNodeID => $targetNodeID);
					array_push($this->epc->edges, $newEdge);
				}
			}
			foreach ( $this->epc->edges as $index => $edge ) {
				if ( array_key_exists($connectorID, $edge) || array_key_exists($connectorID, array_flip($edge)) ) {
					unset($this->epc->edges[$index]);
				}
			}
			switch ($connectorLabel) {
				case "xor": unset($this->epc->xor[$connectorID]); break;
				case "or": unset($this->epc->or[$connectorID]); break;
				case "and": unset($this->epc->and[$connectorID]); break;
			}
		}
	}

	/**
	 * Gibt die transformiete EPK zurueck
	 *
	 * @return EPC
	 */
	public function getTransformedEpc() {
		return $this->epc;
	}
	
	public function removeDuplicatedEdges() {
		foreach ( $this->epc->edges as $index => $edge ) {
			foreach ( $edge as $sourceNodeID => $targetNodeID ) {
				if ( $this->countEdge($sourceNodeID, $targetNodeID) > 1 ) {
					unset($this->epc->edges[$index]);
				}
			}
		}
	}
	
	private function countEdge($sourceNodeID, $targetNodeID) {
		$countEdge = 0;
		foreach ( $this->epc->edges as $index => $edge ) {
			foreach ( $edge as $sourceID => $targetID ) {
				if ( $sourceID == $sourceNodeID && $targetID == $targetNodeID ) {
					$countEdge++;
				}
			}
		}
		return $countEdge;
	}

}
?>