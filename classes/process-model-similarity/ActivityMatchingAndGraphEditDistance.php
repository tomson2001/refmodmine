<?php
class ActivityMatchingAndGraphEditDistance implements ISimilarityMeasure {
	
	public static $literatureSource = "La Rosa et al. 2010: Merging Business Process Models";

	private $mapping;
	private $similarityValue;

	public function __construct(IMapping $mapping) {
		$this->mapping = $mapping;
	}

	public function calculate() {

		$epc1 = $this->mapping->epc1;
		$epc2 = $this->mapping->epc2;

		$numOfAllFunctionsAndConnectors = count($epc1->functions) + count($epc1->getAllConnectors()) + count($epc2->functions) + count($epc2->getAllConnectors());
		$numOfAllEdges = count($epc1->edges) + count($epc2->edges);

		$sn = count($this->mapping->getAllUnmappedFunctionsOfEPC1())
			+ count($this->mapping->getAllUnmappedFunctionsOfEPC2())
			+ count($this->mapping->getAllUnmappedConnectorsOfEPC1())
			+ count($this->mapping->getAllUnmappedConnectorsOfEPC2());

		$se = count($this->mapping->getAllUnmappedEdgesOfEPC1())
			+ count($this->mapping->getAllUnmappedEdgesOfEPC2());

		// Diese Faelle fuehren zu Division durch 0 und werden abgefangen
		if ( $numOfAllFunctionsAndConnectors == 0 || $numOfAllEdges == 0 || $numOfAllFunctionsAndConnectors == $sn ) {
			return "NaN";
		}

		$nodeComponent = $sn / $numOfAllFunctionsAndConnectors;
		if ( Config::FIX_POINT_ARITHMETIC ) $nodeComponent = round($nodeComponent, Config::FIX_POINT_ARITHMETIC);
		$edgeComponent = $se / $numOfAllEdges;
		if ( Config::FIX_POINT_ARITHMETIC ) $edgeComponent = round($edgeComponent, Config::FIX_POINT_ARITHMETIC);

		$corrComponent = 0;
		foreach ($this->mapping->getMapping() as $pair) {
			foreach ($pair as $id1 => $id2) {
				if ( !$this->mapping->epc1->isConnector($id1) ) {
					$label1 = $this->mapping->epc1->getNodeLabel($id1);
					$label2 = $this->mapping->epc2->getNodeLabel($id2);
					$levenshteinDistance = levenshtein($label1, $label2);
					$maxlen = max(array(strlen($label1), strlen($label2)));
					$levenshteinSimilarity = (($maxlen-$levenshteinDistance)/$maxlen);
					if ( Config::FIX_POINT_ARITHMETIC ) $levenshteinSimilarity = round($levenshteinSimilarity, Config::FIX_POINT_ARITHMETIC);
					$corrComponent += (1-$levenshteinSimilarity);
				}
			}
		}
		$corrComponent = (2*$corrComponent) / ($numOfAllFunctionsAndConnectors - $sn);
		if ( Config::FIX_POINT_ARITHMETIC ) $corrComponent = round($corrComponent, Config::FIX_POINT_ARITHMETIC);
			
		// Endberechnung
		$this->similarityValue = round((1-(($nodeComponent + $edgeComponent + $corrComponent) / 3))*100, 2);
		if ( Config::FIX_POINT_ARITHMETIC ) $this->similarityValue = round($this->similarityValue, Config::FIX_POINT_ARITHMETIC - 2);
		
		$count = 0;
		
		/**
		if ( ($this->mapping->epc1->name == "K_03" && $this->mapping->epc2->name == "K_03") 
				|| ($this->mapping->epc1->name == "Variante a: v0" && $this->mapping->epc2->name == "Variante a: v0") ) {
			print("\n\n".$this->mapping->epc1->name." <=> ".$this->mapping->epc2->name."\n");
			print("\nnumOfAllFunctionsAndConnectors: ".$numOfAllFunctionsAndConnectors."\n");
			print("numOfAllEdges: ".$numOfAllEdges."\n");
			print("sn: ".$sn."\n");
			print("se: ".$se."\n");
			print("NodeComponent: ".$nodeComponent."\n");
			print("EdgeComponent: ".$edgeComponent."\n");
			print("Corrcomponent: ".$corrComponent."\n");
			print("Similarity: ".$this->similarityValue."\n");
			
			print("\nNot Mapped Edges Of EPC 2:\n");
			$unmappedEdges = $this->mapping->getAllUnmappedEdgesOfEPC2();
			foreach ($unmappedEdges as $edge) {
				foreach ( $edge as $sourceID => $targetID ) {
					print ("\n".$epc1->getNodeLabel($sourceID)." (".$sourceID.") => ".$epc1->getNodeLabel($targetID)." (".$targetID.")");
				}
			}

			//$count++;
			if ( $count == 0 ) {
				exit();
			}
			//exit();
		}
		*/

		return $this->value();
	}

	public function value() {
		return $this->similarityValue;
	}

}
?>