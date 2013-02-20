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
	 *
	 * Moegliche Optionen: IGNORE_START_EVENTS, IGNORE_END_EVENTS
	 */
	protected function removeEvents($options = array()) {

		// Events, die auf diese Konnektoren folgen, koennen zusammengefasst werden
		$connectorFollowingEventsToReduce = array();
		$reduceStartEventsFollowedByConnector = array();

		// Start- und Endereignisse ermittelt
		$startNodes = $this->epc->getAllStartNodes();
		$endNodes = $this->epc->getAllEndNodes();

		foreach ( $this->epc->events as $eventID => $eventLabel ) {

			// IGNORE_START_EVENTS
			if ( in_array("IGNORE_START_EVENTS", $options) ) {
				if ( array_key_exists($eventID, $startNodes) ) continue;
			}
				
			// IGNORE_TRACE_IMPORTANT_START_EVENTS
			if ( in_array("IGNORE_TRACE_IMPORTANT_START_EVENTS", $options) ) {
				if ( array_key_exists($eventID, $startNodes) ) {
					$succ = $this->epc->getSuccessor($eventID);
					if ( count($succ) == 1 ) {
						$succ = $succ[0];
						if ( $this->epc->isOr($succ) || $this->epc->isXor($succ) ) {
							// Nachfolger ist ein XOR- oder OR-Konnektor
							if ( !isset($reduceStartEventsFollowedByConnector[$succ]) ) $reduceStartEventsFollowedByConnector[$succ] = array();
							array_push($reduceStartEventsFollowedByConnector[$succ], $eventID);
							continue;
						}
					}
				}
			}

			// IGNORE_END_EVENTS
			if ( in_array("IGNORE_END_EVENTS", $options) ) {
				if ( array_key_exists($eventID, $endNodes) ) continue;
			}

			// IGNORE_TRACE_IMPORTANT_END_EVENTS
			if ( in_array("IGNORE_TRACE_IMPORTANT_END_EVENTS", $options) ) {
				if ( array_key_exists($eventID, $endNodes) ) {
					$pred = $this->epc->getPredecessor($eventID);
					if ( count($pred) == 1 ) {
						$pred = $pred[0];
						if ( $this->epc->isConnector($pred) ) {
							// Vorgaenger ist ein Konnektor
							if ( !isset($connectorFollowingEventsToReduce[$pred]) ) $connectorFollowingEventsToReduce[$pred] = array();
							array_push($connectorFollowingEventsToReduce[$pred], $eventID);
							continue;
						}
					}
				}
			}

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
		$this->reduceConnectorFollowingEndEvents($connectorFollowingEventsToReduce);
		$this->reduceStartEventsFollowedByConnectors($reduceStartEventsFollowedByConnector);
	}

	protected function reduceStartEventsFollowedByConnectors($reduceStartEventsFollowedByConnector) {
		foreach ( $reduceStartEventsFollowedByConnector as $connID => $events ) {
			foreach ( $events as $index => $event ) {
				if ( $index == 0 ) {
					$this->epc->events[$event] = "Start Event";
				} else {
					$this->epc->deleteEvent($event);
				}
			}
		}
	}

	protected function reduceConnectorFollowingEndEvents($connectorFollowingEventsToReduce) {
		foreach ( $connectorFollowingEventsToReduce as $connID => $events ) {
			foreach ( $events as $index => $event ) {
				if ( $index == 0 ) {
					$this->epc->events[$event] = "End Event";
				} else {
					$this->epc->deleteEvent($event);
				}
			}
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

	/**
	 * Entfernt alle Konnektoren aus der EPK, die genau eine eingehende UND
	 * genau eine ausgehende Kante haben
	 */
	protected function removeSESEConnectors() {
		$connectors = $this->epc->getAllConnectors();
		foreach ( $connectors as $nodeID => $type ) {
			$sources = $this->epc->getPredecessor($nodeID);
			$targets = $this->epc->getSuccessor($nodeID);
			if ( count($sources) == 1 && count($targets) == 1 ) {

				// Kanten die zum Konnektor hinfuehren entfernen
				$this->epc->deleteEdge($sources[0], $nodeID);
				$this->epc->deleteEdge($nodeID, $targets[0]);

				// Konnektor entfernen
				switch ($type) {
					case "xor": unset($this->epc->xor[$nodeID]); break;
					case "or": unset($this->epc->or[$nodeID]); break;
					case "and": unset($this->epc->and[$nodeID]); break;
				}

				// neue Kante vom Vorgaenger zum Nachfolgen des alten Konntektors hinzufuegen
				$newEdge = array($sources[0] => $targets[0]);
				array_push($this->epc->edges, $newEdge);
			}
		}
	}

	/**
	 * Entfernt alle Konnektoren, die eine eingehende und keine ausgehende bzw. keine eingehende und eine ausgehende Kante haben
	 */
	protected function removeSenselessConnectors() {
		$somethingDone = false;
		$connectors = $this->epc->getAllConnectors();
		foreach ( $connectors as $nodeID => $type ) {
			$sources = $this->epc->getPredecessor($nodeID);
			$targets = $this->epc->getSuccessor($nodeID);
			if ( (count($sources) == 0 && count($targets) == 1) ) {
				// Kanten die zum Konnektor hinfuehren entfernen
				$this->epc->deleteEdge($nodeID, $targets[0]);

				// Konnektor entfernen
				switch ($type) {
					case "xor": unset($this->epc->xor[$nodeID]); break;
					case "or": unset($this->epc->or[$nodeID]); break;
					case "and": unset($this->epc->and[$nodeID]); break;
				}

				$somethingDone = true;
			}

			if ( count($sources) == 1 && count($targets) == 0 ) {
				// Kanten die zum Konnektor hinfuehren entfernen
				$this->epc->deleteEdge($sources[0], $nodeID);
					
				// Konnektor entfernen
				switch ($type) {
					case "xor": unset($this->epc->xor[$nodeID]); break;
					case "or": unset($this->epc->or[$nodeID]); break;
					case "and": unset($this->epc->and[$nodeID]); break;
				}

				$somethingDone = true;
			}
		}
		if ( $somethingDone ) {
			$this->removeSenselessConnectors();
		} else {
			return;
		}
	}

	protected function tryToCorrectErrors() {
		$connectors = $this->epc->getAllConnectors();
		foreach ( $connectors as $nodeID => $type ) {
			$sources = $this->epc->getPredecessor($nodeID);
			$targets = $this->epc->getSuccessor($nodeID);
			/**
			 * Problem, wenn ein Konnektor mehr als einen Eingangsknoten UND mehr als einen Ausgangsknoten hat
			 * ==> Zwei gleiche Konnektoren daraus machen, einen Join und einen Split
			*/
			if ( (count($sources) > 1 && count($targets) > 1) ) {
				// Neuen Split-Konnektor erzeugen
				$newConnectorID = $this->epc->getFreeNodeID();
				switch ( $type ) {
					case "xor": $this->epc->xor[$newConnectorID] = $type; break;
					case "or": $this->epc->or[$newConnectorID] = $type; break;
					case "and": $this->epc->and[$newConnectorID] = $type; break;
				}


				// Ausgehende Pfade vom urspruenglichen Konnektor entfernen und an den neuen Split Konnektor dranhaengen
				foreach ( $targets as $target ) {
					$this->epc->addEdge($newConnectorID, $target);
					$this->epc->deleteEdge($nodeID, $target);
				}

				// Die beiden Konnektoren verbinden
				$this->epc->addEdge($nodeID, $newConnectorID);
			}
		}
	}

}
?>