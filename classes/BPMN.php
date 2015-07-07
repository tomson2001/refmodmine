<?php
class BPMN {
	
	public $id;
	public $name;
	private $xml;
	
	public $activities = array();
	public $events = array();
	public $arcs = array();
	public $gateways = array();
	
	public $participants = array();
	public $lanes = array();
	
	// Meta-Information
	public $exclusiveGateways = array();
	public $parallelGateways = array();
	public $eventBasedGateways = array();
	
	public $startEvents = array();
	public $endEvents = array();
	public $intermediateCatchEvents = array();
	public $seqFlows = array();
	public $messageFlows = array();
	public $nodeParticipantAssignments = array();
	public $laneParticipantAssignments = array();
	
	private $processParticipantAssignments = array();
	
	
	/**
	 * SimpleXMLElement aus einer PNML-File
	 *
	 * @param SimpleXMLElement $epc
	 */
	public function __construct($xml, $modelName, $format="bpmn") {
		$this->xml = $xml;
		$this->id = md5($modelName);
		$this->name = $modelName;
		if ( $format == "bpmn" ) {
			$this->loadBPMN($xml);
		}
		unset($this->xml);
	}
	
	/**
	 * Loads a petri net from a pnml file
	 *
	 * @param SimpleXML $xml
	 * @param mixed $modelID
	 */
	private function loadBPMN($xml) {
		
		// register namespaces
		foreach ( $xml->getDocNamespaces() as $strPrefix => $strNamespace ) {
			if ( strlen($strPrefix) == 0 ) {
				$strPrefix="bpmn"; //Assign an arbitrary namespace prefix.
			}
			$xml->registerXPathNamespace($strPrefix,$strNamespace);
		}
		
		// load participants
		foreach ($xml->xpath("//bpmn:collaboration/bpmn:participant") as $participant) {
			$this->participants[(string) $participant["id"]] = rtrim(ltrim((string) $participant["name"]));
			$this->processParticipantAssignments[(string) $participant["processRef"]] = (string) $participant["id"];
		}
		
		// load messageFlows
		foreach ($xml->xpath("//bpmn:collaboration/bpmn:messageFlow") as $msgFlow) {
			$this->arcs[(string) $msgFlow["id"]] = array("source" => (string) $msgFlow["sourceRef"], "target" => (string) $msgFlow["targetRef"], "label" => (string) $msgFlow["name"]);
			array_push($this->messageFlows, (string) $msgFlow["id"]);
		}
		
		foreach ($xml->xpath("//bpmn:process") as $process) {
			$pid = (string) $process["id"];
			$participantID = $this->processParticipantAssignments[$pid];
			
			// load lanes
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:laneSet/bpmn:lane") as $lane) {
				$this->lanes[(string) $lane["id"]] = (string) $lane["name"];
				$this->laneParticipantAssignments[(string) $lane["id"]] = $participantID;
			}
			
			// load activities
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:task") as $task) {
				$this->activities[(string) $task["id"]] = (string) $task["name"];
				$this->nodeParticipantAssignments[(string) $task["id"]] = $participantID;
				// activityLaneAssignment unknown
			}
			
			// load exclusive gateways
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:exclusiveGateway") as $exclusiveGateway) {
				$this->gateways[(string) $exclusiveGateway["id"]] = (string) $exclusiveGateway["name"];
				$this->nodeParticipantAssignments[(string) $exclusiveGateway["id"]] = $participantID;
				array_push($this->exclusiveGateways, (string) $exclusiveGateway["id"]);
			}
			
			// load parallel gateways
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:parallelGateway") as $parallelGateway) {
				$this->gateways[(string) $parallelGateway["id"]] = (string) $parallelGateway["name"];
				$this->nodeParticipantAssignments[(string) $parallelGateway["id"]] = $participantID;
				array_push($this->parallelGateways, (string) $parallelGateway["id"]);
			}
			
			// load event based gateways
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:eventBasedGateway") as $eventBasedGateway) {
				$this->gateways[(string) $eventBasedGateway["id"]] = (string) $eventBasedGateway["name"];
				$this->nodeParticipantAssignments[(string) $eventBasedGateway["id"]] = $participantID;
				array_push($this->eventBasedGateways, (string) $eventBasedGateway["id"]);
			}
			
			// load intermediate catch event
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:intermediateCatchEvent") as $intermediateCatchEvent) {
				$this->events[(string) $intermediateCatchEvent["id"]] = (string) $intermediateCatchEvent["name"];
				$this->nodeParticipantAssignments[(string) $intermediateCatchEvent["id"]] = $participantID;
				array_push($this->intermediateCatchEvents, (string) $intermediateCatchEvent["id"]);
			}
			
			// load start event
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:startEvent") as $startEvent) {
				$this->events[(string) $startEvent["id"]] = (string) $startEvent["name"];
				$this->nodeParticipantAssignments[(string) $startEvent["id"]] = $participantID;
				array_push($this->startEvents, (string) $startEvent["id"]);
			}
			
			// load end event
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:endEvent") as $endEvent) {
				$this->events[(string) $endEvent["id"]] = (string) $endEvent["name"];
				$this->nodeParticipantAssignments[(string) $endEvent["id"]] = $participantID;
				array_push($this->endEvents, (string) $endEvent["id"]);
			}
			
			// load sequece flows
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:sequenceFlow") as $seqFlow) {
				$this->arcs[(string) $seqFlow["id"]] = array("source" => (string) $seqFlow["sourceRef"], "target" => (string) $seqFlow["targetRef"], "label" => (string) $seqFlow["name"]);
				array_push($this->seqFlows, $msgFlow["id"]);
			}
						
		}

	}
	
	public function transform2EPC() {
		$epc = new EPC(null, $this->id, $this->name, null);
		$epc->functions = $this->activities;
		
		// events
		foreach ( $this->events as $id => $label ) {
			if ( empty($label) ) {
				if ( in_array($id, $this->startEvents) ) $label = "start";
				if ( in_array($id, $this->endEvents) ) $label = "end";
				if ( in_array($id, $this->intermediateCatchEvents) ) $label = "message received";
				if ( empty($label) ) $label = "unknown event";
			}
			$epc->events[$id] = $label;
		}
		
		// xor
		foreach ( $this->exclusiveGateways as $id ) {
			$epc->xor[$id] = "xor";
		}
		
		// and
		foreach ( $this->parallelGateways as $id ) {
			$epc->and[$id] = "and";
		}
		
		// or
		foreach ( $this->eventBasedGateways as $id ) {
			$epc->or[$id] = "or";
		}
		
		// edges
		foreach ( $this->arcs as $id => $arc ) {
			$epc->edges[$id] = array($arc["source"] => $arc["target"]);
		}
		
		// organizational units
		foreach ( $this->participants as $id => $label ) {
			$epc->orgUnits[$id] = $label;
		}
		
		// function to organizational unit assignments
		foreach ( $this->activities as $activityID => $activityLabel ) {
			$epc->functionOrgUnitAssignments[$activityID] = $this->nodeParticipantAssignments[$activityID];
		}
		
		/**
		 * Handling message flows
		 * 
		 * 
		 */
		foreach ( $this->messageFlows as $messageFlowID ) {
			$pred = $this->arcs[$messageFlowID]["source"];
			$succ = $this->arcs[$messageFlowID]["target"];
			
			// add and connector after the message flows source
			$predSuccs = $this->getSuccessors($pred);
			$numPredSuccs = count($predSuccs);
			if ( $numPredSuccs > 1 ) {
				$connectorID = md5("and_split_".$pred."_".$messageFlowID);
				$epc->and[$connectorID] = "and";
				// add edge from predecessor to new connector
				array_push($epc->edges, array($pred => $connectorID));
				// add edges from new connectors to all successors and remove the old edges
				foreach ( $predSuccs as $predSucc ) {
					array_push($epc->edges, array($connectorID => $predSucc));
					$epc->deleteEdge($pred, $predSucc);
				}
			}
			
			// add and connector prior to the message flows target
			$succPreds = $epc->getPredecessor($succ);
			$numSuccPreds = count($succPreds);
			if ( $numSuccPreds > 1 ) {
				$connectorID = md5("and_join_".$succ."_".$messageFlowID);
				$epc->and[$connectorID] = "and";
				// add edge from the new connector to the target
				array_push($epc->edges, array($connectorID => $succ));
				// add edges from the targets sources to the new connector
				foreach ( $succPreds as $succPred ) {
					array_push($epc->edges, array($succPred => $connectorID));
					$epc->deleteEdge($succPred, $succ);
				}
			}
		}		
		
		return $epc;
	}
	
	public function getSuccessors($nodeID) {
		$successors = array();
		foreach ( $this->arcs as $id => $arc ) {
			if ( $arc["source"] == $nodeID ) array_push($successors, $arc["target"]);
		}
		return $successors;
	}
	
	public function getPredecessors($nodeID) {
		$predecessors = array();
		foreach ( $this->arcs as $id => $arc ) {
			if ( $arc["target"] == $nodeID ) array_push($predecessors, $arc["source"]);
		}
		return $predecessors;
	}
	
}
?>