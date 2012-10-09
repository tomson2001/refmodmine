<?php
/**
 * Erzeugt ein Mapping ueber die Funktionsknoten zweier EPKs
 * basierend auf der Levenshtein-Distanz der Labels
 */
class LevenshteinMapping extends AMapping implements IMapping {
	
	private $threshold_levenshtein = 0;
	
	public function __construct(EPC $epc1, EPC $epc2) {
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
	
}
?>