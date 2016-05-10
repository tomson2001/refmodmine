<?php
abstract class ANAryMapping {
	
	public $epcs = array();
	public $clusters = array();
	
	public function __construct() {
		
	}
	
	public function addEPC(EPC $epc) {
		if ( !$this->contains($epc) ) {
			array_push($this->epcs, $epc);
			$numEPCs = count($this->epcs);
			return $numEPCs-1;
		}
		return null;
	}
	
	public function contains(EPC $epc) {
		foreach ( $this->epcs as $currentEPC ) {
			if ( $currentEPC->internalID == $epc->internalID ) return true;
		}
		return false;
	}
		
}
?>