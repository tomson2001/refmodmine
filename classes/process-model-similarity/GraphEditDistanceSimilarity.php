<?php
class GraphEditDistanceSimilarity implements ISimilarityMeasure {
	
	public static $literatureSource = "Dijkman et al. 2009: Graph Matching Algorithms for Business Process Model Similarity Search";
	
	private $mapping;
	private $similarityValue;
	
	public function __construct(IMapping $mapping) {
		$this->mapping = $mapping;
	}
	
	public function calculate() {
		
		// Transformation der EPKs
		$transformer = new EPCTransformerNoConnectorsNoEvents();
		$epc1 = $transformer->transform($this->mapping->epc1);
		$epc2 = $transformer->transform($this->mapping->epc2);
		
		$numOfAllFunctions = count($epc1->functions) + count($epc2->functions);
		$numOfAllEdges = count($epc1->edges) + count($epc2->edges);
		
		$sn = count($this->mapping->getAllUnmappedFunctionsOfEPC1()) + count($this->mapping->getAllUnmappedFunctionsOfEPC2());
		$se = count($this->mapping->getAllUnmappedEdgesOfEPC1()) + count($this->mapping->getAllUnmappedEdgesOfEPC2());
		
		// Diese Faelle fuehren zu Division durch 0 und werden abgefangen
		if ( $numOfAllFunctions == 0 || $numOfAllEdges == 0 || $numOfAllFunctions == $sn ) {
			return "NaN";
		}
		
		$nodeComponent = $sn / $numOfAllFunctions;
		$edgeComponent = $se / $numOfAllEdges;
		
		$corrComponent = 0;
		foreach ($this->mapping->getMapping() as $pair) {
			foreach ($pair as $id1 => $id2) {
				$label1 = $this->mapping->epc1->getNodeLabel($id1);
				$label2 = $this->mapping->epc2->getNodeLabel($id2);
				$levenshteinDistance = levenshtein($label1, $label2);
				$maxlen = max(array(strlen($label1), strlen($label2)));
				$levenshteinSimilarity = (($maxlen-$levenshteinDistance)/$maxlen);
				$corrComponent += (1-$levenshteinSimilarity);
			}
		}
		$corrComponent = (2*$corrComponent) / ($numOfAllFunctions - $sn);
		
		// Endberechnung
		$this->similarityValue = round((1-(($nodeComponent + $edgeComponent + $corrComponent) / 3))*100, 2);
		
		return $this->value();
	}
	
	public function value() {
		return $this->similarityValue;
	}
	
}
?>