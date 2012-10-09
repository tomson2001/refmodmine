<?php
class EPCTransformerNoConnectorsNoEvents {
	
	protected $epc;
	
	public function __construct() {

	}
	
	public function transform(EPC $epc) {
		$this->epc = $epc;
		$this->removeEvents();
		$this->removeConnectors();
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
			foreach ( $this->epc->edges as $index => $edge ) {
				// Konnektor ist Quelle
				if ( array_key_exists($connectorID, $edge) ) {
					$target = $edge[$connectorID];
					$source = $this->epc->getPredecessor($connectorID);
					if ( !empty($source) ) {
						foreach ( $source as $sourceNodeID ) {
							$newEdge = array($sourceNodeID => $target);
							array_push($this->epc->edges, $newEdge);
						}
					}
					unset($this->epc->edges[$index]);
				}
				// Event ist Ziel
				$flipped = array_flip($edge);
				if ( array_key_exists($connectorID, $flipped) ) {
					$source = $flipped[$connectorID];
					$target = $this->epc->getSuccessor($connectorID);
					if ( !empty($target) ) {
						foreach ( $target as $targetNodeID ) {
							$newEdge = array($source => $targetNodeID);
							array_push($this->epc->edges, $newEdge);
						}
					}
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
	
}
?>