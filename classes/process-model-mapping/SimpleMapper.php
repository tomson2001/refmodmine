<?php
/**
 * Berechnet ein Mapping, indem die hoechsten Werten genommen werden
 */
class SimpleMapper extends AMapper implements IMapper {
	
	public function generateMapping() {	
		foreach ( $this->matrix as $id1 => $arr ) {
			$maxLevenshteinSimilarityHorizontal = Tools::getMaxValueHorizontal($arr);
			foreach ( $arr as $id2 => $value ) {
				// Horizontale
				if ( $value == $maxLevenshteinSimilarityHorizontal
						&& $maxLevenshteinSimilarityHorizontal != 0
						&& $value == Tools::getMaxValueVertical($this->matrix, $id2)
				) {
					array_push($this->mapping, array($id1 => $id2));
				}
			}
		}
	}
	
}
?>