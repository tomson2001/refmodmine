<?php
class EPCTransformerNoEventsButEnds extends EPCTransformerNoConnectorsNoEvents {
	
	public function transform(EPC $epc) {
		$this->epc = $epc;
		$this->tryToCorrectErrors();
		
		$this->removeEvents(array('IGNORE_TRACE_IMPORTANT_START_EVENTS', 'IGNORE_TRACE_IMPORTANT_END_EVENTS'));
		$this->removeDuplicatedEdges();
		$this->removeSESEConnectors();
		$this->removeSenselessConnectors();
		$this->removeSESEConnectors();
		
		// Nochmal machen, damit ggf. Aenderungen beruecksichtigt werden
		$this->removeEvents(array('IGNORE_TRACE_IMPORTANT_START_EVENTS', 'IGNORE_TRACE_IMPORTANT_END_EVENTS'));
		$this->removeDuplicatedEdges();
		$this->removeSESEConnectors();
		$this->removeSenselessConnectors();
		$this->removeSESEConnectors();
		
		// Aller Guten Dinge sind drei
		$this->removeEvents(array('IGNORE_TRACE_IMPORTANT_START_EVENTS', 'IGNORE_TRACE_IMPORTANT_END_EVENTS'));
		$this->removeDuplicatedEdges();
		$this->removeSESEConnectors();
		$this->removeSenselessConnectors();
		$this->removeSESEConnectors();
		return $this->getTransformedEpc();
	}
	
}
?>