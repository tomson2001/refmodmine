<?php
/**
 * Erzeugt ein Mapping zweier EPKs ueber die
 *  - Funktionsknoten, auf Basis der Levenshtein-Distanz der Labels
 *  - Konnektorknoten, auf Basis der Eingangs- und Ausgangsknoten
 *
 * Hierzu werden EPKs ohne Ereignisse benoetigt
 */
class LevenshteinWithContextMapping extends AMapping implements IMapping {

	private $threshold_levenshtein = 0;

	public function __construct(EPC $epc1, EPC $epc2) {
		$transformer = new EPCTransformerNoEvents();
		$epc1 = $transformer->transform($epc1);
		$epc2 = $transformer->transform($epc2);
		$this->epc1 = $epc1;
		$this->epc2 = $epc2;
	}

	/**
	 * Setzt den Threshold-Parameter
	 *
	 * @param array $params muss dan Threshold-Parameter [0;100] in der Form array('threshold' => 50) enthalten
	 * @return boolean
	 */
	public function setParams(Array $params) {
		if ( isset($params['threshold_levenshtein']) ) {
			$this->threshold_levenshtein = $params['threshold_levenshtein'];
			return true;
		} else {
			return false;
		}
	}

	public function map() {
		$this->calculateMatrixValues();
		$this->generateMapping();
	}

	private function calculateMatrixValues() {
		$epc1Functions = $this->epc1->functions;
		$epc2Functions = $this->epc2->functions;

		foreach ( $epc1Functions as $id1 => $label1 ) {
			foreach ( $epc2Functions as $id2 => $label2 ) {
				$levenshteinDistance = levenshtein($label1, $label2);
				$maxlen = max(array(strlen($label1), strlen($label2)));
				if ( $maxlen == 0 ) {
					$levenshteinSimilarity = 0;
				} else {
					$levenshteinSimilarity = round((($maxlen-$levenshteinDistance)/$maxlen)*100, 2);
				}
				$this->matrix[$id1][$id2] = $levenshteinSimilarity >= $this->threshold_levenshtein ? $levenshteinSimilarity : 0;
			}
		}

		return $this->getMatrix();
	}

	/**
	 * Generiert das Mapping
	 *
	 * @return void
	 */
	private function generateMapping() {
		$this->generateFunctionMapping();
		$this->generateConnectorMapping();
	}

	/**
	 * Erzeugt das Mapping der Funktionsknoten
	 */
	private function generateFunctionMapping() {
		foreach ( $this->matrix as $id1 => $arr ) {
			$maxLevenshteinSimilarityHorizontal = Tools::getMaxValueHorizontal($arr);
			foreach ( $arr as $id2 => $value ) {
				// Horizontale
				if ( $value == $maxLevenshteinSimilarityHorizontal
						&& $maxLevenshteinSimilarityHorizontal != 0
						&& $value == Tools::getMaxValueVertical($this->matrix, $id2)
				) {
					array_push($this->mapping, array($id1 => $id2));
				}
			}
		}
	}

	/**
	 * Erzeugt das Mapping der Konnektorknoten
	 * 
	 * @TODO bisher ist ein fester Threshold von 0.3 fuer das Matching eingesetzt
	 *       dass muss noch geaendert werden, sodass immer der beste Wert fuer
	 *       ein Mapping genommen wird!
	 */
	private function generateConnectorMapping() {
		$connectorsOfEPC1 = $this->epc1->getAllConnectors();
		$epc2 = clone $this->epc2;
		//$epc2->assignFunctionMapping($this);
		$connectorsOfEPC2 = $epc2->getAllConnectors();

		foreach ( $connectorsOfEPC1 as $id1 => $type1 ) {
			$predecessors1 = $this->epc1->getPredecessor($id1);
			$successors1 = $this->epc1->getSuccessor($id1);
			foreach ( $connectorsOfEPC2 as $id2 => $type2 ) {
				$predecessors2 = $epc2->getPredecessor($id2);
				$successors2 = $epc2->getSuccessor($id2);

				$maxPred = max(count($predecessors1), count($predecessors2));
				$maxSucc = max(count($successors1), count($successors2));
					
				$predCount = 0;
				foreach ( $predecessors1 as $pid1 ) {
					foreach ( $predecessors2 as $pid2 ) {
						if ( $pid1 == $this->mappingExistsTo($pid2) ) {
							$predCount++;
						}
					}
				}
				if ( $maxPred == 0 ) break;
				$predSim = $predCount / $maxPred;
				
				$succCount = 0;
				foreach ( $successors1 as $sid1 ) {
					foreach ( $successors2 as $sid2 ) {
						if ( $sid1 == $this->mappingExistsTo($sid2) ) {
							$succCount++;
						}
					}
				}
				if ( $maxSucc == 0 ) break;
				$succSim = $succCount / $maxSucc;
				$connectorSim = ($predSim+$succSim) / 2;

				// @TODO
				if ( $connectorSim >= 0.3 ) {
					array_push($this->mapping, array($id1 => $id2));
				}

			}
		}
	}


}
?>