<?php
class PercentageOfCommonNodesAndEdges implements ISimilarityMeasure {
	
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
		
		// Berechnung des Nenners: Anzahl aller Funktionen und Kanten
		$numOfAllFuncsAndEdgesInBothEpcs = count($epc1->functions) + 
										   count($epc2->functions) + 
										   count($epc1->edges) + 
										   count($epc2->edges);
		
		// Dieser Fall fuehrt zur Division durch 0
		if ( $numOfAllFuncsAndEdgesInBothEpcs == 0 ) {
			return "NaN";
		}
		
		// Berechnung der Anzahl der Funktionen in EPK1 - der Funktionen, die auch in EPK2 vorkommen 
		$numFuncs1WithoutFuncs2 = count($this->mapping->getAllUnmappedFunctionsOfEPC1());
		
		// Berechnung der Anzahl der Funktionen in EPK2 - der Funktionen, die auch in EPK1 vorkommen
		$numFuncs2WithoutFuncs1 = count($this->mapping->getAllUnmappedFunctionsOfEPC2());
		
		// Berechnung der Anzahl der Kanten in EPK1 - der Kanten, die auch in EPK2 vorkommen
		$numEdges1WithoutEdges2 = count($this->mapping->getAllUnmappedEdgesOfEPC1());
		
		// Berechnung der Anzahl der Kanten in EPK2 - der Kanten, die auch in EPK1 vorkommen
		$numEdges2WithoutEdges1 = count($this->mapping->getAllUnmappedEdgesOfEPC2());
		
		// Berechnung des Zaehlers
		$numator = $numFuncs1WithoutFuncs2 + $numFuncs2WithoutFuncs1 + $numEdges1WithoutEdges2 + $numEdges2WithoutEdges1;
		
		// Endberechnung
		$this->similarityValue = round((1 - ($numator/$numOfAllFuncsAndEdgesInBothEpcs))*100, 2);
		
		return $this->value();
	}
	
	public function value() {
		return $this->similarityValue;
	}
	
}
?>