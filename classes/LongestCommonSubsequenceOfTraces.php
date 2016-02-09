<?php
/**
 * Longest Common Subsequence Of Traces
 *
 * basierend auf
 * - Gerke, K., Cardoso, J., Claus, A.: Measuring the Compliance of Processes with Reference Models
 * - Becker, M., Laue, R.: A comparative surcvey of business process similarity measures
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
		$nameOfEPC1 = $this->mapping->epc1->name;
		$nameOfEPC2 = $this->mapping->epc2->name;
		//print("---".$nameOfEPC1.":".$nameOfEPC2."---");
		if ( strcmp($nameOfEPC1, $nameOfEPC2) == 0 ) {
			$this->similarityValue = round(100, 2);
			//print("break");
			return 1;
		} else {
			//print(strcmp($nameOfEPC1, $nameOfEPC2));
			// ID-Wechsel in EPK2 anhand des Mappings
			$epc2 = $this->mapping->epc2;
			$epc2->assignFunctionMapping($this->mapping);
			$epc1 = $this->mapping->epc1;

			$this->tracesOfEPC1 = $epc1->traces;
			$this->tracesOfEPC2 = $epc2->traces;
				
			//print("\n\n".$epc1->name."\n ");

			// Pruefen auf Fehlermeldungen
			if ( is_string($this->tracesOfEPC1) ) {
				$this->similarityValue = $this->tracesOfEPC1;
				print($this->tracesOfEPC1);
				return $this->value();
			}

			if ( is_string($this->tracesOfEPC2) ) {
				$this->similarityValue = $this->tracesOfEPC2;
				print($this->tracesOfEPC2);
				return $this->value();
			}

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
			$cd = $numTracesOfEPC2 == 0 ? 0 : $cd_numerator / $numTracesOfEPC2;
			$md = $numTracesOfEPC1 == 0 ? 0 : $md_numerator / $numTracesOfEPC1;

			// Ergebnis ist Mittelwert aus Compliance und Maturity
			$this->similarityValue = round((($cd + $md) / 2)*100 ,2);

			return $this->value();
		}
	}

	public function value() {
		return $this->similarityValue;
	}

	/**
	 * Berechunng der "Longest Common Subsequence" zweier Traces
	 *
	 * @link http://en.wikipedia.org/wiki/Longest_common_subsequence_problem
	 * @link Source: https://gist.github.com/1193894
	 *
	 * @param array $trace1 Erster Trace
	 * @param array $trace2 Zweiter Trace
	 *
	 * @return array "Longest Common Subsequence"
	 */
	public static function longestCommonSubsequence(array $trace1, array $trace2)
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