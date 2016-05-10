<?php
class CloneEPCCluster {
	
	public $epcs = array();
	
	public function add(EPC $newEPC) {
		if ( $this->contains($newEPC) ) return false;
		array_push($this->epcs, $newEPC);
		return true;
	}
	
	public function contains(EPC $newEPC) {
		foreach ( $this->epcs as $epc ) {
			if ( $epc->name == $newEPC->name && $epc->id == $newEPC->id ) return true;
		}
		return false;
	}
	
}
?>