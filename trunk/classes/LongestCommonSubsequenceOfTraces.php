<?php
/**
 * Longest Common Subsequence Of Traces
 * 
 * basierend auf 
 * - Gerke, K., Cardoso, J., Claus, A.: Measuring the Compliance of Processes with Reference Models
 * - Becker, M., Laue, R.: A comparative surcvey of business process similarity measures
 * 
 * ACHTUNG: Bei der Trace Extraktion werden alle Konnektoren als XOR behandelt
 * 
 * @author t.thaler
 *
 */
class LongestCommonSubsequenceOfTraces implements ISimilarityMeasure {
	
	private $mapping;
	private $similarityValue;
	
	private $tracesOfEPC1 = null;
	private $tracesOfEPC2 = null;
	
	public function __construct(IMapping $mapping) {
		$this->mapping = $mapping;
	}
	
	public function calculate() {
		
		// ID-Wechsel in EPK2 anhand des Mappings
		$epc2 = $this->mapping->epc2;
		$epc2->assignFunctionMapping($this->mapping);
		
		// Transformation der EPKs, sodass nur noch Funktionen und Kanten enhalten sind
		$transformer = new EPCTransformerNoConnectorsNoEvents();
		$epc1 = $transformer->transform($this->mapping->epc1);
		$epc2 = $transformer->transform($epc2);

		// Extraktion aller moeglichen Traces
		$this->tracesOfEPC1 = $this->extractTraces($epc1);
		$this->tracesOfEPC2 = $this->extractTraces($epc2);
		
		// Anzahl der Traces von EPK1 und EPK2
		$numTracesOfEPC1 = count($this->tracesOfEPC1);
		$numTracesOfEPC2 = count($this->tracesOfEPC2);
		
		// Berechnung der compliance- und maturity-Werte fuer alle Trace-Kombinationen
		$cdTraces = array();
		$mdTraces = array();
		foreach ( $this->tracesOfEPC1 as $i => $trace1 ) {
			foreach ( $this->tracesOfEPC2 as $j => $trace2 ) {
				$cdTraces[$j][$i] = count($this->longestCommonSubsequence($trace1, $trace2)) / count($trace2);
				$mdTraces[$i][$j] = count($this->longestCommonSubsequence($trace1, $trace2)) / count($trace1);
			}
		}
		
		// Berechnung der Summe der maximalen compliance-Werte fuer jeden Trace von EPK2
		$cd_numerator = 0;
		foreach ( $cdTraces as $trace_cds ) {
			$cd_numerator += max($trace_cds);
		}
		
		// Berechnung der Summer der maximalen maturity-Werte fuer jeden Trace von EPK1
		$md_numerator = 0;
		foreach ( $mdTraces as $trace_mds ) {
			$md_numerator += max($trace_mds);
		}
		
		// Berechnung von Compliance und Maturity
		$cd = $cd_numerator / $numTracesOfEPC2;
		$md = $md_numerator / $numTracesOfEPC1;
		
		// Ergebnis ist Mittelwert aus Compliance und Maturity
		$this->similarityValue = round((($cd + $md) / 2)*100 ,2); 
		
		return $this->value();
	}
	
	public function value() {
		return $this->similarityValue;
	}
	
	/**
	 * Stoesst die Extraktion der Traces an
	 * 
	 * @param EPC $epc
	 * @return boolean
	 */
	private function extractTraces(EPC $epc) {
		// Startknoten suchen
		$startNodeId = $epc->getFirstNode();
		if ( is_null($startNodeId) ) return false;
		$traceExtration = array( // detection
					  array(
						  'trace' => array($startNodeId),
						  'backEdges' => array() 
					  )
				  );
		// Rekursive Trace-Extraktion
		$traceExtrationResult = $this->continueTraceExtraction($epc, $traceExtration);
		
		// Array von Traces erstellen
		$traces = array();
		foreach ( $traceExtrationResult as $traceExtractionIndex => $detection ) {
			array_push($traces, $detection['trace']);
		}
		//print_r($traces);
		return $traces;
	}
	
	/**
	 * Rekursive Trace-Extraktion
	 * 
	 * @param EPC $epc
	 * @param Array $traceExtration
	 * @return Array
	 */
	private function continueTraceExtraction(EPC $epc, $traceExtration) {
		$somethingDone = false;
		$newTraceExtraction = array();
		// Iteration ueber bisher extrahierte Trace-Segemente
		foreach ( $traceExtration as $traceExtractionIndex => $detection ) {
			$lastTraceNodeID = end($detection['trace']);
			$successors = $epc->getSuccessor($lastTraceNodeID);
			
			// Endknoten erreicht
			if ( count($successors) == 0 ) {
				array_push($newTraceExtraction, $detection);
				continue;
			}
			
			// Iteration ueber die Nachfolgeknoten
			foreach ($successors as $successor) {
				// Pruefen ob der Nachfolgeknoten (Kante) im Trace vorhanden ist (waere dann ein Rueckschritt)
				if ( in_array($successor, $detection['trace']) ) {
					// Pruefen ob es sich um einen bereits durchlaufenen Rueckschritt handelt
					if ( in_array($lastTraceNodeID."_".$successor, $detection['backEdges']) ) {
						// Wenn dieser Rueckschritt (Kante) bereits durchlaufen wurde, dann verwerfen
						continue;
					} else {
						// Knoten dem Trace hinzufuegen und Rueckschritt merken
						$newDetection = $detection;
						array_push($newDetection['trace'], $successor);
						array_push($newDetection['backEdges'], $lastTraceNodeID."_".$successor);
						array_push($newTraceExtraction, $newDetection);
						$somethingDone = true;
					}
				} else {
					// Knoten dem Trace hinzufuegen
					$newDetection = $detection;
					array_push($newDetection['trace'], $successor);
					array_push($newTraceExtraction, $newDetection);
					$somethingDone = true;
				}
				
			}
		}
		
		// Wenn etwas getan wurde mache weiter, ansonsten hoere auf
		if ( $somethingDone ) {
			return $this->continueTraceExtraction($epc, $newTraceExtraction);
		} else {
			//print_r($newTraceExtraction);
			return $newTraceExtraction;
		}
	}
	
	/**
	 * Berechunng der "Longest Common Subsequence" zweier Traces
	 *
	 * @link http://en.wikipedia.org/wiki/Longest_common_subsequence_problem
	 * @link Source: https://gist.github.com/1193894
	 *
	 * @param array $trace1 Erster Trace
	 * @param array $trace2 Erster Trace
	 * 
	 * @return array "Longest Common Subsequence"
	 */
	private function longestCommonSubsequence(array $trace1, array $trace2)
	{
		$m = count($trace1);
		$n = count($trace2);
	
		// $a[$i][$j] = length of LCS of $left[$i..$m] and $right[$j..$n]
		$a = array();
	
		// compute length of LCS and all subproblems via dynamic programming
		for ($i = $m - 1; $i >= 0; $i--){
			for ($j = $n - 1; $j >= 0; $j--) {
				if ($trace1[$i] == $trace2[$j]) {
					$a[$i][$j] = (isset($a[$i + 1][$j + 1]) ? $a[$i + 1][$j + 1] : 0) + 1;
				} else {
					$a[$i][$j] = max(
							(isset($a[$i + 1][$j]) ? $a[$i + 1][$j] : 0)
							, (isset($a[$i][$j + 1]) ? $a[$i][$j + 1] : 0)
					);
				}
			}
		}
	
		// recover LCS itself
		$i = 0;
		$j = 0;
		$lcs = array();
	
		while($i < $m && $j < $n) {
			if ($trace1[$i] == $trace2[$j]) {
				$lcs[] = $trace1[$i];
				$i++;
				$j++;
			} elseif (
					(isset($a[$i + 1][$j]) ? $a[$i + 1][$j] : 0)
					>= (isset($a[$i][$j + 1]) ? $a[$i][$j + 1] : 0)
			) {
				$i++;
			} else {
				$j++;
			}
		}
	
		return $lcs;
	}
	
}
?>