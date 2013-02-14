<?php
/**
 * Ansatz von Sven Clement
 * 
 * Aehnlichkeit basiert auf der Schnittmenge der in den Modellen verwendeten Terme.
 * hier: mit Beruecksichtigung der Anzahl der vorkommenden Terme, wenn also "Rechnung"
 * in einer EPK 100 mal vorkommt wird das entsprechend beruecksichtigt.
 */
class TerminologySimilarityWithFrequencies implements ISimilarityMeasure {

	private $epc1;
	private $epc2;
	private $similarityValue;
	
	private $terms_in_epc1 = array();
	private $terms_in_epc2 = array();
	private $all_terms = array();

	public function __construct(EPC $epc1, EPC $epc2) {
		$this->epc1 = $epc1;
		$this->epc2 = $epc2;
	}

	/**
	 * @see ISimilarityMeasure::calculate()
	 */
	public function calculate() {
		$this->buildTermArrays();
		
		// Anzahl aller in EPK1 und EPK2 enthaltenen Terme
		$numOfAllTerms = array_sum($this->all_terms);
		
		// Berechnung der Frequenzierten Maechtigkeit der Term-Schnittmenge
		$cardinalNumberOfTermIntersection = 0;
		foreach ( $this->terms_in_epc1 as $term1 => $freq1 ) {
			foreach ( $this->terms_in_epc2 as $term2 => $freq2 ) {
				if ( $term1 == $term2 ) {
					$cardinalNumberOfTermIntersection += 2 * min($freq1, $freq2);
				}
			}
		}
		
		$this->similarityValue = round(($cardinalNumberOfTermIntersection/$numOfAllTerms)*100, 2);

		return $this->value();
	}

	/**
	 * @see ISimilarityMeasure::value()
	 */
	public function value() {
		return $this->similarityValue;
	}
	
	/**
	 * Erstellt die Term-Arrays
	 * (Liste von Termen fuer beide EPKs)
	 */
	public function buildTermArrays() {
		// Behandlung der Funktionen in EPC1
		foreach ( $this->epc1->functions as $functionName ) {
			$functionName = strtolower($functionName);
			$terms = explode(" ", $functionName);
			foreach ( $terms as $term ) {
				$this->addTerm(trim($term), $this->terms_in_epc1);
				$this->addTerm(trim($term), $this->all_terms);
			}
		}
		
		// Behandlung der Funktionen in EPC2
		foreach ( $this->epc2->functions as $functionName ) {
			$functionName = strtolower($functionName);
			$terms = explode(" ", $functionName);
			foreach ( $terms as $term ) {
				$this->addTerm(trim($term), $this->terms_in_epc2);
				$this->addTerm(trim($term), $this->all_terms);
			}
		}
	}
	
	/**
	 * Fuegt einem Array einen Term hinzu
	 * 
	 * @param string $term
	 * @param array $targetArray
	 */
	public function addTerm($term, &$targetArray) {
		if ( array_key_exists($term, $targetArray) ) {
			$targetArray[$term]++;
		} else {
			$targetArray[$term] = 1;
		}
	}

}
?>