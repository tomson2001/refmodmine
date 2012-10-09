<?php
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Model Analyzer \n-------------------------------------------------\n\n");

/**
 * Einstellungen
*/
$content_file_1 = file_get_contents(Config::MODEL_ANALYSIS_FILE);
$xml1 = new SimpleXMLElement($content_file_1);

$modelsInFile1 = count($xml1->xpath("//epc"));

print("Modelldatei:\n");
print("  ".Config::MODEL_ANALYSIS_FILE." (".$modelsInFile1." Modelle)\n\n");
print("Anzahl Modelle: ".$modelsInFile1."\n");

$analysis_csv = "Modelldatei:;".Config::MODEL_ANALYSIS_FILE."\n";
$analysis_csv .= "#Modelle;".$modelsInFile1."\n";
$dataPart = "EPK;#Funktionen;#Gleichbeschriftete Funktionen;Gleichbeschriftete Funktionen;#Events;#Gleichbeschriftete Ereignisse;Gleichbeschriftete Ereignisse;Mehrfachbeschriftungen vorhanden;Ja-Nein-Problem vorhanden\n";

$countCompletedCombinations = 0;
$progress = 0.1;

$modelsWithDuplicatedLabels = 0;
$modelsWithMultipleYesNo = 0;

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$epc1 = new EPC($xml1, $xml_epc1["name"]);
	$dataPart .= $nameOfEPC1.";";
	
	// Pruefung auf doppelte Labels bei Functions und Events
	$checkedFunctions = array();
	$doubleFunctions = array();
	$doubledFunctions = 0;
	
	foreach ( $epc1->functions as $label ) {
		if ( in_array($label, $checkedFunctions) ) {
			array_push($doubleFunctions, str_replace(";", ",", str_replace("\r", "/r", str_replace("\n", "/n", $label))));
			$doubledFunctions++;
		} else {
			array_push($checkedFunctions, $label);
		}
	}
	
	$checkedEvents = array();
	$doubleEvents = array();
	$doubledEvents = 0;
	
	$hasMultipleYesNo = false;

	foreach ( $epc1->events as $label ) {
		if ( in_array($label, $checkedEvents) ) {
			array_push($doubleEvents, str_replace(";", ",", str_replace("\r", "/r", str_replace("\n", "/n", $label))));
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
		
	$dataPart .= count($epc1->functions).";";
	$dataPart .= $doubledFunctions.";";
	$dataPart .= implode(" | ", $doubleFunctions).";";
	$dataPart .= count($epc1->events).";";
	$dataPart .= $doubledEvents.";";
	$dataPart .= implode(" | ", $doubleEvents).";";
	if ( $doubledEvents + $doubledFunctions == 0 ) {
		$dataPart .= "nein;";
	} else {
		$dataPart .= "ja;";
		$modelsWithDuplicatedLabels++;
	}
	
	if ( $hasMultipleYesNo ) {
		$dataPart .= "ja";
		$modelsWithMultipleYesNo++;
	} else {
		$dataPart .= "nein";
	}
	
	$dataPart .= "\n";
	
	print(".");
	if ( ($countCompletedCombinations/$modelsInFile1) >= $progress ) {
		print(" ".($progress*100)."% ");
		$progress += 0.1;
	}
}

$analysis_csv .= "#Modelle mit gleichbeschrifteten Funktionen oder Ereignissen;".$modelsWithDuplicatedLabels."\n";
$analysis_csv .= "#Modelle mit Ja-Nein-Ereignis-Problem;".$modelsWithMultipleYesNo."\n\n";

$analysis_csv .= $dataPart;
$fileGenerator = new FileGenerator("Model_Analysis.csv", $analysis_csv);
$uri_analysis_csv = $fileGenerator->execute();

print("\n\nAnalysedatei wurden erfolgreich erstellt:\n\n");
print("CSV mit Analyseergebnissen: ".$uri_analysis_csv."\n\n");

?>
