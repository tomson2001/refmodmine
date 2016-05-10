<?php
/**
 * Mappt alle Funktionspaar, die einen Aehnlichkeitswert von 1 haben.
 * Damit sind auch N:M Mappings moeglich
 * 
 * @author Tom Thaler
 */
class AllOneMapper extends AMapper implements IMapper {

	public function generateMapping() {
		$matrix = $this->matrix;
		foreach ( $matrix as $id1 => $arr ) {
			foreach ( $arr as $id2 => $value ) {
				if ( $value == 1 ) array_push($this->mapping, array($id1 => $id2));
				//if ( $value >= 50 ) array_push($this->mapping, array($id1 => $id2));
			}
		}
	}

}
?>