<?php
class Metrics {
	
	public $epcs = array();
	public $filename;
	public $values = array();
	
	public function __construct() {
		
	}
	
	public function loadCSV($filename) {
		$csv = array();
		if ( ($handle = fopen($filename, "r")) !== FALSE ) {
		    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
		    	$rowname = null;
		    	foreach ( $data as $index => $value ) {
		    		if ( $index == 0 ) {
		    			$rowname = empty($value) ? "header" : $value;
		    			$csv[$rowname] = array();
		    		} else {
		    			$csv[$rowname][$index] = $value;
		    		}
		    	}
		    }
		    fclose($handle);
		}
		
		foreach ( $csv["header"] as $index => $model ) {
			if ( in_array($model, array("sum", "min", "max", "R", "IQR", "MAD", "MEAN", "SD", "CV", "q25", "median", "q75", "mean")) ) continue;
			$this->values[$model] = array();
			
			foreach ( $csv as $metric => $values ) {
				if ( $metric == "header" ) continue;
				$this->values[$model][$metric] = (float) str_replace(",", ".", $values[$index]);
			}
		}
	}
	
	public function addMetricsValue($epcName, $metricName, $value) {
		if ( !isset($this->values[$epcName]) ) $this->values[$epcName] = array();
		$this->values[$epcName][$metricName] = $value;
	}
	
	public function assignEPC(EPC $epc) {
		array_push($this->epcs, $epc);
	}
	
	public function nodes_after_splits() {
		foreach ( $this->epcs as $epc ) {
			$connectors = $epc->getAllConnectors();
			$splits = $epc->getAllSplitConnectors($connectors);
			$nodesAfterSplits = 0;
			foreach ( $splits as $nodeID => $type ) {
				$succs = $epc->getSuccessor($nodeID);
				$nodesAfterSplits += count($succs);
			}
			$this->addMetricsValue($epc->name, "nodes_after_splits", $nodesAfterSplits);
		}
	}
	
	public function gateway_heterogeneity() {
		foreach ( $this->epcs as $epc ) {
			$gatewayHeterogeneity = 0;
			if ( count($epc->or) > 0 ) $gatewayHeterogeneity++;
			if ( count($epc->xor) > 0 ) $gatewayHeterogeneity++;
			if ( count($epc->and) > 0 ) $gatewayHeterogeneity++;
			$this->addMetricsValue($epc->name, "gateway_heterogeneity", $gatewayHeterogeneity);
		}
	}
	
	public function connector_connected_nodes() {
		foreach ( $this->epcs as $epc ) {
			$connectors = $epc->getAllConnectors();
			$connectorConnectedNodes = 0;
			foreach ( $connectors as $nodeID => $type ) {
				$succs = $epc->getSuccessor($nodeID);
				$connectorConnectedNodes += count($succs);
				
				$preds = $epc->getPredecessor($nodeID);
				$connectorConnectedNodes += count($preds);
			}
			$this->addMetricsValue($epc->name, "connector_connected_nodes", $connectorConnectedNodes);
		}
	}
	
	public function ors() {
		foreach ( $this->epcs as $epc ) {
			$this->addMetricsValue($epc->name, "ors", count($epc->or));
		}
	}
	
	public function start_end_events() {
		foreach ( $this->epcs as $epc ) {
			$startEndEvents = 0;
			foreach ( $epc->events as $nodeID => $label ) {
				if ( $epc->isStartNode($nodeID) || $epc->isEndNode($nodeID) ) $startEndEvents++;
			}
			$this->addMetricsValue($epc->name, "start_end_events", $startEndEvents);
		}
	}
	
}
?>
