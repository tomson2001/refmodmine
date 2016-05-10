<?php
class MultiThreadFunctionOntologyOperation extends Thread {

	public $epcs;
	
	public $functions;
	public $finishedOperations = 0;

	public function __construct($epcsPart) {
		$this->epcs = $epcsPart;
	}

	/**
	 * wird bei start() ausgefuehrt. Per String kann die Thread-ID mit %3 ausgegeben werden.
	 */
	public function run() {
		spl_autoload_register('Autoloader::load');
		
		$functions = array();
		foreach ( $this->epcs as $epc ) {
			foreach ( $epc->functions as $id => $label ) {
				$functionOngology = new FunctionOntologyWithSynonyms($epc, $id, $label);
				array_push($functions, serialize($functionOngology));
				$this->finishedOperations++;
			}
		}
		$this->functions = $functions;
	}

}
?>