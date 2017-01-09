<?php
/**
 * Erzeugt ein Mapping ueber die Funktionsknoten zweier EPKs,
 * basierend auf der Levenshtein-Distance der Labels und einer 
 * Analyse der Eingangs- und Ausgangsknoten
 * 
 * Hierzu werden EPKs ohne Konnektoren und Ereignisse benoetigt
 */
class LevenshteinWithStructuralMapping extends AMapping implements IMapping {
	
	private $threshold_levenshtein = 50;
	private $threshold_structure = 16;
	
	public function __construct(EPC $epc1, EPC $epc2) {
		$transformer = new EPCTransformerNoConnectorsNoEvents();
		$epc1 = $transformer->transform($epc1);
		$epc2 = $transformer->transform($epc2);
		$this->epc1 = $epc1;
		$this->epc2 = $epc2;
	}
	
	/**
	 * Setzt den Threshold-Parameter
	 * 
	 * @param array $params muss dan Threshold-Parameter [0;100] in der Form array('threshold_levenshtein' => 50, 'threshold_structure' => 50) enthalten
	 * @return boolean
	 */
	public function setParams(Array $params) {
		if ( isset($params['threshold_levenshtein']) ) {
			$this->threshold_levenshtein = $params['threshold_levenshtein'];
		}
		if ( isset($params['threshold_structure']) ) {
			$this->threshold_structure = $params['threshold_structure'];
		}
	}
	
	public function map($algorithm) {
		$this->calculateMatrixValues();
		$this->generateMapping($algorithm);
	}
	
	private function calculateMatrixValues() {
		$epc1Functions = $this->epc1->functions;
		$epc2Functions = $this->epc2->functions;
		
		foreach ( $epc1Functions as $id1 => $label1 ) {
			foreach ( $epc2Functions as $id2 => $label2 ) {
				
				/**
				 * Levenshtein Mapping
				 */
				
				$levenshteinDistance = levenshtein($label1, $label2);
				$maxlen = max(array(strlen($label1), strlen($label2)));
				if ( $maxlen == 0 ) {
					$levenshteinSimilarity = 0;
				} else {
					$levenshteinSimilarity = round((($maxlen-$levenshteinDistance)/$maxlen)*100, 2);
				}
				$levenshteinSimilarity = $levenshteinSimilarity >= $this->threshold_levenshtein ? $levenshteinSimilarity : 0;
				
				/**
				 * Structural Mapping
				 */
				
				// Anzahl Ein- und Ausgehender Kanten 
				$incomingEdgesOfFunction1 = count($this->epc1->getPredecessor($id1));
				$outgoingEdgesOfFunction1 = count($this->epc1->getSuccessor($id1));
				
				$incomingEdgesOfFunction2 = count($this->epc2->getPredecessor($id2));
				$outgoingEdgesOfFunction2 = count($this->epc2->getSuccessor($id2));
				
				// Rollenzurodnung
				$rolesOfFunction1['start'] = $incomingEdgesOfFunction1 == 0 ? 1 : 0;
				$rolesOfFunction2['start'] = $incomingEdgesOfFunction2 == 0 ? 1 : 0;
				
				$rolesOfFunction1['stop'] = $outgoingEdgesOfFunction1 == 0 ? 1 : 0;
				$rolesOfFunction2['stop'] = $outgoingEdgesOfFunction2 == 0 ? 1 : 0;
				
				$rolesOfFunction1['regular'] = $incomingEdgesOfFunction1 == 1 && $outgoingEdgesOfFunction1 == 1 ? 1 : 0;
				$rolesOfFunction2['regular'] = $incomingEdgesOfFunction2 == 1 && $outgoingEdgesOfFunction2 == 1 ? 1 : 0;
				
				$rolesOfFunction1['split'] = $outgoingEdgesOfFunction1 >= 2 ? 1 : 0;
				$rolesOfFunction2['split'] = $outgoingEdgesOfFunction2 >= 2 ? 1 : 0;
				
				$rolesOfFunction1['join'] = $incomingEdgesOfFunction1 >= 2 ? 1 : 0;
				$rolesOfFunction2['join'] = $incomingEdgesOfFunction2 >= 2 ? 1 : 0;
				
				// Berechnung des Structual-Similarity Wertes
				$structuralSimilarity = 0;
				if ( count($this->epc1->functions) == 1 && count($this->epc2->functions) == 1 ) {
					$structuralSimilarity = 1;
				} elseif ( $rolesOfFunction1['start'] == 1 && $rolesOfFunction2['start'] == 1 
						&& $rolesOfFunction1['stop'] == 0 && $rolesOfFunction2['stop'] == 0 ) {
					$structuralSimilarity = round((1 - (abs($outgoingEdgesOfFunction1-$outgoingEdgesOfFunction2)/(2*abs($outgoingEdgesOfFunction1+$outgoingEdgesOfFunction2))))*100, 2); 
				} elseif ( $rolesOfFunction1['start'] == 0 && $rolesOfFunction2['start'] == 0 
						&& $rolesOfFunction1['stop'] == 1 && $rolesOfFunction2['stop'] == 1 ) {
					$structuralSimilarity = round((1 - (abs($incomingEdgesOfFunction1-$incomingEdgesOfFunction2)/(2*abs($incomingEdgesOfFunction1+$incomingEdgesOfFunction2))))*100, 2);
				} else {

					if ( $incomingEdgesOfFunction1 + $incomingEdgesOfFunction2 == 0 
						|| $outgoingEdgesOfFunction1 + $outgoingEdgesOfFunction2 == 0) {
						$structuralSimilarity = "NaN";
					} else {
						$structuralSimilarity = round((1-(abs($outgoingEdgesOfFunction1-$outgoingEdgesOfFunction2)/(2*abs($outgoingEdgesOfFunction1+$outgoingEdgesOfFunction2)))-(abs($incomingEdgesOfFunction1-$incomingEdgesOfFunction2)/(2*abs($incomingEdgesOfFunction1+$incomingEdgesOfFunction2))))*100, 2);
					}
				}
				$structuralSimilarity = $structuralSimilarity >= $this->threshold_structure ? $structuralSimilarity : 0;
				
				$this->matrix[$id1][$id2] = ($levenshteinSimilarity + $structuralSimilarity) / 2;
			}
		}
		
		return $this->getMatrix();
	}
	
}
?>