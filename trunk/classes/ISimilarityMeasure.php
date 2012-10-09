<?php
interface ISimilarityMeasure {
	
	public function __construct(IMapping $mapping);
	public function calculate();
	public function value();
	
}
?>