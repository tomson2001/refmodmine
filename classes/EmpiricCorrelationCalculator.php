<?php

class EmpiricCorrelationCalculator {
	
	private $empiricValueSeries1;
	private $empiricValueSeries2;
	
	public function __construct(EmpiricValueSeries $empiricValueSeries1, EmpiricValueSeries $empiricValueSeries2) {
		$this->empiricValueSeries1 = clone $empiricValueSeries1;
		$this->empiricValueSeries2 = clone $empiricValueSeries2;
	}
	
	/**
	 * Ermittelt den empirischen Korrelationskoeffizienten
	 * 
	 * Quelle:
	 * http://de.wikipedia.org/wiki/Korrelationskoeffizient#Empirischer_Korrelationskoeffizient
	 */
	public function getCorrelation() {
		if ( $this->checkEmpiricValueSeries() ) {
			return $this->calculateCorrelation();
		} else {
			return "Messreihen inkompatibel";
		}
	}
	
	/**
	 * Fuehrt die Berechnung durch
	 */
	private function calculateCorrelation() {
		
		$average1 = $this->empiricValueSeries1->getAverage();
		$average2 = $this->empiricValueSeries2->getAverage();
		
		$numerator = 0;
		$denominator1 = 0;
		$denominator2 = 0;
		
		$value1 = $this->empiricValueSeries1->getNextValue();
		$value2 = $this->empiricValueSeries2->getNextValue();
		
		while ( $value1 !== false && $value2 !== false ) {
			$numerator += ($value1 - $average1) * ($value2 - $average2);
			$denominator1 += pow(($value1 - $average1), 2);
			$denominator2 += pow(($value2 - $average2), 2);
			$value1 = $this->empiricValueSeries1->getNextValue();
			$value2 = $this->empiricValueSeries2->getNextValue();
		}
		
		$denominator = sqrt($denominator1 * $denominator2);
		if ( $numerator == $denominator ) {
			return 1;
		} else {		
			return $numerator / $denominator;
		}
	}
	
	/**
	 * Prueft, ob die beiden Messreihen dieselbe Anzahl an Werten haben.
	 * 
	 * @return boolean
	 */
	private function checkEmpiricValueSeries() {
		return $this->empiricValueSeries1->getNumOfValues() == $this->empiricValueSeries2->getNumOfValues();
	}
	
}

?>