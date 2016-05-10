<?php
/**
 * represents a trace of a process log
 * 
 * @author t.thaler
 *
 */
class Trace {
	
	public $id;
	public $traceEntries = array();
	public $numOccurences = 1;
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function addTraceEntry(TraceEntry $traceEntry) {
		$traceEntry->relatedTraceID = $this->id;
		array_push($this->traceEntries, $traceEntry);
	}
	
	/**
	 * In case of an aggregated Log, that Log enrichs the Trace with the 
	 * execution information of another Log, e.g. annoted the timestamps
	 * and originator of other trace to that aggregated task.
	 * 
	 * @param Trace $trace
	 */
	public function enrich($trace) {
		$this->numOccurences++;
		foreach ( $trace->traceEntries as $entry ) {
			$index = $this->getTraceEntryIndex($entry);
			$this->traceEntries[$index]->enrich($entry);
		}
	}
	
	/**
	 * In case of an aggregated Log, the trace entries should be representative in
	 * terms of timestamps, durations, originators etc. This function calculates
	 * e.g. new timestamps, so that the duration between events is a mean value
	 * of all process instances. Therefore, the first start is used.
	 */
	public function makeEntriesRepresentative() {
		if ( $this->numOccurences == 1 ) return;
		
		// Set Start timestamp of first entry to first occurence timestamp
		$currentEntry = $this->getStart();
		$index = $this->getTraceEntryIndex($currentEntry);
		$this->traceEntries[$index]->pot = min($currentEntry->enrichment->pots);
		// Write all Originators to the originator field
		$this->traceEntries[$index]->originator = implode(" | ", $currentEntry->enrichment->originators);
		
		// Recursively do that for all successors
		$nextEntry = $this->getSuccessor($currentEntry);
		if ( $nextEntry != null ) $this->recursiveRepresentativeEntrySetting($nextEntry, $this->traceEntries[$index]->pot);
	}
	
	/**
	 * Recursively making the TraceEntries representative (in case of an aggregated Log)
	 * 
	 * @param TraceEntry $entry
	 * @param predPot Timestamp of the predecessor trace entry
	 */
	private function recursiveRepresentativeEntrySetting(TraceEntry $entry, $predPot) {
		$index = $this->getTraceEntryIndex($entry);
		
		// Use average duration between two events for setting timestamp
		$sumDuration = 0;
		foreach ( $entry->enrichment->pots as $pot ) {
			$sumDuration += $pot - $predPot;
		}
		$this->traceEntries[$index]->pot = $sumDuration / $this->numOccurences;
		
		// Write all originators to the originator field
		$this->traceEntries[$index]->originator = implode(" | ", $currentEntry->enrichment->originators);
		
		// continue with successor
		$nextEntry = $this->getSuccessor($entry);
		if ( $nextEntry != null) $this->recursiveRepresentativeEntrySetting($nextEntry, $this->traceEntries[$index]->pot);
	}
	
	/**
	 * returns the index of the trace entry which has the same name and type like $entry
	 * 
	 * @param TraceEntry $entry
	 * @return int, if no corresponding entry found than NULL
	 * 	 
	 */
	private function getTraceEntryIndex(TraceEntry $entry) {
		foreach ( $this->traceEntries as $index => $currentEntry ) {
			if ( $entry->activity == $currentEntry->activity && $entry->type == $currentEntry->type && $entry->positionInTrace == $currentEntry->positionInTrace ) return $index;
		}
		return null;
	}
	
	/**
	 * Return the start event of the trace
	 * 
	 * @return TraceEntry
	 */
	public function getStart() {
		foreach ( $this->traceEntries as $entry ) {
			if ( $entry->positionInTrace == 1 ) return $entry;
		}
		return null;
	}
	
	/**
	 * Returns the successor event of a given event (Trace Entry)
	 * 
	 * @param TraceEntry $traceEntry
	 */
	public function getSuccessor(TraceEntry $traceEntry) {
		foreach ( $this->traceEntries as $entry ) {
			if ( $entry->positionInTrace == $traceEntry->positionInTrace+1 ) return $entry;
		}
		return null;
	}
	
	/**
	 * Returns the predecessor event of a given event (Trace Entry)
	 *
	 * @param TraceEntry $traceEntry
	 */
	public function getPredecessor(TraceEntry $traceEntry) {
		foreach ( $this->traceEntries as $entry ) {
			if ( $entry->positionInTrace == $traceEntry->positionInTrace-1 ) return $entry;
		}
		return null;
	}
	
	/**
	 * Produces an array with the names of the containing events ordered by timestamp
	 * 
	 * @return array
	 */
	public function getOrderedEventNameArray() {
		$arr = array();
		$entry = $this->getStart();
		array_push($arr, $entry->activity);
		$successor = $this->getSuccessor($entry);
		while ( $successor != null ) {
			array_push($arr, $successor->activity);
			$successor = $this->getSuccessor($successor);
		}
		return $arr;
	}
	
	/**
	 * Produces a string of the workflow, as e.g. A -> B -> C
	 * 
	 * @return string
	 */
	public function getWorkflowString() {
		$arr = $this->getOrderedEventNameArray();
		return implode(" -> ", $arr);
	}
	
	/**
	 * Because of performance reasons, the position of an entry within a trace a saved as an
	 * attribute of the entry. In case of adding new entries between other entries, or if 
	 * the MXML-File is not ordered (which should not be the case!), that function might be
	 * necessary. Thus, it recalculates the entry position based on the timestamps.  
	 */
	public function refactorTraceEntryPositions() {
		// TODO
	}
}
?>