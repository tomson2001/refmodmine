<?php
/**
 * Implementierung basierend auf Dongen: Measuring Similarity between Business Process Models
 */
class CausalFootprints implements ISimilarityMeasure {

	private $mapping;
	private $similarityValue;

	public function __construct(IMapping $mapping) {
		$this->mapping = $mapping;
	}

	public function calculate() {

		// Transformation der EPKs, sodass nur noch Funktionen und Kanten enhalten sind
		$transformer = new EPCTransformerNoConnectorsNoEvents();

		// ID-Wechsel in EPK2 anhand des Mappings
		$epc2 = $this->mapping->epc2;
		$epc2->assignFunctionMapping($this->mapping);

		$epc1 = $transformer->transform($this->mapping->epc1);
		$epc2 = $transformer->transform($epc2);

		// Transformation der EPK in Causal Footprint Graphen
		$causalityGraph1 = new CausalFootprintGraph($epc1);
		$causalityGraph2 = new CausalFootprintGraph($epc2);
		
		$sigma = Tools::array_value_union(
				$causalityGraph1->functions,
				$causalityGraph1->lookBackLinks,
				$causalityGraph1->lookAheadLinks,
				$causalityGraph2->functions,
				$causalityGraph2->lookBackLinks, 
				$causalityGraph2->lookAheadLinks
		); 
		

		// Berechnung der Causal Footprint Vectoren
		$footprintVector1 = array();
		$footprintVector2 = array();
		$linkSet1 = Tools::array_value_union($causalityGraph1->lookBackLinks, $causalityGraph1->lookAheadLinks);
		$linkSet2 = Tools::array_value_union($causalityGraph2->lookBackLinks, $causalityGraph2->lookAheadLinks);
		foreach ( $sigma as $index => $term ) {
			if ( !is_array($term) ) {
				// Term ist ein einfacher Knoten
				$footprintVector1[$index] = array_key_exists($term, $epc1->functions) ? 1 : 0;
				$footprintVector2[$index] = array_key_exists($term, $epc2->functions) ? 1 : 0;
			} else {
				foreach ($term as $node1 => $arr1) {
					// Term ist ein Link (LookBack oder LookAhead)
					// Wert fuer Vector 1
					$footprintVector1[$index] = 0;
					foreach ( $linkSet1 as $link ) {
						foreach ( $link as $node2 => $arr2 ) {
							if ( $node1 == $node2 && count(array_diff($arr1, $arr2)) == 0 && count(array_diff($arr2, $arr1)) == 0 ) {
								//$footprintVector1[$index] = 1 / pow(2, count($arr1));
								$footprintVector1[$index] =1;
								break;
							}
						}
					}
					// Wert fuer Vector 2
					$footprintVector2[$index] = 0;
					foreach ( $linkSet2 as $link ) {
						foreach ( $link as $node2 => $arr2 ) {
							if ( $node1 == $node2 && count(array_diff($arr1, $arr2)) == 0 && count(array_diff($arr2, $arr1)) == 0 ) {
								//$footprintVector2[$index] = 1 / pow(2, count($arr1));
								$footprintVector2[$index] = 1;
								break;
							}
						}
					}
					break;
				}
			}
		}
		
		// Berechnung der einzelnen Komponenten (Kreuzprodukt, Betrag Vector 1 und 2)
		$numerator = 0;
		$absolutValueOfVector1 = 0;
		$absolutValueOfVector2 = 0;
		foreach ( $sigma as $index => $term ) {
			$numerator += $footprintVector1[$index] * $footprintVector2[$index];
			$absolutValueOfVector1 += pow($footprintVector1[$index], 2);
			$absolutValueOfVector2 += pow($footprintVector2[$index], 2);
		}
		$denominator = sqrt($absolutValueOfVector1) * sqrt($absolutValueOfVector2);
		
		/**
		if ( $epc1->name == "Variante a: v0" && $epc2->name == "Variante e: v4" ) {
			print("\n\nZaehler: ".$numerator."\n");
			print("Betrag Vektor 1: ".$absolutValueOfVector1."\n");
			print("Betrag Vektor 2: ".$absolutValueOfVector2."\n");
			print("LookAheadLinks1: ".count($causalityGraph1->lookAheadLinks)."\n");
			print("LookAheadLinks2: ".count($causalityGraph2->lookAheadLinks)."\n");
			print("LookBackLinks1: ".count($causalityGraph1->lookBackLinks)."\n");
			print("LookBackLinks2: ".count($causalityGraph2->lookBackLinks)."\n");
			print("Nenner: ".$denominator."\n");
			print("Similarity: ".(($numerator / $denominator)*100)."\n\n");
			
			foreach ( $sigma as $index => $term ) {
				print($index." ");
				if ( is_array($term) ) {
					foreach ( $term as $source => $arr ) {
						print($term['type'].": ");
						if ( array_key_exists($source, $epc1->functions) ) {
							print($epc1->functions[$source]);
						} elseif ( array_key_exists($source, $epc2->functions) ) {
							print($epc2->functions[$source]);
						}
						print(" => ");
						foreach ( $arr as $id ) {
							if ( array_key_exists($id, $epc1->functions) ) {
								print($epc1->functions[$id]);
							} elseif ( array_key_exists($id, $epc2->functions) ) {
								print($epc2->functions[$id]);
							}
							print(", ");
						} 
						print(" (".$footprintVector1[$index].", ".$footprintVector2[$index].")\n");
						break;
					}
				} else {
					if ( array_key_exists($term, $epc1->functions) ) {
						print($epc1->functions[$term]);
					} elseif ( array_key_exists($term, $epc2->functions) ) {
						print($epc2->functions[$term]);
					}
					print(" (".$footprintVector1[$index].", ".$footprintVector2[$index].")\n");
				}
			}

			exit();
		}
		*/
		
		
		
		if ( $denominator == 0 ) {
			return "NaN";
		}

		// Endberechnung
		$this->similarityValue = round(($numerator / $denominator)*100, 2);

		return $this->value();
	}

	public function value() {
		return $this->similarityValue;
	}

}
?>