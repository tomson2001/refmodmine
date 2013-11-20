<?php
class CLIProgressbar {
	
	public $operations;	// Anzahl aller Operationen, die durchgefuehrt werden
	public $steps;		// Die Schritte in denen der Forschritt angezeigt werden soll, also beispielsweise 0,1 fuer 10% Schritte
	
	private $barLength;
	private $moveSymbol = "\\";
	private $lastSymbolChangeTimestamp;
	private $lastPrintTimestamp;
	
	public function __construct($operations, $steps) {
		if ( $steps <= 0 || $steps >= 1 ) throw new Exception("Invalid step specification in class CLIProgressbar");
		$this->operations = $operations;
		$this->steps = $steps;
		$this->barLength = (1 / $steps);
		$this->lastSymbolChangeTimestamp = time();
		$this->lastPrintTimestamp = round(microtime(true) * 1000);
	}
	
	public function run($operationNumber, $print = true) {
		$moveSymbol = $this->getMoveSymbol();
		$barString = " ".$moveSymbol." [";
		$currentProgressPercentage = $operationNumber / $this->operations;
		for ( $i=0; $i<$this->barLength; $i++ ) {
			$currentStep = $i*$this->steps;
			if ( $currentProgressPercentage == 1 ) {
				$barString .= "==";
			} elseif ( $currentProgressPercentage >= $currentStep && $currentProgressPercentage < ($currentStep+$this->steps) ) {
				$barString .= "=>";
			} elseif ( $currentProgressPercentage > $currentStep ) {
				$barString .= "==";
			} else {
				$barString .= "--";
			}
		}
		$percentage = round($currentProgressPercentage*100);
		$barString .= "] ".$percentage."%";
		$millitimestamp = round(microtime(true) * 1000);
		if ( $print && $millitimestamp - $this->lastPrintTimestamp > 200 ) print("\r".$barString);
		return $barString;
	}
	
	public function getMoveSymbol() {
		$time = time();
		if ( ($time-$this->lastSymbolChangeTimestamp) > 0 ) {
			$this->lastSymbolChangeTimestamp = $time;
		} else {
			return $this->moveSymbol;
		}
		if ( $this->moveSymbol == "\\" ) {
			$this->moveSymbol = "|";
		} elseif ( $this->moveSymbol == "|" ) {
			$this->moveSymbol = "/";
		} elseif ( $this->moveSymbol == "/" ) {
			$this->moveSymbol = "-";
		} else {
			$this->moveSymbol = "\\";
		}
		return $this->moveSymbol;
	}
		
}
?>