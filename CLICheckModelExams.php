<?php
$start = time();
require 'autoloader.php';

print("\n--------------------------------------------------------------------\n RefMod-Miner (PHP) - Automatic Scoring of Modelling Exams \n--------------------------------------------------------------------\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) || !isset($argv[4]) || !isset($argv[5]) || !isset($argv[6]) || !isset($argv[7]) || !isset($argv[8]) || !isset($argv[9]) || !isset($argv[10]) || !isset($argv[11]) ) {
	exit("   Please provide the following parameters:\n
   input=            path to input epml
   sample_solution=  model name of the sample solution
   output=           path to output file
   notification=
      no
      [E-Mail adress]
   error_types=	     (enter several types separated by a comma)
       1   Missing start and end events
       2   activities have exactly one in-coming and exactly one outgoing edge
       3   events have at least one in-coming and at least one outgoing edge
       4   alternating events and activities
       5   labels should be unique (each label occurs exactly once)
       6   connectors are either split (one imcoming, more than one outgoing edges) or join (more than one incoming, one outgoing edges) connectors
   warning_types=    (analogue to error_types)
   max_points=       maximum points overall
   weight_syntax=    weight of syntax for overall scoring (use 2 as default)
   weight_functions= weight of functioin recall for overall scoring (use 2 as default)
   weight_behaviour= weight of behavioural f-measure for overall scoring (use 1 as default)
   max_syntax_error= if this number of syntax errors is reached, the syntax part is rated with 0
   [metrics]=        filename of a csv containing metrics for all input models

   please use the correct order!
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$input   = 				substr($argv[1],  6,  strlen($argv[1]));
$sampleSolution = 		substr($argv[2],  16, strlen($argv[2]));
$output  = 				substr($argv[3],  7,  strlen($argv[3]));
$email   = 				substr($argv[4],  13, strlen($argv[4]));
$error_types = 			substr($argv[5],  12, strlen($argv[5]));
$warning_types = 		substr($argv[6],  14, strlen($argv[6]));
$max_points = 			substr($argv[7],  11, strlen($argv[7]));
$weight_syntax = 		substr($argv[8],  14, strlen($argv[8]));
$weight_functions = 	substr($argv[9],  17, strlen($argv[9]));
$weight_behaviour = 	substr($argv[10], 17, strlen($argv[10]));
$max_syntax_error = 	substr($argv[11], 17, strlen($argv[11]));
$metrics_csv = 			isset($argv[12]) ? substr($argv[12], 8, strlen($argv[12])) : "no";

print("
input: ".$input."
sample solution: ".$sampleSolution."
output: ".$output."
notification: ".$email."

checking input parameters ...
");

// Check input
if ( file_exists($input) ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file does not exist)");
}

// Check sample solution
// @TODO: check, whether der sample solution exists in model file
if ( strlen($sampleSolution) > 0 ) {
	print "  sample solution ... ok\n";
} else {
	exit("  sample solution ... failed (sample solution does not exist in input file)");
}

// Check notification
$doNotify = true;
if ( empty($email) || $email == "no" ) {
	$doNotify = false;
	print "  notification ... disabled\n";
} else {
	print "  notification ... ok (mail to ".$email.")\n";
}

// Check error types
$error_types = explode(",", $error_types);
$errTypeOK = true;
foreach ( $error_types as $type ) {
	if ( !in_array($type, array(1,2,3,4,5,6)) ) $errTypeOK = false;
}
if ( !$errTypeOK ) {
	exit("  error types ... unknown error type!\n");
} else {
	print "  error types ... ok (".implode(", ", $error_types).")\n";
}

// Check warning types
$warning_types = explode(",", $warning_types);
$warTypeOK = true;
foreach ( $warning_types as $type ) {
	if ( !in_array($type, array(1,2,3,4,5,6)) ) $warTypeOK = false;
}
if ( !$warTypeOK ) {
	exit("  warning types ... unknown error type!\n");
} else {
	print "  warning types ... ok (".implode(", ", $error_types).")\n";
}

// check max points
if ( strlen($max_points) > 0 ) {
	print "  max points ... ok (".$max_points.")\n";
} else {
	exit("  max points ... corrupt entry)");
}

// check syntax wight
if ( strlen($weight_syntax) > 0 ) {
	print "  weight syntax ... ok (".$weight_syntax.")\n";
} else {
	exit("  weight syntax ... corrupt entry)");
}

// check activity wight
if ( strlen($weight_functions) > 0 ) {
	print "  weight function recall ... ok (".$weight_functions.")\n";
} else {
	exit("  weight function recall ... corrupt entry)");
}

// check bahvioural f-measure wight
if ( strlen($weight_behaviour) > 0 ) {
	print "  weight behavioural f-measure ... ok (".$weight_behaviour.")\n";
} else {
	exit("  weight behavioural f-measure ... corrupt entry)");
}

// check max syntax error
if ( strlen($max_syntax_error) > 0 ) {
	print "  max syntax error ... ok (".$max_syntax_error.")\n";
} else {
	exit("  max syntax error ... corrupt entry)");
}

// Check metrics csv
$checkMetrics = false;
$metrics = null;
if ( $metrics_csv == "no" ) {
	print "  metrics CSV ... skipped\n";
} else {
	$checkMetrics = true;
	if ( file_exists($metrics_csv) ) {
		$metrics = new Metrics();
		$metrics->loadCSV($metrics_csv);
		print "  metrics CSV ... ok\n";
	} else {
		exit("  metrics CSV ... failed (file does not exist)");
	}
}

// Laden der Modelldateien
$content_file = file_get_contents($input);
$xml = new SimpleXMLElement($content_file);

// Vorbereitung der Forschrittsanzeige
$modelsInFile = count($xml->xpath("//epc"));
$studentSolutions = $modelsInFile-1;

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("\nNumber of student solutions: ".($modelsInFile-1)."\n\n");

// ReadMe.txt erzeugen
$readme = "--------------------------------------------------------------------------\r\n";
$readme .= " RMMaaS - Automatic Scoring of Modelling Exams\r\n";
$readme .= "--------------------------------------------------------------------------\r\n\r\n";
$readme .= "Log:\r\n";
$readme .= " - Model file:  ".$input." (".$modelsInFile." models)\r\n";
$readme .= " - Sample Solution:  ".$sampleSolution."	\r\n";
$readme .= " - Number of student solutions: ".$studentSolutions."\r\n\r\n";

print("Start analyzing student solutions ...\n\n");

/**
 * LOADING EXAM OBJECTS, SYNTAX CHECK
 */
print("  Checking syntax of student solutions ... \n\n");
$readme .= "Syntax Check\r\n\r\n";
$progressBar = new CLIProgressbar($studentSolutions, 0.1);
$modelCounter = 0;
$sampleSolutionID = null;
$sampleSolutionModelIndex = null;
$sampleSolutionEPC = null;

$examResults = array();

foreach ($xml->xpath("//epc") as $xml_epc) {
	if ( strtolower($xml_epc["name"]) == strtolower($sampleSolution) ) {
		$sampleSolutionID = $xml_epc["epcId"];
		$sampleSolutionModelIndex = $modelCounter;
		$sampleSolutionEPC = new EPC($xml, $xml_epc["epcId"], $xml_epc["name"]);
		$sampleSolutionEPC->autocorrectSpelling();
	}
	
	$epc = new EPC($xml, $xml_epc["epcId"], $xml_epc["name"]);
	
	if ( $checkMetrics ) $metrics->assignEPC($epc);
	
	$syntaxErrors = $epc->getSyntaxErrors($error_types, $warning_types);
	$examResults[$modelCounter] = new ExamResult($epc);
	$examResults[$modelCounter]->addSytaxResults($syntaxErrors);
	
	$errorTxtPart = null;
	$errorTxtPart = ("    ".$xml_epc["name"]." ... ");
	if ( empty($syntaxErrors) ) {
		$errorTxtPart .= "ok\r\n";
	} else {
		$errorTxtPart .= count($syntaxErrors)." issues found\r\n";
	}
	foreach ( $syntaxErrors as $key => $error ) {
		$errorTxtPart .= "      (".($key+1).") ".$error."\r\n";
	}
	
	print($errorTxtPart);
	$readme .= $errorTxtPart;
	
	$modelCounter++;
	$progressBar->run($modelCounter);
}

/**
 * CHECKING CHEATING ATTEMPTS
 */

print("\n\n  Check cheating attempts ... \n\n");
$readme .= "\r\n\r\nCheck for cheating attempts\r\n\r\n";
$progressBar = new CLIProgressbar($studentSolutions, 0.1);
$modelCounter = 0;

foreach ( $examResults as $modelIndex1 => $examResult1 ) {
	if ( $modelIndex1 == $sampleSolutionModelIndex ) continue;

	foreach ( $examResults as $modelIndex2 => $examResult2 ) {
		if ( $modelIndex2 == $sampleSolutionModelIndex || $modelIndex2 <= $modelIndex1 ) continue;

		$isCheated = ExamResult::checkCheatingAttempt($examResults[$modelIndex1]->epc, $examResults[$modelIndex2]->epc, 0.90);

		if ( $isCheated ) {
			$percentageOfCommonNodesAndEdges = round($isCheated, 2);
			array_push($examResults[$modelIndex1]->cheatingAttempts, $examResult2->epc->name." (".($percentageOfCommonNodesAndEdges*100)."% cloned)");
			array_push($examResults[$modelIndex2]->cheatingAttempts, $examResult1->epc->name." (".($percentageOfCommonNodesAndEdges*100)."% cloned)");
			$txtPart = null;
			$txtPart = "    Cheat attempt detected between ".$examResults[$modelIndex1]->epc->name." and ".$examResults[$modelIndex2]->epc->name." (POCNAE: ".$percentageOfCommonNodesAndEdges.")\r\n";
			print($txtPart);
			$readme .= $txtPart;
		}

		$modelCounter++;
		$progressBar->run($modelCounter);

	}
}

/**
 * AUTOCORRECT SPELLING
 */

print("\n\n  Autocorrect spelling ... \n\n");
$readme .= "\r\n\r\nAutocorrection of spelling\r\n\r\n";
$progressBar = new CLIProgressbar($studentSolutions, 0.1);
$modelCounter = 0;

foreach ( $examResults as $modelIndex => $examResult ) {
	$corrections = $examResults[$modelIndex]->epc->autocorrectSpelling();

	if ( !empty($corrections) ) {
		$txtPart = "  ".$examResults[$modelIndex]->epc->name."\r\n    ";
		$txtPart .= implode("    ", $corrections)."\r\n";
		print($txtPart);
		$readme .= $txtPart;
	}

	$modelCounter++;
	$progressBar->run($modelCounter);
}

/**
 * NODE MATCHING
 */

print("\n  Create node mappings (RMM-NHCM) ... \n\n");
$readme .= "\r\nUsed Matching Approach (RMM-NHCM)\r\n\r\n";

// Prepare cluster mapping
print("    Calculate node clusters ... ");
$naryMapping = new NAryWordstemMappingWithAntonyms2015();
foreach ($xml->xpath("//epc") as $xml_epc) {
	$epc = new EPC($xml, $xml_epc["epcId"], $xml_epc["name"]);
	$naryMapping->addEPC($epc);
}

$naryMapping->solveShortcuts();
$naryMapping->autocorrectSpelling();
$naryMapping->map2015(false);
print("done\n\n");

// extract binary mappings and proceed post processing
// Load persisted NLP data
print("    Derive binary mappings ... ");
$antonymCache = NLP::loadAntonymLabelsFromPersistedFile();
$nonAntonymCache = NLP::loadNonAntonymLabelsFromPersistedFile();
$corrspondencyCache = NLP::loadCorrespondentLabelsFromPersistedFile();
$nonCorrespondencyCache = NLP::loadNonCorrespondentLabelsFromPersistedFile();

$binaryMappings = array();
foreach ( $examResults as $modelIndex => $examResult ) {	
	if ( $modelIndex == $sampleSolutionModelIndex ) continue;
		//print("add mapping to sample solution (EX: ".$examResults[$modelIndex]->epc->name.", From: ".$naryMapping->epcs[$modelIndex]->name.", TO: ".$naryMapping->epcs[$sampleSolutionModelIndex]->name."\n");
		$examResults[$modelIndex]->mappingToSampleSolution = $naryMapping->extractBinaryMapping($naryMapping->epcs[$modelIndex], $naryMapping->epcs[$sampleSolutionModelIndex]);
		$examResults[$modelIndex]->mappingToSampleSolution->map("AllOne");
		$examResults[$modelIndex]->mappingToSampleSolution->deleteDummyTransitions();
		
		$examResults[$modelIndex]->mappingToSampleSolution->removeAntonymMaps($antonymCache, $nonAntonymCache);
		$examResults[$modelIndex]->mappingToSampleSolution->removeMissingVerbObjectCorrespondentMaps($corrspondencyCache, $nonCorrespondencyCache);
		
		if ( $modelsInFile == 2 && $naryMapping->harmonizationDegree == 1 ) {
			$examResults[$modelIndex]->mappingToSampleSolution->removeMapsCausedByHarmonizedModel();
		}
		
		$examResults[$modelIndex]->mappingToSampleSolution->removeMatchesForWhichAnAdversingIdentyMatchIsAvailable();
		$examResults[$modelIndex]->mappingToSampleSolution->improveComplexMatchesBasedOnContext();
		
		if ( $naryMapping->harmonizationDegree >= ($naryMapping->threshold_ontology_quote/100) ) {
			$examResults[$modelIndex]->mappingToSampleSolution->improveComplexMatchesCausedByHarmonizedModels();
		}
		
		print(".");
}
print("done\n\n");

/**
 * CHECK RECALL OF ACTIVITIES WITH REGARD TO SAMPLE SOLUTION
 */

print("  Checking recall of activities in sample solution ... \n\n");
$readme .= "Recall of activities in sample solution\r\n\r\n";
$sampleSolutionActionsString = "    The following functions should be available: \r\n    - \"".implode("\"\r\n    - \"", $sampleSolutionEPC->functions)."\"\r\n\r\n";
//print($sampleSolutionActionsString);
$readme .= $sampleSolutionActionsString;
$progressBar = new CLIProgressbar($studentSolutions, 0.1);
$modelCounter = 0;
$numDifferentFuncsInSampleSolution = $sampleSolutionEPC->getNumDifferentFunctionLabels();

foreach ( $examResults as $modelIndex => $examResult ) {
	if ( $modelIndex == $sampleSolutionModelIndex ) continue;
	$txtPart = null;
	$txtPart = ("    ".$examResults[$modelIndex]->epc->name." ... ");
	$recall = $examResults[$modelIndex]->getRecallOfFunctionNodes($numDifferentFuncsInSampleSolution);
	
	$txtPart .= $recall." (TP=".$examResult->truePositives.", FN=".$examResult->falseNegatives.", FP=".$examResult->falsePositives.")\r\n";
	if ( $examResult->falsePositives > 0 ) $txtPart .= "      False Positives: \"".implode("\", \"", $examResult->falsePositivesLabels)."\"\r\n";
	print($txtPart);
	$readme .= $txtPart;
	
	$modelCounter++;
	$progressBar->run($modelCounter);
}



// print("\n  Structural similarity to sample solution (Causal Footprints) ... \n\n");
// $readme .= "\r\n\r\nStructural Similarity based on Causal Footprints\r\n\r\n";
// $progressBar = new CLIProgressbar($studentSolutions, 0.1);
// $modelCounter = 0;

// foreach ( $examResults as $modelIndex => $examResult ) {
// 	if ( $modelIndex == $sampleSolutionModelIndex ) continue;
// 	$footprints = new CausalFootprints($examResults[$modelIndex]->mappingToSampleSolution);
// 	$footprintSimilarity = $footprints->calculate()/100;
	
// 	$txtPart = null;
// 	$txtPart = "    ".$examResults[$modelIndex]->epc->name.": ".$footprintSimilarity."\r\n";
// 	print($txtPart);
// 	$readme .= $txtPart;

// 	$modelCounter++;
// 	$progressBar->run($modelCounter);
// }


if ( $weight_behaviour >= 1 ) {
	/**
	 * BEHAVIORAL SPACE SIMILARITY BASED ON ALL POSSIBLE TRACES
	 */
	
	print("\n  Trace similarity to sample solution ... \n\n");
	$readme .= "\r\n\r\nSimilarity based on possible execution traces\r\n\r\n";
	$progressBar = new CLIProgressbar($studentSolutions, 0.1);
	$modelCounter = 0;
	
	// Traces for Sample Solution
	$traceExtractor = new TraceExtractor($sampleSolutionEPC, false, Config::MAX_TIME_PER_TRACE_EXTRAKTION);
	$traces = $traceExtractor->execute();
	$sampleSolutionTraces = array();
	
	foreach ( $traces as $traceIndex => $trace ) {
		$updatedTrace = array();
		foreach ( $trace as $entryIndex => $entryID ) {
			array_push($updatedTrace, $entryID);
		}
		if ( !in_array($updatedTrace, $sampleSolutionTraces) ) array_push($sampleSolutionTraces, $updatedTrace);
	}
	
	// print("\n\n  Traces for sample solution: ".$sampleSolutionEPC->name.": \n");
	// foreach ( $sampleSolutionTraces as $index => $trace ) {
	// 	$traceString = implode("|", $trace);
	// 	print("   ".$traceString."\n");
	// }
	
	// compare function for sorting an array of traces by trace lengths
	function traceLenCompare($trace1, $trace2) {
		$len1 = count($trace1);
		$len2 = count($trace2);
		if ( $len1 == $len2 ) return 0;
		return ( $len1 < $len2 ) ? -1 : 1;
	}
	
	foreach ( $examResults as $modelIndex => $examResult ) {
		if ( $modelIndex == $sampleSolutionModelIndex ) continue;
		
		$currEPC = $examResult->epc;
	 	$traceExtractor = new TraceExtractor($currEPC, false, Config::MAX_TIME_PER_TRACE_EXTRAKTION);
	 	$traces = $traceExtractor->execute();
	 	$currEPC->traces = $traces;
	 	$currEPC->assignFunctionMapping2($examResult->mappingToSampleSolution);
	 	$traces = $currEPC->traces;
	
		$updatedTraces = array();
	 	
	 	foreach ( $traces as $traceIndex => $trace ) {
	 		$updatedTrace = array();
	 		foreach ( $trace as $entryIndex => $entryID ) {
	 			if ( isset($sampleSolutionEPC->functions[$entryID]) ) {
	 				array_push($updatedTrace, $entryID);
	 			}
	 		}
	 		if ( !in_array($updatedTrace, $updatedTraces) ) array_push($updatedTraces, $updatedTrace);
	 	}
	 	$traces = $updatedTraces;
	 	$examResults[$modelIndex]->traces = $traces;
	 	 
	//  	print("\n\n  Traces for ".$examResult->epc->name.": \n");
	//  	foreach ( $traces as $index => $trace ) {
	//  		$traceString = implode("|", $trace);
	//  		print("   ".$traceString."\n");
	//  	}
	 	
	 	// True Positives
	 	$truePositives = array();
	 	foreach ( $traces as $traceIndex => $trace ) {
	 		if ( in_array($trace, $sampleSolutionTraces) ) array_push($truePositives, $trace);
	 	} 
	 	$truePositiveVal = count($truePositives); 
	 	
	 	// False Positives
	 	$falsePositives = array();
	 	foreach ( $traces as $traceIndex => $trace ) {
	 		if ( !in_array($trace, $truePositives) ) array_push($falsePositives, $trace);
	 	}
	 	$falsePositiveVal = count($falsePositives);
	 	
	 	// False Negatives
	 	$falseNegatives = array();
	 	foreach ( $sampleSolutionTraces as $trace ) {
	 		if ( !in_array($trace, $truePositives) ) array_push($falseNegatives, $trace);
	 	}
	 	$falseNegativeVal = count($falseNegatives);
	 	
	 	// partially true positives
	 	//Record longest common subsequences
	 	$longest_common_subsequences = array();
	 	foreach ( $falsePositives as $fpTraceIndex => $fpTrace ) {
			$longest_common_subsequence = array();
	 		foreach ( $falseNegatives as $fnTraceIndex => $fnTrace ) {
	 			$lcs = LongestCommonSubsequenceOfTraces::longestCommonSubsequence($fpTrace, $fnTrace);
	 			$lcs_len = count($lcs);
	 			$currLen = count($longest_common_subsequence);
	 			// a (sub)sequence is defined as having at least two elements
	 			if ( $lcs_len > $currLen && $lcs_len > 1 ) $longest_common_subsequence = $lcs;
	 		}
	 		if ( !in_array($longest_common_subsequence, $longest_common_subsequences) ) array_push($longest_common_subsequences, $longest_common_subsequence);
	 	}
	 	
	 	// sort lcs by trace length
	 	usort($longest_common_subsequences, "traceLenCompare");
	 	
	 	// update true and false positive values
	 	$falseNegativesTmp = $falseNegatives;
	 	foreach ( $longest_common_subsequences as $lcsIndex => $subsequence ) {
	 		foreach ( $falseNegativesTmp as $fnTmpIndex => $fnTmpTrace ) {
	 			$lcs = LongestCommonSubsequenceOfTraces::longestCommonSubsequence($subsequence, $fnTmpTrace);
	 			if ( $lcs == $subsequence ) {
	 				unset($falseNegativesTmp[$fnTmpIndex]);
	 				$lcsLen = count($subsequence);
	 				$fnLen = count($fnTmpTrace);
	 				$truePositiveAddition = $lcs_len / $fnLen;
	 				$falsePositiveSubstraction = $truePositiveAddition;
	 				$falseNegativeSubstraction = $truePositiveAddition;
	 				$truePositiveVal += $truePositiveAddition;
	 				$falsePositiveVal -= $falsePositiveSubstraction;
	 				$falseNegativeVal -= $falseNegativeSubstraction;
	 				break;
	 			}
	 		}
	 	} 
	 	
	 	
	 	// Precision, Recall, F-Measure
	 	$precision = round(($truePositiveVal / ($truePositiveVal+$falsePositiveVal)), 2);
	 	$recall = round(($truePositiveVal / ($truePositiveVal+$falseNegativeVal)), 2);
	 	$fmeasure = ($precision == 0 || $recall == 0) ? 0 : round(((2*$precision*$recall) / ($precision+$recall)), 2);
	 	
	 	$examResults[$modelIndex]->tracePrecision = $precision;
	 	$examResults[$modelIndex]->traceRecall = $recall;
	 	$examResults[$modelIndex]->traceFMeasure = $fmeasure;
	 	
	// 	print("\n   Precision: ".$precision.", Recall: ".$recall.", F-Measure: ".$fmeasure."\n");
	
		$txtPart = null;
	 	$txtPart = "    ".$examResults[$modelIndex]->epc->name." - Precision: ".$precision.", Recall: ".$recall.", F-Measure: ".$fmeasure."\r\n";
	 	print($txtPart);
	 	$readme .= $txtPart;
	
		$modelCounter++;
		$progressBar->run($modelCounter);
	}
} else {
	foreach ( $examResults as $modelIndex => $examResult ) {
		if ( $modelIndex == $sampleSolutionModelIndex ) continue;
		$examResults[$modelIndex]->tracePrecision = "-";
		$examResults[$modelIndex]->traceRecall = "-";
		$examResults[$modelIndex]->traceFMeasure = "-";
	}
}

print("\n\nfinished");

/**
 * REPORT
 */
print("\n\ncreating report ... ");

$report = "";
// Parameters

$report .= "General Parameters\r\n\r\n";

$report .= "Input file;".$input."\r\n";
$report .= "Sample Solution;".$sampleSolution."\r\n";
$report .= "Output file;".$output."\r\n";
$report .= "Notification E-Mail;".$email."\r\n";

$report .= "\r\nConfiguration Parameters\r\n\r\n";
$report .= "Error types;".implode(", ", $error_types)."\r\n";
$report .= "Warning types;".implode(", ", $warning_types)."\r\n";
$report .= "Max. points;".$max_points."\r\n";
$report .= "Weight of syntax;".$weight_syntax."\r\n";
$report .= "Weight of functioin recall;".$weight_functions."\r\n";
$report .= "Weight of behavioural f-measure;".$weight_behaviour."\r\n";
$report .= "Max. syntax errors;".$max_syntax_error."\r\n";
$report .= "Metrics;".$metrics_csv."\r\n";

// Numeric Results

$report .= "\r\n\r\nResults\r\n\r\n";

// Header
foreach ( $examResults as $examResult ) $report .= ";".$examResult->epc->name;

// syntax errors
$report .= "\r\n#Syntax Errors";
foreach ( $examResults as $examResult ) $report .= ";".count($examResult->syntaxErrors);
$report .= "\r\n#Syntax Warnings";
foreach ( $examResults as $examResult ) $report .= ";".count($examResult->syntaxWarnings);

// activity recall
$report .= "\r\nActivity Recall";
foreach ( $examResults as $examResult ) $report .= ";".str_replace(".", ",", $examResult->activityRecall);

// trace correspondency
$report .= "\r\nBehavioral Precision";
foreach ( $examResults as $examResult ) $report .= ";".str_replace(".", ",", $examResult->tracePrecision);
$report .= "\r\nBehavioral Recall";
foreach ( $examResults as $examResult ) $report .= ";".str_replace(".", ",", $examResult->traceRecall);
$report .= "\r\nBehavioral F-Measure";
foreach ( $examResults as $examResult ) $report .= ";".str_replace(".", ",", $examResult->traceFMeasure);

// cheat attempt
$report .= "\r\nCheat attempt";
foreach ( $examResults as $examResult ) $report .= (count($examResult->cheatingAttempts) == 0) ? ";no" : ";yes";

// Overall result
$report .= "\r\nOVERALL SCORE";
foreach ( $examResults as $modelIndex => $examResult ) {
	$score = $examResults[$modelIndex]->score($max_points, $max_syntax_error, $weight_syntax, $weight_functions, $weight_behaviour);
	if ( $modelIndex == $sampleSolutionModelIndex ) {
		$report .= ";";
	} else {
		$report .= ";".str_replace(".", ",", $score);
	}
	
}

// Detailed results
$report .= "\r\n\r\nDetailed results\r\n";

// Header
foreach ( $examResults as $examResult ) $report .= ";".$examResult->epc->name;
		
// syntax errors
$report .= "\r\n#Syntax Errors";
foreach ( $examResults as $examResult ) {
	$report .= ";\"";
	foreach ( $examResult->syntaxErrors as $error ) {
		$report .= substr(str_replace("\"", "'", $error), 7)."\n";
	}
	$report .= "\"";
}
$report .= "\r\n#Syntax Warnings";
foreach ( $examResults as $examResult ) {
	$report .= ";\"";
	foreach ( $examResult->syntaxWarnings as $warning ) {
		$report .= substr(str_replace("\"", "'", $warning), 9)."\n";
	}
	$report .= "\"";
}

// spelling corrections
$report .= "\r\nSpelling corrections";
foreach ( $examResults as $examResult )	$report .= ";\"".implode("\n", $examResult->epc->spellingCorrections)."\"";

// activity recall
$report .= "\r\nUnassigned activities";
foreach ( $examResults as $examResult )	$report .= ";\"".implode("\", \"", $examResult->falsePositivesLabels)."\""; 
$report .= "\r\nMissing activities";
foreach ( $examResults as $examResult )	$report .= ";\"".implode("\", \"", $examResult->falseNegativesLabels)."\"";

// activity recall
$report .= "\r\nCheat attempts";
foreach ( $examResults as $examResult )	$report .= ";\"".implode("\", \"", $examResult->cheatingAttempts)."\"";

// CHECK UNDERSTANDABILITY
if ( $checkMetrics ) {
	$report .= "\r\n\r\nModel understandability\r\n\r\nRemark: Effects on the understandability in comparison to the sample solution are presented in the brackets.\r\n\r\n";
	
	$metrics->nodes_after_splits();
	$metrics->gateway_heterogeneity();
	$metrics->connector_connected_nodes();
	$metrics->ors();
	$metrics->start_end_events();
	
	// Header
	foreach ( $examResults as $examResult ) $report .= ";".$examResult->epc->name;
	
	// SF22
	$report .= "\r\n#arcs";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["arcs"]);
		if ( $metrics->values[$examResult->epc->name]["arcs"] > $metrics->values[$sampleSolution]["arcs"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["arcs"] < $metrics->values[$sampleSolution]["arcs"] ) $report .= " (+)";
	}
	
	// SF22
	$report .= "\r\ndensity";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["density_1"]);
		if ( $metrics->values[$examResult->epc->name]["density_1"] > $metrics->values[$sampleSolution]["density_1"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["density_1"] < $metrics->values[$sampleSolution]["density_1"] ) $report .= " (+)";
	}
	
	// SF23
	$report .= "\r\nsequentiality";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["sequentiality"]);
		if ( $metrics->values[$examResult->epc->name]["sequentiality"] > $metrics->values[$sampleSolution]["sequentiality"] ) $report .= " (+)";
		if ( $metrics->values[$examResult->epc->name]["sequentiality"] < $metrics->values[$sampleSolution]["sequentiality"] ) $report .= " (-)";
	}
	
	// SF24
	$report .= "\r\naverage connector degree";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["avg_connector_degree"]);
		if ( $metrics->values[$examResult->epc->name]["sequentiality"] > $metrics->values[$sampleSolution]["sequentiality"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["sequentiality"] < $metrics->values[$sampleSolution]["sequentiality"] ) $report .= " (+)";
	}
	
	// SF25
	$report .= "\r\ndepth";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["depth"]);
		if ( $metrics->values[$examResult->epc->name]["depth"] > $metrics->values[$sampleSolution]["depth"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["depth"] < $metrics->values[$sampleSolution]["depth"] ) $report .= " (+)";
	}
	
	// SF26
	$report .= "\r\n#nodes after split connectors";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["nodes_after_splits"]);
		if ( $metrics->values[$examResult->epc->name]["nodes_after_splits"] > $metrics->values[$sampleSolution]["nodes_after_splits"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["nodes_after_splits"] < $metrics->values[$sampleSolution]["nodes_after_splits"] ) $report .= " (+)";
	}
	
	// SF27
	$report .= "\r\n#connectors";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["connectors"]);
		if ( $metrics->values[$examResult->epc->name]["connectors"] > $metrics->values[$sampleSolution]["connectors"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["connectors"] < $metrics->values[$sampleSolution]["connectors"] ) $report .= " (+)";
	}
	
	// SF28
	$report .= "\r\n#OR connectors";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["ors"]);
		if ( $metrics->values[$examResult->epc->name]["ors"] > $metrics->values[$sampleSolution]["ors"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["ors"] < $metrics->values[$sampleSolution]["ors"] ) $report .= " (+)";
	}
	
	// SF29
	$report .= "\r\ngateway heterogeneity";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["gateway_heterogeneity"]);
		if ( $metrics->values[$examResult->epc->name]["gateway_heterogeneity"] > $metrics->values[$sampleSolution]["gateway_heterogeneity"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["gateway_heterogeneity"] < $metrics->values[$sampleSolution]["gateway_heterogeneity"] ) $report .= " (+)";
	}
	
	// SF30
	$report .= "\r\ncontrol flow complexity";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["control_flow_complexity"]);
		if ( $metrics->values[$examResult->epc->name]["control_flow_complexity"] > $metrics->values[$sampleSolution]["control_flow_complexity"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["control_flow_complexity"] < $metrics->values[$sampleSolution]["control_flow_complexity"] ) $report .= " (+)";
	}
	
	// SF31
	$report .= "\r\ndiameter";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["diameter"]);
		if ( $metrics->values[$examResult->epc->name]["diameter"] > $metrics->values[$sampleSolution]["diameter"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["diameter"] < $metrics->values[$sampleSolution]["diameter"] ) $report .= " (+)";
	}

	// GS9
	$report .= "\r\n#connector connected nodes";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["connector_connected_nodes"]);
		if ( $metrics->values[$examResult->epc->name]["connector_connected_nodes"] > $metrics->values[$sampleSolution]["connector_connected_nodes"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["connector_connected_nodes"] < $metrics->values[$sampleSolution]["connector_connected_nodes"] ) $report .= " (+)";
	}
	
	// GS11
	$report .= "\r\ncross connectivity";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["cross_connectivity"]);
		if ( $metrics->values[$examResult->epc->name]["cross_connectivity"] > $metrics->values[$sampleSolution]["cross_connectivity"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["cross_connectivity"] < $metrics->values[$sampleSolution]["cross_connectivity"] ) $report .= " (+)";
	}
	
	// GS39
	$report .= "\r\ncoefficient of connectivity";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["coefficient_of_connectivity"]);
		if ( $metrics->values[$examResult->epc->name]["coefficient_of_connectivity"] > $metrics->values[$sampleSolution]["coefficient_of_connectivity"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["coefficient_of_connectivity"] < $metrics->values[$sampleSolution]["coefficient_of_connectivity"] ) $report .= " (+)";
	}
	
	// SF44
	$report .= "\r\nstart and end events";
	foreach ( $examResults as $examResult ) {
		$report .= ";".str_replace(".", ",", $metrics->values[$examResult->epc->name]["start_end_events"]);
		if ( $metrics->values[$examResult->epc->name]["start_end_events"] > $metrics->values[$sampleSolution]["start_end_events"] ) $report .= " (-)";
		if ( $metrics->values[$examResult->epc->name]["start_end_events"] < $metrics->values[$sampleSolution]["start_end_events"] ) $report .= " (+)";
	}
}

print("done");

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme .= "Duration: ".$minutes." Min. ".$seconds." Sec.\r\n\r\n";

// ERSTELLEN DER AUSGABEDATEIEN
//$fileGenerator = new FileGenerator($output, $readme);
$fileGenerator = new FileGenerator($output, $report);
$fileGenerator->setPathFilename($output);
//$fileGenerator->setContent($readme);
$fileGenerator->setContent($report);
$uri_readme = $fileGenerator->execute(false);
// AUSGABEDATEIEN ERSTELLT

// Extract session ID from uri
$sid = $uri_readme;
$sid = str_replace("workspace/", "", $sid);
$pos = strpos($sid, "/");
$sid = $pos ? substr($sid, 0, $pos) : $sid;
$readme .= "Your workspace: ".Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLIModelSimilarityNotification($email, $readme);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n");
print("Result file successfully created (".$output.").\n\n");

Logger::log($email, "CLICheckModelExams finished: input=".$input." sample_solution=".$sampleSolution." output=".$output, "ACCESS");
?>