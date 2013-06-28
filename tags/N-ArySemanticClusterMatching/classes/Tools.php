<?php
class Tools {
	
	public static function getMaxValueHorizontal($matrix) {
		$maxValue = 0;
		foreach ( $matrix as $id2 => $value ) {
			if ( $value > $maxValue ) {
				$maxValue = $value;
			}
		}
		return $maxValue;
	}
	
	public static function getMaxValueVertical($matrix, $nodeID2) {
		$maxValue = 0;
		foreach ( $matrix as $id1 => $arr ) {
			foreach ( $arr as $id2 => $value ) {
				if ( $id2 == $nodeID2 && $value > $maxValue ) {
					$maxValue = $value;
				}
			}
		}
		return $maxValue;
	}
	
	public static function isNodePreciseMappableFromAtoB($matrix, $maxValue) {
		$countMatchingNodes = 0;
		foreach ( $matrix as $id2 => $value ) {
			if ( $value == $maxValue ) {
				$countMatchingNodes++;
			}
		}
		return $countMatchingNodes > 1 ? false : true;
	}
	
	public static function array_value_union() {
		$numArgs = func_num_args();
		$unionArr = array();
		for ( $i=0; $i<$numArgs; $i++ ) {
			$arr = func_get_arg($i);
			if ( !is_array($arr) ) return false;
			foreach ( $arr as $value ) {
				if ( !in_array($value, $unionArr) ) {
					array_push($unionArr, $value);
				}
			}
		}
		return $unionArr;
	}
	
}
?>