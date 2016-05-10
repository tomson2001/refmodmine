<?php
/**
 * Erzeugt ein Mapping ueber die Funktionsknoten zweier EPKs
 * basierend auf der Gleichheit der Wortstaemme der in den 
 * Labels enthaltenen Woerter
 */
class OntologyWithWordstemMapping extends AMapping implements IMapping {
	
	private $threshold_ontology_quote = 0;
	
	private $ontology_words_ignore_list = array(
		"to", "and", "or", "of", "for", "by", "is", "if", "then", "else", "is", "are", 
		"the", "with", "on", "has", "have", "about", "above"
	);
	
	public function __construct(EPC $epc1, EPC $epc2) {
		$this->epc1 = $epc1;
		$this->epc2 = $epc2;
	}
	
	/**
	 * Setzt den Threshold-Parameter
	 * 
	 * @param array $params muss dan Threshold-Parameter [0;100] in der Form array('threshold' => 50) enthalten
	 * @return boolean
	 */
	public function setParams(Array $params) {
		$paramSet = false;
		if ( isset($params['threshold_ontology_quote']) ) {
			$this->threshold_ontology_quote = $params['threshold_ontology_quote'];
			$paramSet = true;
		}
		
		return $paramSet;
	}
	
	public function map($algorithm) {
		$this->calculateMatrixValues();
		$this->generateMapping($algorithm);
	}
	
	protected function calculateMatrixValues() {
		$epc1Functions = $this->epc1->functions;
		$epc2Functions = $this->epc2->functions;
		
		foreach ( $epc1Functions as $id1 => $label1 ) {
			foreach ( $epc2Functions as $id2 => $label2 ) {
				
				// 1. Operation: strtolower
				$label1 = strtolower($label1);
				$label2 = strtolower($label2);
				
				// 2. Split der Labels anhand der Leerzeichen
				$label1_components = explode(" ", $label1);
				$label2_components = explode(" ", $label2);
				
				// 3. Zahlen rausnehmen
				foreach ( $label1_components as $index => $component ) {
					if ( preg_match("/^[0-9]*$/", $component) || empty($component) ) unset($label1_components[$index]);
				}			
				foreach ( $label2_components as $index => $component ) {
					if ( preg_match("/^[0-9]*$/", $component) || empty($component) ) unset($label2_components[$index]);
				}
				
				// 4. Ignores rausnehmen
				foreach ( $label1_components as $index => $component ) {
					if ( in_array($component, $this->ontology_words_ignore_list) ) unset($label1_components[$index]);
				}
				foreach ( $label2_components as $index => $component ) {
					if ( in_array($component, $this->ontology_words_ignore_list) ) unset($label2_components[$index]);
				}
				
				// 5. Wortstaemme ermitteln
				foreach ( $label1_components as $index => $component ) {
					$label1_components[$index] = PorterStemmer::Stem($component);
					//print($component." -> ".$label1_components[$index]."\n");
				}
				foreach ( $label2_components as $index => $component ) {
					$label2_components[$index] = PorterStemmer::Stem($component);
					//print($component." -> ".$label2_components[$index]."\n");
				}
				
				// 5. Mapping der einzelnen Komponenten ueber Levenshtein (Anzahl der Mappings des Labels mit der geringen Anzahl an Komponenten mit dem anderen genuegt)
				$countComponentsOfLabel1 = count($label1_components);
				$countComponentsOfLabel2 = count($label2_components);
				if ( $countComponentsOfLabel1 > $countComponentsOfLabel2 ) {
					// Label1 muss immer dasjenigen mit der geringeren Anzahl an Komponenten (Woertern) sein
					$label_components_tmp = $label1_components;
					$label1_components = $label2_components;
					$label2_components = $label_components_tmp;
				}
				$countComponentMappings = 0;
				foreach ( $label1_components as $component1 ) {
					foreach ( $label2_components as $component2 ) {
						if ( $component1 == $component2 ) {
							$countComponentMappings++;
							break;
						}
					}
				}
				
				// 6. Entscheidung, ob Labels gematcht werden, oder nicht
				$ontologySimilarity = round(($countComponentMappings / max(array($countComponentsOfLabel1, $countComponentsOfLabel2)))*100, 2);
				if ( $ontologySimilarity >= $this->threshold_ontology_quote )
				
				
				$this->matrix[$id1][$id2] = $ontologySimilarity >= $this->threshold_ontology_quote ? 1 : 0;
			}
		}
		
		return $this->getMatrix();
	}
	
}
?>