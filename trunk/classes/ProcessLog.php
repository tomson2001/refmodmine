<?php
/**
 * represents a process los
 * 
 * @author t.thaler
 *
 */
class ProcessLog {
	
	private $filename;
	public $isAggregated;
	public $traces = array();
	
	/**
	 * 
	 * @param unknown_type $filename
	 * @param boolean $do_aggregate Determine if the log is being aggregates. If it is 
	 * 								set to true, equal traces in terms of the contained
	 * 								events and its order are only inserted once
	 */
	public function __construct($filename=null, $aggregate=false) {
		$this->filename = $filename;
		$this->isAggregated = $aggregate;
	}
	
	/**
	 * adds a trace to the process log
	 * 
	 * @param Trace   $trace		The Trace
	 */
	public function addTrace(Trace $trace) {
		if ( $this->isAggregated ) {
			$index = $this->containsTrace($trace);
			if ( $index !== false ) {
				$this->traces[$index]->enrich($trace);
				return false;
			}
		}
		array_push($this->traces, $trace);
	}
	
	public function containsTrace($trace) {
		foreach ( $this->traces as $index => $currentTrace ) {
			if ( $trace->getWorkflowString() == $currentTrace->getWorkflowString() ) return $index;
		}
		return false;
	}
	
	/**
	 * calculates the number of all contained traces
	 * 
	 * @return int
	 */
	public function getNumTraces() {
		$numTraces = 0;
		foreach ( $this->traces as $trace ) {
			$numTraces += $trace->numOccurences;
		}
		return $numTraces;
	}
	
	/**
	 * calculates the number of different traces
	 * 
	 * @return int
	 */
	public function getNumDifferentTraces() {
		$differntTraces = array();
		foreach ( $this->traces as $trace ) {
			$workflowString = $trace->getWorkflowString();
			if ( !in_array($workflowString, $differntTraces) ) array_push($workflowString);
		}
		return count($differntTraces);
	}
	
	/**
	 * exports the ProcessLog as an MXML
	 */
	public function exportMXML($logDescription="Unified single process") {
		if ( $this->isAggregated ) $logDescription .= " | aggregated";
		
		$content = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
		$content .= "<!-- MXML version 1.1 -->\n";
		$content .= "<!-- Created by RefMod-Miner (PHP) CLI -->\n";
		$content .= "<!-- Tom Thaler (tom.thaler@iwi.dfki.de); Institute for Information Systems (IWi) at the German Research Center for Artificial Intelligence (DFKI) and Saarland University -->\n";
		$content .= "<!-- You can load this file e.g. with the RefMod-Miner (refmod-miner.dfki.de) or in the ProM Framework for Process Mining. -->\n";
		$content .= "<!-- More information about MXML http://www.processmining.org/. -->\n";
		$content .= "<WorkflowLog xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"http://is.tm.tue.nl/research/processmining/WorkflowLog.xsd\" description=\"".$logDescription."\">\n";
		$content .= "   <Process id=\"UNIFIED\" description=\"Unified single process\">\n";
		
		foreach ( $this->traces as $trace ) {
			$content .= "      <ProcessInstance id=\"".$trace->id."\">\n";
			
			if ( $this->isAggregated ) {
				$content .= "         <Data>\n";
				$content .= "            <Attribute name=\"numOccurences\">".$trace->numOccurences."</Attibute>\n";
				$content .= "         </Data>\n";
			}
			
			$entry = $trace->getStart();
			while ($entry != null) {
				$content .= "         <AuditTrailEntry>\n";
				$content .= "            <WorkflowModelElement>".$entry->activity."</WorkflowModelElement>\n";
				$content .= "            <EventType>".$entry->type."</EventType>\n";
				$content .= "            <Timestamp>".date("c", $entry->pot)."</Timestamp>\n";
				$content .= "            <Originator>".$entry->originator."</Originator>\n";
				$content .= "         </AuditTrailEntry>\n";
				$entry = $trace->getSuccessor($entry);
			}
			
			$content .= "      </ProcessInstance>\n";
		}
		
		$content .= "   </Process>\n";
		$content .= "</WorkflowLog>";
		
		$filename = $this->isAggregated ? "aggregated_".basename($this->filename) : basename($this->filename);
		$fileGenerator = new FileGenerator($filename, $content);
		$fileGenerator->setFilename($filename);
		$file = $fileGenerator->execute();
		return $file;
	}
	
}
?>