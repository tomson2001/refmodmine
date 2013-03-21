<?php
class EmpiricValueSeries {
	
	public $measureName;
	private $values = array();
	private $nextIndex = 0;

	/**
	 * Konstruktor
	 * 
	 * @param string $measureName Name des Aehnlichkeitsmasses
	 */
	public function __construct($measureName) {
		$this->measureName = $measureName;
	}
	
	/**
	 * Fuegt der Wertereihe einen Wert hinzu
	 * 
	 * @param number $value
	 */
	public function add($value) {
		array_push($this->values, str_replace(",", ".", $value));
	}
	
	/**
	 * Ermittelt den Mittelwert der Wertereihe
	 * 
	 * @return number
	 */
	public function getAverage() {
		return array_sum($this->values) / $this->getNumOfValues();
	}
	
	public function getNumOfValues() {
		return count($this->values);
	}
	
	public function getValue($index) {
		return $this->values[$index];
	}
	
	public function getNextValue() {
		if ( isset($this->values[$this->nextIndex]) ) {
			//print("/".$this->nextIndex."/");
			$value = $this->values[$this->nextIndex];
			$this->nextIndex++;
			//print($this->measureName." Index++ ");
			return $value;
		} else {
			//print("/false/");
			$this->nextIndex = 0;
			return false;
		}
	}
	
	public function reset() {
		$this->nextIndex = 0;
	}
	
}
?>