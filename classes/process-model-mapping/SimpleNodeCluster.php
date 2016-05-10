<?php
class SimpleNodeCluster {

	public $nodes = array();

	public function __construct() {

	}

	public function addNode($nodeID) {
		if ( !in_array($nodeID, $this->nodes) ) array_push($this->nodes, $nodeID);
	}

	public function contains($nodeID) {
		if ( in_array($nodeID, $this->nodes) ) return true;
		return false;
	}
	
	public function combine(SimpleNodeCluster $cluster) {
		foreach ($cluster->nodes as $nodeID) {
			$this->addNode($nodeID);
		}
		return $this;
	}

}
?>