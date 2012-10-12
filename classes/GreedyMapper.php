<?php
class GreedyMapper extends AMapper implements IMapper {
	
	public function generateMapping() {
		$matrix = $this->matrix;
		while ( !empty($matrix) ) {
			$matrix = $this->assignBestMatrixValue($matrix);
		}
	}
	
	/**
	 * Sucht in der Funktions-Matrix den besten (hoechsten) Wert.
	 * Die Funktionskombination wird dem Mapping hinzugefuegt und die
	 * Zeile und Spalte aus der Matrix entfernt.
	 *
	 * @param 2-dimensionales Array $matrix
	 */
	private function assignBestMatrixValue($matrix) {
		$bestValue = 0;
		$node1;
		$node2;
	
		// Besten Wert suchen
		foreach ( $matrix as $id1 => $arr ) {
			foreach ( $arr as $id2 => $value ) {
				if ( $value > $bestValue ) {
					$bestValue = $value;
					$node1 = $id1;
					$node2 = $id2;
				}
			}
		}
	
		if ( $bestValue > 0 ) {
			// Mapping hinzuguefen
			array_push($this->mapping, array($node1 => $node2));
	
			// Zeile und Spalte aus der Matrix entfernen
			unset($matrix[$node1]);
			foreach ( $matrix as $id1 => $arr ) {
				unset($matrix[$id1][$node2]);
			}
	
			return $matrix;
	
		} else {
			// Kein Mapping gefunden
			return array();
		}
	}
	
}
?>