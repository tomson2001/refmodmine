<?php
class EPCTransformerNoEvents extends EPCTransformerNoConnectorsNoEvents {
	
	public function transform(EPC $epc) {
		$this->epc = $epc;
		$this->removeEvents();
		$this->removeDuplicatedEdges();
		$this->removeSESEConnectors();
		$this->removeSenselessConnectors();
		$this->removeSESEConnectors();
		return $this->getTransformedEpc();
	}
	
}
?>