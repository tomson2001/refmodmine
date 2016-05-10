<?php
class BPMN {
	
	public $id;
	public $name;
	public $reservedIDs = array();
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
	public $genericEvents = array();
	public $intermediateCatchEvents = array();
	public $seqFlows = array();
	public $messageFlows = array();
	public $nodeParticipantAssignments = array();
	public $laneParticipantAssignments = array();
	public $nodeLaneAssignments = array();
	
	public $processParticipantAssignments = array();
	private $currentID = 0;
	
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
			array_push($this->reservedIDs, (string) $participant["id"]);
		}
		
		// load messageFlows
		foreach ($xml->xpath("//bpmn:collaboration/bpmn:messageFlow") as $msgFlow) {
			$this->arcs[(string) $msgFlow["id"]] = array("source" => (string) $msgFlow["sourceRef"], "target" => (string) $msgFlow["targetRef"], "label" => (string) $msgFlow["name"]);
			array_push($this->messageFlows, (string) $msgFlow["id"]);
			array_push($this->reservedIDs, (string) $msgFlow["id"]);
		}
		
		foreach ($xml->xpath("//bpmn:process") as $process) {
			$pid = (string) $process["id"];
			array_push($this->reservedIDs, (string) $process["id"]);
			$participantID = $this->processParticipantAssignments[$pid];
			
			// load lanes
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:laneSet/bpmn:lane") as $lane) {
				$laneName = $this->participants[$this->processParticipantAssignments[$pid]];
				if ( !empty((string) $lane["name"]) ) $laneName = (string) $lane["name"];
				$this->lanes[(string) $lane["id"]] = $laneName;
				$this->laneParticipantAssignments[(string) $lane["id"]] = $participantID;
				array_push($this->reservedIDs, (string) $lane["id"]);
				
				// load lane assignments
				foreach ( $lane->flowNodeRef as $flowNodeRef ) {
					$this->nodeLaneAssignments[(string) $flowNodeRef] = (string) $lane["id"];
				}
			}
			
			// load activities
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:task") as $task) {
				$this->activities[(string) $task["id"]] = (string) $task["name"];
				$this->nodeParticipantAssignments[(string) $task["id"]] = $participantID;
				array_push($this->reservedIDs, (string) $task["id"]);
				// activityLaneAssignment unknown
			}
			
			// load exclusive gateways
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:exclusiveGateway") as $exclusiveGateway) {
				$this->gateways[(string) $exclusiveGateway["id"]] = (string) $exclusiveGateway["name"];
				$this->nodeParticipantAssignments[(string) $exclusiveGateway["id"]] = $participantID;
				array_push($this->exclusiveGateways, (string) $exclusiveGateway["id"]);
				array_push($this->reservedIDs, (string) $exclusiveGateway["id"]);
			}
			
			// load parallel gateways
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:parallelGateway") as $parallelGateway) {
				$this->gateways[(string) $parallelGateway["id"]] = (string) $parallelGateway["name"];
				$this->nodeParticipantAssignments[(string) $parallelGateway["id"]] = $participantID;
				array_push($this->parallelGateways, (string) $parallelGateway["id"]);
				array_push($this->reservedIDs, (string) $parallelGateway["id"]);
			}
			
			// load event based gateways
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:eventBasedGateway") as $eventBasedGateway) {
				$this->gateways[(string) $eventBasedGateway["id"]] = (string) $eventBasedGateway["name"];
				$this->nodeParticipantAssignments[(string) $eventBasedGateway["id"]] = $participantID;
				array_push($this->eventBasedGateways, (string) $eventBasedGateway["id"]);
				array_push($this->reservedIDs, (string) $eventBasedGateway["id"]);
			}
			
			// load intermediate catch event
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:intermediateCatchEvent") as $intermediateCatchEvent) {
				$this->events[(string) $intermediateCatchEvent["id"]] = (string) $intermediateCatchEvent["name"];
				$this->nodeParticipantAssignments[(string) $intermediateCatchEvent["id"]] = $participantID;
				array_push($this->intermediateCatchEvents, (string) $intermediateCatchEvent["id"]);
				array_push($this->reservedIDs, (string) $intermediateCatchEvent["id"]);
			}
			
			// load start event
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:startEvent") as $startEvent) {
				$this->events[(string) $startEvent["id"]] = (string) $startEvent["name"];
				$this->nodeParticipantAssignments[(string) $startEvent["id"]] = $participantID;
				array_push($this->startEvents, (string) $startEvent["id"]);
				array_push($this->reservedIDs, (string) $startEvent["id"]);
			}
			
			// load end event
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:endEvent") as $endEvent) {
				$this->events[(string) $endEvent["id"]] = (string) $endEvent["name"];
				$this->nodeParticipantAssignments[(string) $endEvent["id"]] = $participantID;
				array_push($this->endEvents, (string) $endEvent["id"]);
				array_push($this->reservedIDs, (string) $endEvent["id"]);
			}
			
			// load generic event
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:event") as $event) {
				$this->events[(string) $event["id"]] = (string) $event["name"];
				$this->nodeParticipantAssignments[(string) $event["id"]] = $participantID;
				array_push($this->genericEvents, (string) $event["id"]);
				array_push($this->reservedIDs, (string) $event["id"]);
			}
			
			// load sequece flows
			foreach ($xml->xpath("//bpmn:process[@id='".$pid."']/bpmn:sequenceFlow") as $seqFlow) {
				$this->arcs[(string) $seqFlow["id"]] = array("source" => (string) $seqFlow["sourceRef"], "target" => (string) $seqFlow["targetRef"], "label" => (string) $seqFlow["name"]);
				array_push($this->seqFlows, $msgFlow["id"]);
				array_push($this->reservedIDs, (string) $seqFlow["id"]);
			}
						
		}

	}
	
	public function getNextID() {
		$this->currentID++;
		if ( !in_array($this->currentID, $this->reservedIDs) ) {
			array_push($this->reservedIDs, $this->currentID);
			return $this->currentID;
		}
		return $this->getNextID();
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
		foreach ( $this->lanes as $id => $label ) {
			$epc->orgUnits[$id] = $label;
		}
		
		// function to organizational unit assignments
		foreach ( $this->activities as $activityID => $activityLabel ) {
			$epc->functionOrgUnitAssignments[$activityID] = $this->nodeLaneAssignments[$activityID];
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
	
	public function handleCatchEventsAsReceiveFunktions() {
		
		foreach ( $this->intermediateCatchEvents as $id ) {
			$label = $this->events[$id];
			
			$predID = null;
			foreach ( $this->messageFlows as $flowID ) {
				if ( $this->arcs[$flowID]["target"] == $id ) {
					$predID = $this->arcs[$flowID]["source"];
					break;
				}
			}
			
			if ( !is_null($predID) ) {
				if ( isset($this->activities[$predID]) ) {
					$activityLabel = $this->activities[$predID];
					$object = NLP::getLabelObject($activityLabel);
					if ( is_null($object) || empty($object) ) continue;
					$newLabel = "receive ".$object;
					//print( "Old Label: ".$label." | New Label: ".$newLabel."\n");
				
					unset($this->events[$id]);
					foreach ( $this->intermediateCatchEvents as $index => $eventID ) {
						if ( $eventID == $id ) {
							unset($this->intermediateCatchEvents[$index]);
							break;
						}
					}
					$this->activities[$id] = $newLabel;
					print( " Message receive event: ".$label." changed to activity: ".$newLabel."\n");
				}
			}
		}
		
	}
	

	public function improveFunctionsWhichAreCatchEventsLabels() {
		$debug = true;
		foreach ( $this->activities as $id => $label ) {
			$hasIncomingMessageFlow = $this->hasIncomingMessageFlow($id);
			if ( $hasIncomingMessageFlow !== false ) {
				$taggedLabel = NLP::tag($label);
				$taggedLabel = NLP::extendTaggedTextWithHighLevelTags($taggedLabel);
				$labelStyle = NLP::getLabelStyle($taggedLabel);
				if ( $debug ) print(" Activity \"".$label."\" is being checked ... ");
				if ( is_null($labelStyle) ) {
					if ( NLP::hasVerbs($taggedLabel) ) {
						if ( $debug ) print(" label style could not be detected\n");
					} else {
						$this->activities[$id] = "receive ".$label;
						if ( $debug ) print(" renamed to \"".$this->activities[$id]."\"\n");
					}
					continue;
				}
				$verb = NLP::getLabelVerb($label);
				if ( is_null($verb) || empty($verb) ) {
					$this->activities[$id] = "receive ".$label;
					if ( $debug ) print(" renamed to \"".$this->activities[$id]."\"\n");
					continue;
				} else {
					$doesVerbCorrespondsToReceive = WordNet::checkIfVerbsAreSynonym($verb, "receive");
					if ( $doesVerbCorrespondsToReceive ) {
						$object = NLP::getLabelObject($label);
						if ( !is_null($object) && !empty($object) ) {
							$this->activities[$id] = "receive ".$object;
							if ( $debug ) print(" renamed to \"".$this->activities[$id]."\"\n");
							continue;
						}
					}
				}
				if ( $debug ) print(" no changes\n");
			}
		}
	}
	
	public function handleFunctionsWhichAreCatchEvents() {
	
		// check whether functions with incoming message flow might be intermediate catch events and correct it.
		foreach ( $this->activities as $id => $label ) {
			$isMessageReceiveActivity = $this->isMessageReceiveActivity($id);
			if ( $isMessageReceiveActivity !== false ) {
				//print(" ".$label." changed to ".$isMessageReceiveActivity." and added as an event\n");
				$this->events[$id] = $isMessageReceiveActivity;
				array_push($this->intermediateCatchEvents, $id);
				unset($this->activities[$id]);
			} else {
				//print(" ".$label." added as an activity\n");
				//$epc->functions[$id] = $label;
			}
		}
	}
	
	/**
	 * checks, whether an activity represents the Receivement of a message
	 * 
	 * @param unknown $nodeID
	 * @return false, if not, otherwise a new generated corresponding event label
	 */
	private function isMessageReceiveActivity($nodeID) {
		if ( $this->hasIncomingMessageFlow($nodeID) ) {
			$nodeLabel = $this->activities[$nodeID];
			$taggedLabel = NLP::tag($nodeLabel);
			$taggedLabel = NLP::extendTaggedTextWithHighLevelTags($taggedLabel);
			$labelStyle = NLP::getLabelStyle($taggedLabel);
			if ( is_null($labelStyle) ) return false;
			$verb = NLP::getLabelVerb($taggedLabel);
			if ( is_null($verb) || empty($verb) ) return false;
			$doesVerbCorrespondsToReceive = WordNet::checkIfVerbsAreSynonym($verb, "receive");
			if ( $doesVerbCorrespondsToReceive ) {
				$newEventLabel = NLP::getLabelObject($taggedLabel)." received";
				
				// CLI-Output
// 				print(" isMessageReceiveActivity - Label: ". $nodeLabel." | Tags: ");
// 				foreach ( $taggedLabel as $token ) {
// 					print("{".$token["tag"]."}");
// 				}
// 				print(" | High-Level-Tags: ");
// 				foreach ( $taggedLabel as $token ) {
// 					print("{".$token["high_level_tag"]."}");
// 				}
// 				$isReceive = $doesVerbCorrespondsToReceive ? "yes" : "no";
// 				print(" | Label Style: ".$labelStyle." | Verb: ".$verb." | isReceive: ".$isReceive." | new event label: ".$newEventLabel);
// 				print("\n");
				return $newEventLabel;
			} else {
				return false;
			}
			
			
		}
		return false;
	}
	
	private function hasIncomingMessageFlow($nodeID) {
		foreach ( $this->arcs as $arcID => $arc ) {
			if ( $arc["target"] == $nodeID ) {
				if ( in_array($arcID, $this->messageFlows) ) return true;
			}
		}
		return false;
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
	
	private function getIncomingEdgeID($nodeID) {
		$incomingEdgeIDs = array();
		foreach ( $this->arcs as $id => $arc ) {
			if ( $arc["target"] == $nodeID ) array_push($incomingEdgeIDs, $id);
		}
		return $incomingEdgeIDs;
	}
	
	private function getOutgoingEdgeID($nodeID) {
		$outgoingEdgeIDs = array();
		foreach ( $this->arcs as $id => $arc ) {
			if ( $arc["source"] == $nodeID ) array_push($outgoingEdgeIDs, $id);
		}
		return $outgoingEdgeIDs;
	}
	
	public function exportBPMN() {
		
		$content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$content .= "<definitions xmlns=\"http://www.omg.org/spec/BPMN/20100524/MODEL\" xmlns:bpmndi=\"http://www.omg.org/spec/BPMN/20100524/DI\""; 
		$content .= " xmlns:omgdc=\"http://www.omg.org/spec/DD/20100524/DC\" xmlns:omgdi=\"http://www.omg.org/spec/DD/20100524/DI\""; 
		$content .= " xmlns:RMMaaS=\"http://rmm.dfki.de\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\""; 
		$content .= " exporter=\"RefMod-Miner as a Service, http://rmm.dfki.de\" exporterVersion=\"1.0.0\"";
		$content .= " expressionLanguage=\"http://www.w3.org/1999/XPath\" id=\"".$this->id."\"";
		$content .= " targetNamespace=\"http://rmm.dfki.de/bpmn20\" typeLanguage=\"http://www.w3.org/2001/XMLSchema\"";
		$content .= " xsi:schemaLocation=\"http://www.omg.org/spec/BPMN/20100524/MODEL http://www.omg.org/spec/BPMN/2.0/20100501/BPMN20.xsd\">\n";
		
		$content .= $this->getBPMNExportCollaborationPart();
		$content .= $this->getBPMNExportProcessPart();
		
		$content .= "</definitions>";
		
		$fileGenerator = new FileGenerator(trim($this->name).".bpmn", $content);
		$fileGenerator->setFilename(trim($this->name).".bpmn");
		$file = $fileGenerator->execute();
		return $file;
	}
	
	private function getBPMNExportCollaborationPart() {
		$content  = "   <collaboration id=\"".$this->getNextID()."\">\n";
		
		foreach ( $this->participants as $id => $name ) {
			$participantProcessAssignments = array_flip($this->processParticipantAssignments);
			$content .= "      <participant id=\"".$id."\" name=\"".$name."\" processRef=\"".$participantProcessAssignments[$id]."\">\n";
			$content .= "      </participant>\n";
		}
		
		foreach ( $this->messageFlows as $msgFlowID ) {
			$content .= "      <messageFlow id=\"".$msgFlowID."\" name=\"".$this->arcs[$msgFlowID]["label"]."\" sourceRef=\"".$this->arcs[$msgFlowID]["source"]."\" targetRef=\"".$this->arcs[$msgFlowID]["target"]."\"/>\n";
		}
		
		$content .= "   </collaboration>\n";
		return $content;
	}
	
	private function getBPMNExportProcessPart() {
		$content = "";
		foreach ( $this->processParticipantAssignments as $processID => $participantID ) {
			$content .= "   <process id=\"".$processID."\" isClosed=\"false\" isExecutable=\"false\" name=\"".$this->participants[$participantID]."\" processType=\"None\">\n";
			
			$content .= "      <laneSet id=\"".$this->getNextID()."\">\n";
			foreach ( $this->lanes as $laneID => $laneName ) {
				$content .= "         <lane id=\"".$laneID."\">\n";
				
				$flowNodes = $this->getNodesOfLane($laneID);
				foreach ( $flowNodes as $flowNodeID ) {
					$content .= "            <flowNodeRef>".$flowNodeID."</flowNodeRef>\n";
				}
				
				$content .= "         </lane>\n";
			}
			
			$content .= "      </laneSet>\n";
			
			// start events
			foreach ( $this->startEvents as $startEventID ) {
				$nodeLabel = $this->events[$startEventID];
				$content .= "      <startEvent id=\"".$startEventID."\" name=\"".$nodeLabel."\">\n";
				$content .= $this->getExportIncomingOutgoingPart($startEventID);
				$content .= "      </startEvent>\n";
			}
			
			// generic events
			foreach ( $this->genericEvents as $genericEventID ) {
				$nodeLabel = $this->events[$genericEventID];
				$content .= "      <event id=\"".$genericEventID."\" name=\"".$nodeLabel."\">\n";
				$content .= $this->getExportIncomingOutgoingPart($genericEventID);
				$content .= "      </event>\n";
			}
			
			// end events
			foreach ( $this->endEvents as $endEventID ) {
				$nodeLabel = $this->events[$endEventID];
				$content .= "      <endEvent id=\"".$endEventID."\" name=\"".$nodeLabel."\">\n";
				$content .= $this->getExportIncomingOutgoingPart($endEventID);
				$content .= "      </endEvent>\n";
			}
			
			// intermediate catch event
			foreach ( $this->intermediateCatchEvents as $intermediateCatchEventID ) {
				$nodeLabel = $this->events[$intermediateCatchEventID];
				$content .= "      <intermediateCatchEvent id=\"".$intermediateCatchEventID."\" name=\"".$nodeLabel."\">\n";
				$content .= $this->getExportIncomingOutgoingPart($intermediateCatchEventID);
				$content .= "      </intermediateCatchEvent>\n";
			}
			
			// tasks
			foreach ( $this->activities as $activityID => $nodeLabel ) {
				$content .= "      <task completionQuantity=\"1\" id=\"".$activityID."\" isForCompensation=\"false\" name=\"".$nodeLabel."\" startQuantity=\"1\">\n";
				$content .= $this->getExportIncomingOutgoingPart($activityID);
				$content .= "      </task>\n";
			}
			
			// event based gateway
			foreach ( $this->eventBasedGateways as $eventBasedGatewayID ) {
				$nodeLabel = $this->gateways[$eventBasedGatewayID];
				$content .= "      <eventBasedGateway eventGatewayType=\"Exclusive\" gatewayDirection=\"Diverging\" id=\"".$eventBasedGatewayID."\" instantiate=\"false\" name=\"".$nodeLabel."\">\n";
				$content .= $this->getExportIncomingOutgoingPart($eventBasedGatewayID);
				$content .= "      </eventBasedGateway>\n";
			}
			
			// exclusive gateway
			foreach ( $this->exclusiveGateways as $exclusiveGatewayID ) {
				$nodeLabel = $this->gateways[$exclusiveGatewayID];
				$content .= "      <exclusiveGateway gatewayDirection=\"Diverging\" id=\"".$exclusiveGatewayID."\" name=\"".$nodeLabel."\">\n";
				$content .= $this->getExportIncomingOutgoingPart($exclusiveGatewayID);
				$content .= "      </exclusiveGateway>\n";
			}
			
			// parallel gateway
			foreach ( $this->parallelGateways as $parallelGatewayID ) {
				$nodeLabel = $this->gateways[$parallelGatewayID];
				$content .= "      <parallelGateway gatewayDirection=\"Diverging\" id=\"".$parallelGatewayID."\" name=\"".$nodeLabel."\">\n";
				$content .= $this->getExportIncomingOutgoingPart($parallelGatewayID);
				$content .= "      </parallelGateway>\n";
			}
			
			// sequence flow
			foreach ( $this->seqFlows as $seqFlowID ) {
				$arc = $this->arcs[$seqFlowID];
				$content .= "      <sequenceFlow id=\"".$seqFlowID."\" name=\"".$arc["label"]."\" sourceRef=\"".$arc["source"]."\" targetRef=\"".$arc["target"]."\">\n";
				$content .= "      </sequenceFlow>\n";
			}
			
			$content .= "   </process>\n";
		}
		return $content;
	}
	
	private function getExportIncomingOutgoingPart($nodeID) {
		$content = "";
		$incomingEdgeIDs = $this->getIncomingEdgeID($nodeID);
		$outgoingEdgeIDs = $this->getOutgoingEdgeID($nodeID);
		foreach ( $incomingEdgeIDs as $currentID ) {
			$content .= "         <incoming>".$currentID."</incoming>\n";
		}
		foreach ( $outgoingEdgeIDs as $currentID ) {
			$content .= "         <outgoing>".$currentID."</outgoing>\n";
		}
		return $content;
	}
	
	public function getNodesOfLane($laneID) {
		$nodeIDs = array();
		foreach ( $this->nodeLaneAssignments as $nodeID => $currentlaneID ) {
			if ( $currentlaneID == $laneID ) array_push($nodeIDs, $nodeID);
		}
		return $nodeIDs;
	}
	
}
?>