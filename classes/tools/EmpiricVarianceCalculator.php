<?php

class EmpiricVarianceCalculator {
	
	private $empiricValueSeries1;
	private $empiricValueSeries2;
	
	public function __construct(EmpiricValueSeries $empiricValueSeries1, EmpiricValueSeries $empiricValueSeries2) {
		$this->empiricValueSeries1 = clone $empiricValueSeries1;
		$this->empiricValueSeries2 = clone $empiricValueSeries2;
	}
	
	/**
	 * Ermittelt die Stichprobenvarianz
	 * 
	 * Quelle:
	 * http://de.wikipedia.org/wiki/Stichprobenvarianz
	 */
	public function getVariance() {
		if ( $this->checkEmpiricValueSeries() ) {
			return $this->calculateVariance();
		} else {
			return "Messreihen inkompatibel";
		}
	}
	
	/**
	 * Fuehrt die Berechnung durch
	 */
	private function calculateVariance() {
		
		$numerator = 0;
		
		$value1 = $this->empiricValueSeries1->getNextValue();
		$value2 = $this->empiricValueSeries2->getNextValue();
		
		// Mittwelwert berechnen
		$average = 0;
		while ( $value1 !== false && $value2 !== false ) {
			$average += abs(($value2/100)-($value1/100));
			
			$value1 = $this->empiricValueSeries1->getNextValue();
			$value2 = $this->empiricValueSeries2->getNextValue();
		}
		$average = $average / $this->empiricValueSeries1->getNumOfValues();
		
		$this->empiricValueSeries1->reset();
		$this->empiricValueSeries2->reset();
		
		$value1 = $this->empiricValueSeries1->getNextValue();
		$value2 = $this->empiricValueSeries2->getNextValue();
		
		// Summenteil berechnen
		$numerator = 0;
		while ( $value1 !== false && $value2 !== false ) {
			$x = abs(($value2/100)-($value1/100));
			$numerator += pow($x - $average, 2);
				
			$value1 = $this->empiricValueSeries1->getNextValue();
			$value2 = $this->empiricValueSeries2->getNextValue();
		}
		
		$denominator = $this->empiricValueSeries1->getNumOfValues();
		if ( $denominator == 0 ) {
			return "not defined";
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