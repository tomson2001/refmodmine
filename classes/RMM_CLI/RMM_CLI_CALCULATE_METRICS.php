<?php
class RMM_CLI_CALCULATE_METRICS implements iRefModMiner {
	
	public $_RMM_CLI_COMMAND = "CALCULATE_METRICS"; 
	public $_RETURN_DIRECTLY = true;
	
	private $_RESULT_FILE_EXTENSION = "metrics";
	public static $_DOWNLOAD_FILE_EXTENSION = ".csv";
	
	public $execResult = "";
	
	private $modes = array(
		"standard" => "EVENTS FUNCTIONS AND_SPLITS AND_JOINS XOR_SPLITS XOR_JOINS OR_SPLITS OR_JOINS CONNECTORS NODES ARCS DIAMETER DENSITY_1 COEFFICIENT_CONNECTIVITY"
	);

	public function __construct($inputEPMLFilename, $mode="standard") {
		$execCommand = "java -jar ".Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME." CLI ".$this->_RMM_CLI_COMMAND;
		$execCommand .= " INPUT_DATA=".$inputEPMLFilename." OUTPUT_DATA=".$inputEPMLFilename.".".$this->_RESULT_FILE_EXTENSION.".".$mode;
		$execCommand .= " METRICS=".$this->modes[$mode];
		$execResult = exec($execCommand);
		$this->execResult = $execResult;
	}
	
	
	
}
?>