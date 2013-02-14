<?php
class LabelMatchingSimilarity implements ISimilarityMeasure {
	
	private $mapping;
	private $similarityValue;
	
	public function __construct(IMapping $mapping) {
		$this->mapping = $mapping;
	}
	
	public function calculate() {
		$sumCorr = 0;
		foreach ($this->mapping->getMapping() as $pair) {
			foreach ($pair as $id1 => $id2) {
				$label1 = $this->mapping->epc1->getNodeLabel($id1);
				$label2 = $this->mapping->epc2->getNodeLabel($id2);
				$levenshteinDistance = levenshtein($label1, $label2);
				$maxlen = max(array(strlen($label1), strlen($label2)));
				$levenshteinSimilarity = (($maxlen-$levenshteinDistance)/$maxlen)*100;
				$sumCorr += $levenshteinSimilarity;
			}
		}
		$numFunctionsOfEpc1 = count($this->mapping->epc1->functions);
		$numFunctionsOfEpc2 = count($this->mapping->epc2->functions);
		$numOfAllFunctions = $numFunctionsOfEpc1 + $numFunctionsOfEpc2;
		if ( $numOfAllFunctions == 0 ) {
			$this->similarityValue = 0;
		} else {
			$this->similarityValue = round((2*$sumCorr)/($numOfAllFunctions), 2);
		}
		return $this->value();
	}
	
	public function value() {
		return $this->similarityValue;
	}
	
}
?>