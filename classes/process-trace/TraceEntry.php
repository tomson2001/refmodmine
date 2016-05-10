<?php
/**
 * represents an entry of a process instance (trace)
 * 
 */
class TraceEntry {
	
	public $id; // a hash build from the content serving as an identifyer
	
	public $activity;
	public $type;
	public $pot;
	public $originator;
	public $positionInTrace;
	
	public $relatedTraceID;
	
	public $enrichment = null;
	
	/**
	 * Constructor
	 * 
	 * @param string $lable
	 * @param timestamp $pot
	 * @param string $originator
	 * @param string $type
	 */
	public function __construct($activity, $pot, $originator, $type, $positionInTrace) {
		$this->activity = $activity;
		$this->type = $type;
		$this->pot = $pot;
		$this->originator = $originator;
		$this->positionInTrace = $positionInTrace;
		
		$this->id = md5($activity.$pot.$originator.$type.$positionInTrace);
	}
	
	/**
	 * adds information of other executions to that trace entry
	 * 
	 * @param TraceEntry $entry
	 */
	public function enrich(TraceEntry $entry) {
		if ( $this->enrichment == null ) $this->enrichment = new TraceEntryEnrichment($this);
		$this->enrichment->annotate($entry);
	}
	
}
?>