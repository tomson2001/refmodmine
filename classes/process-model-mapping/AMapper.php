<?php
/**
 * Berechnet ein Knotenmapping auf Basis einer 2-dimensionalen Matrix.
 */
abstract class AMapper {
	
	protected $matrix;
	protected $mapping = array();
	
	public function __construct($matrix) {
		$this->matrix = $matrix;
		$this->generateMapping();
	}
	
	public function getMapping() {
		return $this->mapping;
	}
	
}
?>