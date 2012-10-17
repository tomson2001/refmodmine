<?php
class EPCTransformerNoEvents extends EPCTransformerNoConnectorsNoEvents {
	
	public function transform(EPC $epc) {
		$this->epc = $epc;
		$this->removeEvents();
		$this->removeDuplicatedEdges();
		return $this->getTransformedEpc();
	}
	
}
?>