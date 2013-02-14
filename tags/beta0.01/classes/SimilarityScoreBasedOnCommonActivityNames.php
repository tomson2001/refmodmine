<?php
class SimilarityScoreBasedOnCommonActivityNames implements ISimilarityMeasure {
	
	protected $mapping;
	protected $similarityValue;
	
	public function __construct(IMapping $mapping) {
		$this->mapping = $mapping;
	}
	
	public function calculate() {
		$numFunctionsOfEpc1 = count($this->mapping->epc1->functions);
		$numFunctionsOfEpc2 = count($this->mapping->epc2->functions);
		$numOfAllFunctions = $numFunctionsOfEpc1+$numFunctionsOfEpc2;
		if ( $numOfAllFunctions == 0 ) {
			$this->similarityValue = 0;
		} else {
			$this->similarityValue = round((2*(count($this->mapping->getMapping()))/($numOfAllFunctions))*100, 2);
		}
		return $this->value();
	}
	
	public function value() {
		return $this->similarityValue;
	}
	
}
?>