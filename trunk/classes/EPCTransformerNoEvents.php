<?php
class EPCTransformerNoEvents extends EPCTransformerNoConnectorsNoEvents {
	
	public function transform(EPC $epc) {
		$this->epc = $epc;
		$this->removeEvents();
		return $this->getTransformedEpc();
	}
	
}
?>