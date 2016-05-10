<?php
class Match {
	
	public $nodeIDsOfModel1 = array();
	public $nodeIDsOfModel2 = array();
	
	public function __construct() {
		
	}
	
	/**
	 * @param int $modelNumber 1 oder 2
	 */
	public function contains($modelNumber, $nodeID) {
		switch ($modelNumber) {
			case 1: return in_array($nodeID, $this->nodeIDsOfModel1);
			case 2: return in_array($nodeID, $this->nodeIDsOfModel2);
		}
	}
	
	public function add($modelNumber, $nodeID) {
		if ( $this->contains($modelNumber, $nodeID) ) return false;
		switch ($modelNumber) {
			case 1: array_push($this->nodeIDsOfModel1, $nodeID); return true;
			case 2: array_push($this->nodeIDsOfModel2, $nodeID); return true;
			default: return false;
		}
	}
	
	public function merge(Match $match) {
		foreach ( $match->nodeIDsOfModel1 as $nodeID ) {
			$this->add(1, $nodeID);
		}
		foreach ( $match->nodeIDsOfModel2 as $nodeID ) {
			$this->add(2, $nodeID);
		}
		return true;
	}
	
	public function isComplex() {
		$numOfModel1Nodes = count($this->nodeIDsOfModel1);
		$numOfModel2Nodes = count($this->nodeIDsOfModel2);
		return ( $numOfModel1Nodes > 1 || $numOfModel2Nodes > 1 );
	}
	
	public function toString(EPC &$epc1, EPC &$epc2) {
		$string = "\n  Nodes in Model 1:\n";
		foreach ( $this->nodeIDsOfModel1 as $nodeID ) {
			$nodeType = $epc1->getType($nodeID);
			$label = "";
			switch ( $nodeType ) {
				case "function": $label = $epc1->functions[$nodeID]; break;
				case "event": 	 $label = $epc1->events[$nodeID]; break;
				case "xor":      $label = "xor"; break;
				case "or":		 $label = "or"; break;
				case "and":		 $label = "and"; break;
			}
			$string .= "    ".$label." (".$nodeID.")\n";
		}
		$string .= "  Nodes in Model 2:\n";
		foreach ( $this->nodeIDsOfModel2 as $nodeID ) {
			$nodeType = $epc2->getType($nodeID);
			$label = "";
			switch ( $nodeType ) {
				case "function": $label = $epc2->functions[$nodeID]; break;
				case "event": 	 $label = $epc2->events[$nodeID]; break;
				case "xor":      $label = "xor"; break;
				case "or":		 $label = "or"; break;
				case "and":		 $label = "and"; break;
			}
			$string .= "    ".$label." (".$nodeID.")\n";
		}
		return $string;
	}
	
}
?>