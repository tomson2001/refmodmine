<?php
print("\n-----------------------------------------------------------\n RefModMining - Mapping, Similarity and Variant Simulator \n-----------------------------------------------------------\n\n");

$similarityMeasures = array(
		"--ssbocan" => "Similarity Score Based On Common Activity Names",
		"--lms"		=> "Label Matching Similarity",
		"--fbse"    => "Feature Based Similarity Estimation",
		"--pocnae"	=> "Percentage Of Common Nodes And Edges",
		"--geds"	=> "Graph Edit Distance Similarity",
		"--amaged"  => "Activity Matching And Graph Edit Distance",
		"--cf"		=> "Causal Footprints"
);

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !array_key_exists($argv[1], $similarityMeasures) ) {
	exit("Folgende Aehnlichkeitsmasse stehen Ihenen zur Verfuegung:\n\n
	[--ssbocan]   ".$similarityMeasures["--ssbocan"]."
	[--lms]       ".$similarityMeasures["--lms"]."
	[--fbse]      ".$similarityMeasures["--fbse"]."
	[--pocnae]    ".$similarityMeasures["--pocnae"]."
	[--geds]      ".$similarityMeasures["--geds"]."
	[--amaged]    ".$similarityMeasures["--amaged"]."
	[--cf]        ".$similarityMeasures["--cf"]."\n
	[--help]      Hilfe\n\n");
}

/**
 * Einstellungen
 */

// Laden der Modelldateien
$content_file_1 = file_get_contents(Config::MODEL_FILE_1);
$xml1 = new SimpleXMLElement($content_file_1);

$html_analysis = HTMLComponents::AUTOMAPPING_HEADER;
$analysis_csv = "EPC1;#Functions in EPC1;#Events in EPC1;EPC2;#Functions in EPC2;#Events in EPC2;Eindeutig;".$similarityMeasures[$argv[1]]."\n";

// Aehnlichkeitsmatrix in CSV vorbereiten
$similarity_matrix_csv = ";Variante 1;Variante 2;Variante 3;Variante 4;Variante 5\n";

// Vorbereitung der Forschrittsanzeige
$modelsInFile1 = count($xml1->xpath("//epc"));
$countCombinations = $modelsInFile1 * 5;
$countCompletedCombinations = 0;
$progress = 0.1;

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("Modelldateien:\n");
print("  ".Config::MODEL_FILE_1." (".count($xml1->xpath("//epc"))." Modelle)\n\n");
print("Anzahl Modellkombinationen: ".$countCombinations."\n");
print("Gewaehltes Aehnlichkeitsmass: ".$similarityMeasures[$argv[1]]."\n\n");
?>