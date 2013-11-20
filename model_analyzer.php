<?php
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Model Analyzer \n-------------------------------------------------\n\n");

/**
 * Folgende Modelle werden ignoriert
*/
$ignore_models = array();


/**
 * Einstellungen
*/
$content_file_1 = file_get_contents(Config::MODEL_ANALYSIS_FILE);
$xml1 = new SimpleXMLElement($content_file_1);

$modelsInFile1 = count($xml1->xpath("//epc"));

print("Modelldatei:\n");
print("  ".Config::MODEL_ANALYSIS_FILE." (".$modelsInFile1." Modelle)\n\n");
print("Anzahl Modelle: ".$modelsInFile1."\n");

// Extrahiert den Namen des Modellrepositories anhand des Dateinamens
$repoName = Config::MODEL_ANALYSIS_FILE;
$lastSlash = strrpos($repoName, "/");
$repoName = substr($repoName, $lastSlash, -5);

$analysis_csv = "Modelldatei:;".Config::MODEL_ANALYSIS_FILE."\n";
$analysis_csv .= "#Modelle;".$modelsInFile1."\n";

$dataPart = "EPC;#XOR;#OR;#AND;#Functions;#Events;#Edges;#ProcessInterfaces;#RefinedFunctions;#SEQ-Operators;multi labeled functions;multi labeled events;";
$dataPart .= "#IllegalEvents;IllegalEvents (Labels);#StartEvents;#EndEvents;#IllegalFunctions;IllegalFunctions (Labels);#StartFunctions;#EndFunctions;#UndefinedConnectors;#ErrorConnectors;#StartConnectors;";
$dataPart .= "#EndConnectors;#StartProcessInterfaces;#EndProcessInterfaces;#IntermediateProcessInterfaces\n";

$countCompletedCombinations = 0;
$progress = 0.1;

$modelsWithDuplicatedLabels = 0;
$modelsWithMultipleYesNo = 0;

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$epc1 = new EPCExt($xml1, $xml_epc1["name"]);
	if ( !in_array($nameOfEPC1, $ignore_models) ) {
		$epc1->calculateSyntaxMetrics();
		$dataPart .= $nameOfEPC1.";";

		// Pruefung auf doppelte Labels bei Functions und Events
		$checkedFunctions = array();
		$doubleFunctions = array();
		$doubledFunctions = 0;

		foreach ( $epc1->functions as $label ) {
			if ( in_array($label, $checkedFunctions) ) {
				//array_push($doubleFunctions, str_replace(";", ",", str_replace("\r", "/r", str_replace("\n", "/n", $label))));
				array_push($doubleFunctions, str_replace(";", ",", str_replace("\r", " ", str_replace("\n", " ", $label))));
				$doubledFunctions++;
			} else {
				array_push($checkedFunctions, $label);
			}
		}

		$checkedEvents = array();
		$doubleEvents = array();
		$doubledEvents = 0;

		// Ja-Nein-Problem
		$hasMultipleYesNo = false;

		foreach ( $epc1->events as $label ) {
			if ( in_array($label, $checkedEvents) ) {
				//array_push($doubleEvents, str_replace(";", ",", str_replace("\r", "/r", str_replace("\n", "/n", $label))));
				array_push($doubleEvents, str_replace(";", ",", str_replace("\r", " ", str_replace("\n", " ", $label))));
				$doubledEvents++;
				if ( strtolower($label) == "yes" || strtolower($label) == "no"
						|| strtolower($label) == "ja" || strtolower($label) == "nein"
						|| strtolower($label) == "ok" || strtolower($label) == "not ok"
						|| strtolower($label) == "nicht ok"
				) {
					$hasMultipleYesNo = true;
				}
			} else {
				array_push($checkedEvents, $label);
			}
		}

		$dataPart .= count($epc1->xor).";";
		$dataPart .= count($epc1->or).";";
		$dataPart .= count($epc1->and).";";
		$dataPart .= count($epc1->functions).";";
		$dataPart .= count($epc1->events).";";
		$dataPart .= count($epc1->edges).";";
		
		$dataPart .= count($epc1->processInterfaces).";";
		$dataPart .= count($epc1->refinedFunctions).";";
		$dataPart .= count($epc1->seq).";";
		
		$dataPart .= implode(" | ", $doubleFunctions).";";
		$dataPart .= implode(" | ", $doubleEvents).";";
		
		$dataPart .= $epc1->illegalEvents.";";
		$dataPart .= implode(", ", $epc1->illegalEventLabels).";";
		$dataPart .= $epc1->startEvents.";";
		$dataPart .= $epc1->endEvents.";";
		$dataPart .= $epc1->illegalFunctions.";";
		$dataPart .= implode(", ", $epc1->illegalFunctionLabels).";";
		$dataPart .= $epc1->startFunctions.";";
		$dataPart .= $epc1->endFunctions.";";
		$dataPart .= $epc1->undefinedConnectors.";";
		$dataPart .= $epc1->errorConnectors.";";
		$dataPart .= $epc1->startConnectors.";";
		$dataPart .= $epc1->endConnectors.";";
		$dataPart .= $epc1->startProcessInterfaces.";";
		$dataPart .= $epc1->endProcessInterfaces.";";
		$dataPart .= $epc1->intermediateProcessInterfaces.";";

		$dataPart .= "\n";
	}
	print(".");
	if ( ($countCompletedCombinations/$modelsInFile1) >= $progress ) {
		print(" ".($progress*100)."% ");
		$progress += 0.1;
	}
}

$analysis_csv .= $dataPart;
$fileGenerator = new FileGenerator("AdHoc_Analysis".$repoName.".csv", $analysis_csv);
$uri_analysis_csv = $fileGenerator->execute(false);

print("\n\nAnalysedatei wurden erfolgreich erstellt:\n\n");
print("CSV mit Analyseergebnissen: ".$uri_analysis_csv."\n\n");

?>
