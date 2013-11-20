<?php
class MultiThreadMappingOperation extends Thread {

	public $epcsOfFile1;
	public $epcsOfFile2Part;
	public $scriptArgv;
	public $allMatchedFuncNodesOfModelFile1;
	public $allMatchedFuncNodesOfModelFile2;
	public $folderName;
	public $html_analysis_part = "";
	public $analysis_csv_part = "";
	public $threadCount;
	public $naryMapping;
	public $removedPossibleEvents = array();
	
	public $finishedOperations = 0;

	public function __construct($threadId, $epcsOfFile1, $epcsOfFile2Part, $scriptArgv, $foldername, $threadCount, $naryMapping=null) {
		$this->threadId = $threadId;
		$this->epcsOfFile1 = $epcsOfFile1;
		$this->epcsOfFile2Part = $epcsOfFile2Part;
		$this->scriptArgv = $scriptArgv;
		$this->folderName = $foldername;
		$this->threadCount = $threadCount;
		$this->naryMapping = $naryMapping;
	}

	/**
	 * wird bei start() ausgefuehrt. Per String kann die Thread-ID mit %3 ausgegeben werden.
	 */
	public function run() {
		spl_autoload_register('Autoloader::load');
		
		$allMatchedFuncNodesOfModelFile1 = array();
		$allMatchedFuncNodesOfModelFile2 = array();
		$html_analysis_part = "";
		$analysis_csv_part = "";

		//$content_file_2 = file_get_contents(Config::MODEL_FILE_2);
		//$xml2 = new SimpleXMLElement($this->content_file_2);

		//foreach ($xml2->xpath("//epc") as $xml_epc2) {

		foreach ( $this->epcsOfFile1 as $epc1 ) {

			foreach ( $this->epcsOfFile2Part as $epc2 ) {

				$html_analysis_part .= "<h3>".$epc1->name." <=> ".$epc2->name."</h3>";
				
				// Die Funktionen, die wahrscheinlich ein Ereignis sind, werden jetzt aus der EPK entfernt
				if ( $this->scriptArgv[1] == "--nscm" ) {
					$this->removedPossibleEvents = $this->naryMapping->removedPossibleEvents;
					foreach ( $this->naryMapping->removedPossibleEvents as $funcNode ) {
						if ( $funcNode->epc->internalID == $epc1->internalID ) $epc1->transformFunctionToEvent($funcNode->id);
						if ( $funcNode->epc->internalID == $epc2->internalID ) $epc2->transformFunctionToEvent($funcNode->id);
					}
				}

				// Matrix berechnen
				$analysis_csv_part .= $epc1->name.";".count($epc1->functions).";".count($epc1->events).";".$epc2->name.";".count($epc2->functions).";".count($epc2->events).";";

				// Mapping-Initalisierung
				$mapping = null;

				// Auswahl der Mappings fuer die entsprechenden Aehnlichkeitsmasse
				switch ( $this->scriptArgv[1] ) {

					case "--nscm": 
						$mapping = $this->naryMapping->extractBinaryMapping($epc1, $epc2);
						break;
					
					// Funktionen ueber Levenshtein, Konnektoren ueber Ein- und Ausgehende Kanten
					case "--fbse":
						$mapping = new LevenshteinWithStructuralMapping($epc1, $epc2);
						//$mapping->setParams(array('threshold_levenshtein' => 91));
						break;

						// Identity
					case "--ssbocan":
					case "--pocnae":
					case "--cf":
						$mapping = new LevenshteinMapping($epc1, $epc2);
						$mapping->setParams(array('threshold_levenshtein' => 50));
						//$mapping->setParams(array('threshold_levenshtein' => 100));
						break;

						// Funktionen ueber Levenshtein und Ein- und Ausgehende Kanten
					case "--amaged":
						$mapping = new LevenshteinWithContextMapping($epc1, $epc2);
						break;

						// kein Mapping
					case "--ts":
						$mapping = null;
						break;

						// Funktionen ueber Levenshtein
					default:
						$mapping = new LevenshteinMapping($epc1, $epc2);
						// Grenze auf 50% Aehnlichkeit setzen
						$mapping->setParams(array('threshold_levenshtein' => 90));
						break;
				}

				/**
				 * Angabe des Algorithmus, der fuer das Mapping verwendet werden soll: "Greedy", "Simple"
				 */
				if ( $this->scriptArgv[1] != "--nscm" ) {
					$mapping->map("Greedy");
					//$mapping->map("Simple");
				} else {
					$mapping->map("AllOne");
				}

				$matchedFuncs1 = count($mapping->mappedNodesOfEPC1);
				$matchedFuncs2 = count($mapping->mappedNodesOfEPC2);
				$matches = $mapping->getNumOfMatches();
				$complexMatches = $mapping->getNumOfComplexMatches();
				$simpleMatches = $mapping->getNumOfSimpleMatches();
				
				$numOfAllFuncs = count($epc1->functions) + count($epc2->functions);
				$modelSimilarity = $numOfAllFuncs == 0 ? 0 : round((($matchedFuncs1 + $matchedFuncs2) / $numOfAllFuncs)*100, 2);
				
				$analysis_csv_part .= $matchedFuncs1.";".$matchedFuncs2.";".$matches.";".$simpleMatches.";".$complexMatches.";".str_replace(".", ",", $modelSimilarity)."\n";

				if ( $modelSimilarity != 0 ) $mapping->export2($this->folderName."/", false);
				$matrix = $mapping->getMatrix();

				// Schreiben der insgesamt gematchten Funktionen
				foreach ( $mapping->mappedNodesOfEPC1 as $id ) {
					array_push($allMatchedFuncNodesOfModelFile1, array("id" => $id, "label" => $epc1->functions[$id], "EPC_InternalID" => $epc1->internalID, "EPC_Name" => $epc1->name));
				}
				foreach ( $mapping->mappedNodesOfEPC2 as $id ) {
					array_push($allMatchedFuncNodesOfModelFile2, array("id" => $id, "label" => $epc2->functions[$id], "EPC_InternalID" => $epc2->internalID, "EPC_Name" => $epc2->name));
				}

				// Matrix in HTML
				$isMappingPrecise = true;
				$html_analysis_part .= "<table border='1'>";
				$html_analysis_part .= "<tr><th></th>";
				foreach ($epc2->functions as $func_id => $label) {
					$html_analysis_part .= "<th height=".((int) strlen($label)*8.5)."><div class='verticalText'>".$label." (".$func_id.")</div></th>";
				}
				$html_analysis_part .= "</tr>";
				foreach ( $matrix as $id1 => $arr ) {
					$label1 = $epc1->getNodeLabel($id1);
					$html_analysis_part .= "<tr><td>".$label1." (".$id1.")</td>";
					$maxLevenshteinSimilarity = Tools::getMaxValueHorizontal($matrix[$id1]);
					foreach ( $arr as $id2 => $value ) {
						$label2 = $epc2->getNodeLabel($id2);
						if ( $mapping->isMapped($id1, $id2) ) {
							$highlight = "bgcolor=green";
							if ( $mapping->isMappedPrecisely($id1, $id2) ) {
								$highlight = "bgcolor=green";
							} else {
								$isMappingPrecise = false;
								$highlight = "bgcolor=red";
							}
						} else {
							$highlight = "bgcolor=white";
						}
						$html_analysis_part .= "<td align='center' ".$highlight.">".$value."</td>";

					}
					$html_analysis_part .= "</tr>";
				}
				$html_analysis_part .= "</table>";

				// FORTSCHRITTSANZEIGE
				$this->finishedOperations++;
// 				$this->countCompletedCombinations++;

// 				if ( ($this->countCompletedCombinations/$this->countCombinations) >= $this->progress ) {
// 					print(" ".($this->progress*100)."% ");
// 					$this->progress += 0.1;
// 				}
				// ENDE DER FORTSCHRITTSANZEIGE
			}
		}
		
		$this->analysis_csv_part = $analysis_csv_part;
		$this->html_analysis_part = $html_analysis_part;
		
		$this->allMatchedFuncNodesOfModelFile1 = $allMatchedFuncNodesOfModelFile1;
		$this->allMatchedFuncNodesOfModelFile2 = $allMatchedFuncNodesOfModelFile2;
	}

}
?>