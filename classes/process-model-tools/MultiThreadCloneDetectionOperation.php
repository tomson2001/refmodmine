<?php
class MultiThreadCloneDetectionOperation extends Thread {

	public $modelPairs;
	public $exportFolder;

	public $finishedOperations = 0;

	public function __construct($cloneDetectionModelPairs, $exportFolder="") {
		$this->modelPairs = $cloneDetectionModelPairs;
		$this->exportFolder = $exportFolder;
	}

	/**
	 * wird bei start() ausgefuehrt. Per String kann die Thread-ID mit %3 ausgegeben werden.
	 */
	public function run() {
		spl_autoload_register('Autoloader::load');

		foreach ( $this->modelPairs as $pair ) {
			$epc1 = $pair[0];
			$epc2 = $pair[1];
			$cloneDetector = new RefModCloneDetector($epc1, $epc2);
			$cloneDetector->execute();
			$cloneDetector->exportClonesAsEPML($this->exportFolder);
			$this->finishedOperations++;
		}
	}

}
?>