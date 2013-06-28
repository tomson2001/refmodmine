<?php
interface ISimilarityMeasure {
	
	/**
	 * Fuehrt die Berechnung des Aehnlichkeitswertes durch
	 */
	public function calculate();
	
	/**
	 * Gibt des berechneten Aehnlichkeitswert zurueck
	 * 
	 * @return float
	 */
	public function value();
	
}
?>