<?php
/**
 * Erzeugt ein Mapping ueber die Funktionsknoten zweier EPKs
 * basierend auf der Levenshtein-Distanz der Labels
 */
class BinaryMapping extends AMapping implements IMapping {

	private $clusters = null;

	public function __construct(EPC $epc1, EPC $epc2) {
		$this->epc1 = $epc1;
		$this->epc2 = $epc2;
		//print ("\n\nErstelle Binaer-Mapping zwischen ".$epc1->name." und ".$epc2->name."...\n");
	}

	/**
	 * Setzt den Threshold-Parameter
	 *
	 * @param array $params muss dan Threshold-Parameter [0;100] in der Form array('threshold' => 50) enthalten
	 * @return boolean
	 */
	public function setParams(Array $params) {
		if ( isset($params['clusters']) ) {
			$this->clusters = $params['clusters'];
			return true;
		} else {
			return false;
		}
	}

	public function map($algorithm) {
		$this->calculateMatrixValues();
		$this->generateMapping($algorithm);
	}

	protected function calculateMatrixValues() {
		if ( is_null($this->clusters) ) exit("Fehler bei Erstellung des BinaryMapping: Es wurden keine Cluster uebergeben");

		// Alle Matrixwerte auf 0 setzen
		$epc1Functions = $this->epc1->functions;
		$epc2Functions = $this->epc2->functions;
		foreach ( $epc1Functions as $id1 => $label1 ) {
			foreach ( $epc2Functions as $id2 => $label2 ) {
				$this->matrix[$id1][$id2] = 0;
			}
		}

		// Cluster durchpruefen und entsprechende Matrixwerte auf 1 setzen
		foreach ( $this->clusters as $index => $cluster ) {
			$toMap = $cluster->checkForNodesOfBothEPCs($this->epc1, $this->epc2);
			if ( !empty($toMap) ) {
				foreach ( $toMap[0] as $id1 ) {
					foreach ( $toMap[1] as $id2 ) {
						$this->matrix[$id1][$id2] = 1;
					}
				}
			}
		}

		return $this->getMatrix();
	}

}
?>