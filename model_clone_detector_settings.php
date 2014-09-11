<?php
print("\n-------------------------------------------------\n RefModMining - Model Clone Detection \n-------------------------------------------------\n\n");

if ( in_array("--help", $argv) || in_array("-help", $argv) || in_array("--h", $argv) || in_array("-h", $argv) || in_array("h", $argv) || in_array("help", $argv) ) {
	exit("   Optionen:\n
   [--onefile]       Fuehrt das CloneDetection nur auf MODEL_FILE_1 aus\n
	
   [--help]          Hilfe\n\n");
}

if ( !in_array("--onefile", $argv) ) print("Gewaelte Option: Clone Detection innerhalb einer Modelldatei\n\n");

/**
 * Einstellungen
 */

// Laden der Modelldateien
$content_file_1 = file_get_contents(Config::MODEL_FILE_1);
$xml1 = new SimpleXMLElement($content_file_1);
$content_file_2 = file_get_contents(Config::MODEL_FILE_2);
$xml2 = new SimpleXMLElement($content_file_2);

$html_analysis = HTMLComponents::AUTOMAPPING_HEADER;
$analysis_csv = "EPC1;#Functions in EPC1;#Events in EPC1;EPC2;#Functions in EPC2;#Events in EPC2;#Gemappte Funktionen in EPC1;#Gemappte Funktionen in EPC2;#Matches;#Simple Matches;#Complex Matches;Model Similarity based on function labels\n";

// Vorbereitung der Forschrittsanzeige
$modelsInFile1 = count($xml1->xpath("//epc"));
$modelsInFile2 = in_array("--onefile", $argv) ? $modelsInFile1 : count($xml2->xpath("//epc"));
$numOfAllModels = $modelsInFile1 + $modelsInFile2;
$countCombinations = $modelsInFile1 * $modelsInFile2;
$countCompletedCombinations = 0;
$progress = 0.1;

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("Modelldateien:\n");
print("  1. ".Config::MODEL_FILE_1." (".count($xml1->xpath("//epc"))." Modelle)\n");
if ( !in_array("--onefile", $argv) ) print("  2. ".Config::MODEL_FILE_2." (".count($xml2->xpath("//epc"))." Modelle)\n\n");
print("Anzahl Modellkombinationen: ".$countCombinations."\n");

?>