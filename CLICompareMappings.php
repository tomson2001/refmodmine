<?php
/**
 * Compares RDF (format of contest 2015) by their correspondences
 */
$start = time ();
require 'autoloader.php';

print ("\n-------------------------------------------------\n RefModMining - Compare Mappings \n-------------------------------------------------\n\n") ;

// Hilfeanzeige auf Kommandozeile
if (! isset ( $argv [1] ) || ! isset ( $argv [2] ) || ! isset ( $argv [3] )  || ! isset ( $argv [4] )) {
	exit ( "   Please provide the following parameters:\n
   epml=            path to the epml containing the models
   mappings_path=   path to rdf mappings; therin one folder for each matcher containined the corresponding rdf-mappings is expected
   output=          path / filename of the result csv
   output_stats=    path / filename of stats csv

   please user the correct order!
			
   ERROR: Parameters incomplete
" );
}

$goldStandardSearchPatterns = array("gold standard", "reference matching", "reference mapping");

// Checking Parameters
$epml = substr ( $argv [1], 5, strlen ( $argv [1] ) );
$mappings_path = substr ( $argv [2], 14, strlen ( $argv [2] ) );
$output = substr ( $argv [3], 7, strlen ( $argv [3] ) );
$output_stats = substr ( $argv [4], 13, strlen ( $argv [4] ) );

print ("
epml: " . $epml . "
mappings_path: " . $mappings_path . "
output: " . $output . "
output_stats: " . $output_stats . "

checking input parameters ...
") ;

// Check input
if (file_exists ( $epml )) {
	print "  epml ... ok\n";
} else {
	exit ( "  epml ... failed (file does not exist)\n\n" );
}

// Check mapping_path
if (is_dir ( $mappings_path )) {
	print "  mapping directory ... ok\n";
} else {
	exit ( "  mapping directory ... failed (path does not exist)\n\n" );
}

$params = array();
$params[] = "Model file;".$epml;
$params[] = "Mappings path;".$mappings_path;
$params[] = "Result CSV;".$output;
$params[] = "Stats CSV;".$output_stats;

// Verarbeitung der Modelldatei
$content_file1 = file_get_contents ( $epml );
$xml1 = new SimpleXMLElement ( $content_file1 );
$modelsInFile1 = count ( $xml1->xpath ( "//epc" ) );

$params[] = "Num models;".$modelsInFile1;

// print infos to console
print ("\nLoad EPCs ...") ;

$epcs = array ();

// Load EPCs
foreach ( $xml1->xpath ( "//epc" ) as $xml_epc ) {
	$epcID = isset ( $xml_epc ["epcId"] ) ? ( string ) $xml_epc ["epcId"] : ( string ) $xml_epc ["EpcId"];
	$epc = new EPC ( $xml1, $epcID, $xml_epc ["name"] );
	$epc->name = str_replace ( " ", "", str_replace ( ":", "", $epc->name ) );
	array_push($epcs, $epc);
	
	// print($epc->name."\n");
}

print (" done (" . $modelsInFile1 . ")\n") ;

function getAvailableMatcher($dirPath) {
	$matcher = array();
	$files = scandir ( $dirPath );
	foreach ( $files as $filename ) {
		$path = $dirPath . DIRECTORY_SEPARATOR . $filename;
		if ( is_dir($path) && $filename != "." && $filename != ".." ) {
			$matcherName = $filename;
			if ( substr_count(strtolower($filename), "mappings_") ) {
				$rpos = strrpos($filename, "_");
				$matcherName = substr($filename, $rpos+1);
			}			
			$matcher[$matcherName] = $path;
		}
	}
	return $matcher;
}

print ("\nDetecting Matchers ...\n") ;

$matchersNames = array();
$goldStandardMatcherName = null;
$matchers = getAvailableMatcher($mappings_path);
foreach ( $matchers as $matcherName => $path ) {
	
	// check if is gold standard
	foreach ( $goldStandardSearchPatterns as $pattern ) {
		$patternOptions = array($pattern);
		$patternOptions[] = str_replace(" ", "_", $pattern);
		$patternOptions[] = str_replace(" ", "-", $pattern);
		$patternOptions[] = str_replace(" ", "", $pattern);
		
		foreach ( $patternOptions as $currPattern ) {
			if ( substr_count(strtolower($matcherName), $currPattern) ) $goldStandardMatcherName = $matcherName;
		}
	}
	
	if ( !is_null($goldStandardMatcherName) && $goldStandardMatcherName == $matcherName ) {
		print("  - ".$matcherName." (".$path.") ==> Reference Matching!\n");
		$params[] = "Gold Standard;".$goldStandardMatcherName;
	} else {
		print("  - ".$matcherName." (".$path.")\n");
	}
	
	array_push($matchersNames, $matcherName);
}
$numMatchers = count($matchers);

print ("done (".$numMatchers." matchers detected)\n\n") ;

$params[] = "Detected matchers;".$numMatchers." (".implode(", ", $matchersNames).")";

function getRDFsInDir($dirPath) {
	$rdfs = array ();
	$files = scandir ( $dirPath );
	foreach ( $files as $filename ) {
		$path = $dirPath . DIRECTORY_SEPARATOR . $filename;
		if (substr ( $filename, - 4 ) == ".rdf") {
			array_push ( $rdfs, $path );
		}
	}
	return $rdfs;
}

print ("Detecting Mapping files ... \n") ;

$matchersMappingfiles = array();
$overallNumRDFs = 0;
foreach ( $matchers as $matcherName => $path ) {
	$rdfs = getRDFsInDir($path);
	$matchersMappingfiles[$matcherName] = $rdfs;
	$numRDFs = count($rdfs);
	$overallNumRDFs += $numRDFs;
	print("  - ".$matcherName.": ".$numRDFs." mapping files (rdf)\n");
}

print ("done (".$overallNumRDFs." rdfs detected)\n\n") ;

$params[] = "Detected mapping files;".$overallNumRDFs;

print("Compare matches ... \n");

function containsMap($container, $map) {
	foreach ( $container as $currMap ) {
		if ( GenericMapping::equalsMap($map, $currMap, false) ) return false;
	}
	return false;
}

$infos = array();
$availableMatches = array();

$targetCombinations = 0;
$notComparableCombinations = 0;
$removedMapsContainingDummyTransitions = 0;

$goldStandardMapping = null;

$maxModelCombinations = (($modelsInFile1 * $modelsInFile1)/2)-($modelsInFile1/2);
$progressBar = new CLIProgressbar($maxModelCombinations, 0.1);
$modelCounter = 0;

for ( $i=0; $i<$modelsInFile1; $i++ ) {
	for ( $j=$i+1; $j<$modelsInFile1; $j++ ) {
		
		if ( $j <= $i ) continue;
		
		$rdfNameOption1 = $epcs[$i]->name."-".$epcs[$j]->name.".rdf";
		$rdfNameOption2 = $epcs[$j]->name."-".$epcs[$i]->name.".rdf";
		
		$fileAvailableForAllMatchers = true;
		$missingMatchers = array();
		
		// check existency of model combination for all matchers
		foreach ( $matchers as $matcherName => $matcherPath ) {
			if ( !(file_exists($matcherPath . DIRECTORY_SEPARATOR . $rdfNameOption1) 
			    || file_exists($matcherPath . DIRECTORY_SEPARATOR . $rdfNameOption2)) ) {
			    $fileAvailableForAllMatchers = false;
			    print($matcherPath . DIRECTORY_SEPARATOR . $rdfNameOption1."\n");
			    array_push($missingMatchers, $matcherName);
			}
		}
		
		if ( $fileAvailableForAllMatchers ) {
			
			//print("   Analyze model combination ".$epcs[$i]->name." | ".$epcs[$j]->name."\n");
			
			$modelCombination = $epcs[$i]->name."-|-".$epcs[$j]->name;
			
			$mappingsForModelCombination = array();
			
			// load mappings
			foreach ( $matchers as $matcherName => $matcherPath ) {
				$mappingFile = file_exists($matcherPath . DIRECTORY_SEPARATOR . $rdfNameOption1) ? $matcherPath . DIRECTORY_SEPARATOR . $rdfNameOption1 : $matcherPath . DIRECTORY_SEPARATOR . $rdfNameOption2; 
				$mapping = new GenericMapping();
				$mapping->loadRDF_BPMContest2015($mappingFile);
				
// 				var_dump($modelCombination);
// 				var_dump($mapping);
				
 				foreach ( $mapping->models as $modelName ) {
 					$modelName = str_replace ( " ", "", str_replace ( ":", "", $modelName ) );
 					$assigned = false;
 					foreach ( $epcs as $epc ) {
 						if ( $epc->name == $modelName ) $mapping->assignEPC($epc);
 						$assigned = true;
 					}
 				}
				
				$mapping->removeAllButFunctionMappings();
				$mappingsForModelCombination[$matcherName] = $mapping;
				if ( $matcherName == $goldStandardMatcherName ) $goldStandardMapping = $mapping;
			}
			
			//print("   ... mappings loaded\n");
			
			// detect distinct matches
			$foundMatches = array();
			foreach ( $mappingsForModelCombination as $matcherName => $mapping ) {
				foreach ( $mapping->maps as $map ) {
					if ( !containsMap($foundMatches, $map) ) array_push($foundMatches, $map);
				}
			}
			//print("   ... distinct maps detected\n");
			
			// check which maps are in which mappings
			foreach ( $foundMatches as $map ) {
				$matchVector = array();
				foreach ( $mappingsForModelCombination as $matcherName => $mapping ) {
					$matchVector[$matcherName] = $mapping->containsMap($map) ? 1 : 0;
				}
				
				if ( !empty($matchVector) ) {
					if ( !isset($availableMatches[$modelCombination]) ) $availableMatches[$modelCombination] = array();
					$mapString = GenericMapping::convertMapToString($map, "NODE_LABELS", array($epcs[$i], $epcs[$j]));
					if ( $mapString === false ) continue;
					
					// remove dummy transitions
					$labels = explode("}{", $mapString);
					$labels[0] = str_replace("{", "", $labels[0]);
					$labels[1] = str_replace("}", "", $labels[1]);
					if ( preg_match("/^t[0-9]*$/", $labels[0]) || preg_match("/^t[0-9]*$/", $labels[1]) ) {
						$removedMapsContainingDummyTransitions++;
						continue;
					}
					
					// replace semicolons by pipes, sincs they lead to errors in the resulting csv
					$mapString = str_replace(";", "|", $mapString);
					
					$availableMatches[$modelCombination][$mapString] = $matchVector;
				}
			}
			//print("   ... assignments analyzed\n");
			
		} else {
			$notComparableCombinations++;
			$info = "Model combination ".$epcs[$i]->name."-".$epcs[$j]->name." skipped: Missing RDF mapping for ".implode(", ", $missingMatchers);
			array_push($infos, $info);
			print("  ".$info."\n");
		}
		
		$targetCombinations++;
		$modelCounter++;
		$progressBar->run($modelCounter);
	}
}

$comparableCombinations = $targetCombinations - $notComparableCombinations;
print("\ndone (comparable matchings: ".$comparableCombinations." of ".$targetCombinations." | ".round(($comparableCombinations/$targetCombinations)*100)." %)\n");

print ("\nGenerate Analysis CSV ... ") ;

// write to CSV
$numAgreements = array();
$numDistinctMaps = 0;

$csv = "model combination;map";
foreach ( $matchers as $matcherName => $matcherPath ) {
	if ( $matcherName != $goldStandardMatcherName )
	$csv .= ";".$matcherName;
}
$csv .= ";__sum__";
if ( isset($goldStandardMatcherName) ) $csv .= ";Gold Standard";
$csv .= "\r\n";
foreach ( $availableMatches as $modelCombination => $matchesVectors ) {
	foreach ( $matchesVectors as $mapString => $matchVector ) {
		$csv .= $modelCombination.";".$mapString;
		$sum = 0;
		foreach ( $matchers as $matcherName => $matcherPath ) {
			if ( $matcherName != $goldStandardMatcherName ) {
				$csv .= ";".$matchVector[$matcherName];
				if ( $matchVector[$matcherName] === 1 ) $sum++;
			}
		}
		$numDistinctMaps++;
		if ( !isset($numAgreements[$sum]) ) $numAgreements[$sum] = 0;
		$numAgreements[$sum]++;
		$csv .= ";".$sum;
		if ( isset($goldStandardMatcherName) ) $csv .= ";".$matchVector[$goldStandardMatcherName];
		$csv .= "\r\n";
	}
}

$fileGenerator = new FileGenerator($output, $csv);
$fileGenerator->setPathFilename($output);
$fileGenerator->setContent($csv);
$uri_readme = $fileGenerator->execute(false);

print ("done\n");

print ("\nCalculate Stats CSV ... ") ;

$statsCSV = "General information\r\n";
$statsCSV .= implode("\r\n", $params);

$statsCSV .= "\r\n\r\nSome information on the analysis:\r\n";
$statsCSV .= implode("\r\n", $infos);

print("\n  Num distinct maps: ".$numDistinctMaps."\n");
$statsCSV .= "\r\n\r\nNum distinct maps;".$numDistinctMaps."\r\n"; 

print("  Removed maps containing dummy transitions: ".$removedMapsContainingDummyTransitions."\n");
$statsCSV .= "Removed maps containing dummy transitions;".$removedMapsContainingDummyTransitions."\r\n";

print("\n  Agreements Overview (Agreements => Occurences (Percentage)):\n");
$statsCSV .= "\r\nAgreements Overview\r\nAgreements;Occurences;Percentage\r\n";
ksort($numAgreements);
foreach ( $numAgreements as $numAgreements => $occurences ) {
	print("    ".$numAgreements." => ".$occurences." (".round(($occurences/$numDistinctMaps)*100).")\n");
	$statsCSV .= $numAgreements.";".$occurences.";".str_replace(".", ",", round((($occurences/$numDistinctMaps)*100), 2))."\r\n";
}

// count true positives, false positives and false negatives
$statValuesForMatchers = array();
$emptyStats = array("TP" => 0, "FP" => 0, "FN" => 0);
if ( isset($goldStandardMatcherName) ) {
	foreach ( $matchers as $matcherName => $path ) {
		//if ( $matcherName != $goldStandardMatcherName ) {
			$statValuesForMatchers[$matcherName] = array("_micro_stats" => $emptyStats);
			foreach ( $availableMatches as $modelCombination => $matchesVectors ) {
				$statValuesForMatchers[$matcherName][$modelCombination] = $emptyStats;
				foreach ( $matchesVectors as $mapString => $matchVector ) {
					$tp_addition = ($matchVector[$matcherName] == 1 && $matchVector[$goldStandardMatcherName] == 1) ? 1 : 0;
					$fp_addition = ($matchVector[$matcherName] == 1 && $matchVector[$goldStandardMatcherName] == 0) ? 1 : 0;
					$fn_addition = ($matchVector[$matcherName] == 0 && $matchVector[$goldStandardMatcherName] == 0) ? 1 : 0;
					$statValuesForMatchers[$matcherName][$modelCombination]["TP"] += $tp_addition;
					$statValuesForMatchers[$matcherName][$modelCombination]["FP"] += $fp_addition;
					$statValuesForMatchers[$matcherName][$modelCombination]["FN"] += $fn_addition;
					$statValuesForMatchers[$matcherName]["_micro_stats"]["TP"] += $tp_addition;
					$statValuesForMatchers[$matcherName]["_micro_stats"]["FP"] += $fp_addition;
					$statValuesForMatchers[$matcherName]["_micro_stats"]["FN"] += $fn_addition;
				}
			}
		//}
	}
}

// Calculate micro-stats Precision / Recall / F-Measure
foreach ( $statValuesForMatchers as $matcherName => $dataStats ) {
	foreach ( $dataStats as $dataName => $stats ) {
		$precisionIf0 = $statValuesForMatchers[$goldStandardMatcherName][$dataName]["TP"] == 0 ? 1 : 0;
		$precision = ($stats["TP"]+$stats["FP"]) == 0 ? $precisionIf0 : $stats["TP"] / ($stats["TP"]+$stats["FP"]);
		
		$recallIf1 = $statValuesForMatchers[$goldStandardMatcherName][$dataName]["TP"] == 0 ? 1 : 0;
		$recall = ($stats["TP"]+$stats["FN"]) == 0 ? 1 : $stats["TP"] / ($stats["TP"]+$stats["FN"]);
		$fmeasure = ( ($recall+$precision) == 0 ) ? 0 : (2*$precision*$recall) / ($precision+$recall);
		$statValuesForMatchers[$matcherName][$dataName]["precision"] = round($precision, 3);
		$statValuesForMatchers[$matcherName][$dataName]["recall"] = round($recall, 3);
		$statValuesForMatchers[$matcherName][$dataName]["f-measure"] = round($fmeasure, 3);
	}
}

// calculate macro-stats for matchers
foreach ( $statValuesForMatchers as $matcherName => $dataStats ) {
	$numModelCombinations = 0;
	$macro_prec_sum = 0;
	$macro_rec_sum = 0;
	$marco_fmeasure_sum = 0;
	foreach ( $dataStats as $dataName => $stats ) {
		if ( $dataName != "_micro_stats" ) {
			$numModelCombinations++;
			$macro_prec_sum += $stats["precision"];
			$macro_rec_sum += $stats["recall"];
			$marco_fmeasure_sum += $stats["f-measure"];
		}
	}
	
	$statValuesForMatchers[$matcherName]["_macro_stats"] = array();
	$statValuesForMatchers[$matcherName]["_macro_stats"]["precision"] = ($numModelCombinations == 0) ? "n/a" : round(($macro_prec_sum/$numModelCombinations), 3);
	$statValuesForMatchers[$matcherName]["_macro_stats"]["recall"]    = ($numModelCombinations == 0) ? "n/a" : round(($macro_rec_sum/$numModelCombinations), 3);
	$statValuesForMatchers[$matcherName]["_macro_stats"]["f-measure"] = ($numModelCombinations == 0) ? "n/a" : round(($marco_fmeasure_sum/$numModelCombinations), 3);
}

// calculate variance and standard derivation for matchers
foreach ( $statValuesForMatchers as $matcherName => $dataStats ) {
	$numModelCombinations = 0;
	$variancePrecNumerator = 0;
	$varianceRecNumerator = 0;
	$varianceFmeasureNumerator = 0;
	foreach ( $dataStats as $dataName => $stats ) {
		if ( $dataName != "_micro_stats" && $dataName != "_macro_stats" ) {
			$numModelCombinations++;
			$variancePrecNumerator += ($stats["precision"]-$statValuesForMatchers[$matcherName]["_macro_stats"]["precision"])*($stats["precision"]-$statValuesForMatchers[$matcherName]["_macro_stats"]["precision"]);
			$varianceRecNumerator += ($stats["recall"]-$statValuesForMatchers[$matcherName]["_macro_stats"]["recall"])*($stats["recall"]-$statValuesForMatchers[$matcherName]["_macro_stats"]["recall"]);
			$varianceFmeasureNumerator += ($stats["f-measure"]-$statValuesForMatchers[$matcherName]["_macro_stats"]["f-measure"])*($stats["f-measure"]-$statValuesForMatchers[$matcherName]["_macro_stats"]["f-measure"]);
		}
	}

	$statValuesForMatchers[$matcherName]["_macro_stats"]["precision_variance"] = ($numModelCombinations == 0) ? "n/a" : round(($variancePrecNumerator/$numModelCombinations), 3);
	$statValuesForMatchers[$matcherName]["_macro_stats"]["precision_std_derivation"] = ($numModelCombinations == 0) ? "n/a" : round(sqrt($statValuesForMatchers[$matcherName]["_macro_stats"]["precision_variance"]), 3);
	$statValuesForMatchers[$matcherName]["_macro_stats"]["recall_variance"] = ($numModelCombinations == 0) ? "n/a" : round(($varianceRecNumerator/$numModelCombinations), 3);
	$statValuesForMatchers[$matcherName]["_macro_stats"]["recall_std_derivation"]    = ($numModelCombinations == 0) ? "n/a" : round(sqrt($statValuesForMatchers[$matcherName]["_macro_stats"]["recall_variance"]), 3);
	$statValuesForMatchers[$matcherName]["_macro_stats"]["f-measure_variance"] = ($numModelCombinations == 0) ? "n/a" : round(($varianceFmeasureNumerator/$numModelCombinations), 3);
	$statValuesForMatchers[$matcherName]["_macro_stats"]["f-measure_std_derivation"] = ($numModelCombinations == 0) ? "n/a" : round(sqrt($statValuesForMatchers[$matcherName]["_macro_stats"]["f-measure_variance"]), 3);

}

//var_dump($statValuesForMatchers);

// write micro stats to CSV
$statsCSV .= "\r\n;;;;precision;;;recall;;;f-measure;;\r\n";
$statsCSV .= "matcher;TP;FP;FN;mic;mac;sd;mic;mac;sd;mic;mac;sd\r\n";
foreach ( $statValuesForMatchers as $matcherName => $dataStats ) {
	if ( $matcherName == $goldStandardMatcherName ) continue;
	$stats_mic = $dataStats["_micro_stats"];
	$precision = str_replace(".", ",", round($stats_mic["precision"], 3));
	$recall = str_replace(".", ",", round($stats_mic["recall"], 3));
	$fmeasure = str_replace(".", ",", round($stats_mic["f-measure"], 3));
	$stats_mac = $dataStats["_macro_stats"];
	$statsCSV .= 
		$matcherName.";".
		$stats_mic["TP"].";".
		$stats_mic["FP"].";".
		$stats_mic["FN"].";".
		$precision.";".
		$dataStats["_macro_stats"]["precision"].";".
		$dataStats["_macro_stats"]["precision_std_derivation"].";".
		$recall.";".
		$dataStats["_macro_stats"]["recall"].";".
		$dataStats["_macro_stats"]["recall_std_derivation"].";".
		$fmeasure.";".
		$dataStats["_macro_stats"]["f-measure"].";".
		$dataStats["_macro_stats"]["f-measure_std_derivation"]."\r\n";
}

//write to stats CSV
$separators = "";
for ($i=1;$i<$numMatchers;$i++) $separators .= ";";

$matchersCSV = "";
foreach ( $statValuesForMatchers as $matcherName => $dataStats ) if ( $matcherName != $goldStandardMatcherName ) $matchersCSV .= ";".$matcherName;

$statsCSV .= "\r\n;precision;".$separators."recall;".$separators."f-measure".$separators."\r\n";
$statsCSV .= "model pair".$matchersCSV."".$matchersCSV."".$matchersCSV."\r\n";

foreach ( $statValuesForMatchers as $matcherName => $dataStats ) {
	if ( $matcherName == $goldStandardMatcherName ) continue;
	foreach ( $dataStats as $dataName => $stats ) {
		if ( $dataName != "_micro_stats" && $dataName != "_macro_stats" ) {
			
			$statsCSV .= $dataName;
			
			$precs = "";
			$recs = "";
			$fs = "";
			
			foreach ( $statValuesForMatchers as $currMatcherName => $ignoreTHIS ) {
				if ( $currMatcherName == $goldStandardMatcherName ) continue;
				$precs .= ";".$statValuesForMatchers[$currMatcherName][$dataName]["precision"];
				$recs .= ";".$statValuesForMatchers[$currMatcherName][$dataName]["recall"];
				$fs .= ";".$statValuesForMatchers[$currMatcherName][$dataName]["f-measure"];
			}
			
			$statsCSV .= $precs."".$recs."".$fs."\r\n";
			
		}
	}
	break;
}

$fileGenerator = new FileGenerator($output_stats, $statsCSV);
$fileGenerator->setPathFilename($output_stats);
$fileGenerator->setContent($statsCSV);
$uri_readme = $fileGenerator->execute(false);

print("done\n\nCompare mappings successfully finished.\n");


?>