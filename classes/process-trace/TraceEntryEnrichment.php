<?php
class TraceEntryEnrichment {
	
	public $pots = array();
	public $originators = array();
	
	public function __construct(TraceEntry $entry) {
		$this->annotate($entry);
	}
	
	public function annotate(TraceEntry $entry) {
		array_push($this->pots, $entry->pot);
		array_push($this->originators, $entry->originator);
	}
	
}
?>