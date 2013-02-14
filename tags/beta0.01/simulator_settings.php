<?php
print("\n-------------------------------------------------\n RefModMining - Mapping and Similarity Simulator \n-------------------------------------------------\n\n");

$similarityMeasures = array(
		"--ssbocan" => "Similarity Score Based On Common Activity Names",
		"--lms"		=> "Label Matching Similarity",
		"--fbse"    => "Feature Based Similarity Estimation",
		"--pocnae"	=> "Percentage Of Common Nodes And Edges",
		"--geds"	=> "Graph Edit Distance Similarity",
		"--amaged"  => "Activity Matching And Graph Edit Distance",
		"--cf"		=> "Causal Footprints",
		"--lcsot"	=> "Longest Common Subsequence Of Traces",
		"--ts"		=> "Terminology Similarity",
		"--tswf"	=> "Terminology Similarity With Frequencies"
);

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !array_key_exists($argv[1], $similarityMeasures) ) {
	exit("   Verfuegbare Aehnlichkeitsmasse:\n
   [--ssbocan]   ".$similarityMeasures["--ssbocan"]."
   [--lms]       ".$similarityMeasures["--lms"]."
   [--fbse]      ".$similarityMeasures["--fbse"]."
   [--pocnae]    ".$similarityMeasures["--pocnae"]."
   [--geds]      ".$similarityMeasures["--geds"]."
   [--amaged]    ".$similarityMeasures["--amaged"]."
   [--cf]        ".$similarityMeasures["--cf"]."
   [--lcsot]     ".$similarityMeasures["--lcsot"]."		
   [--ts]        ".$similarityMeasures["--ts"]."
   [--tswf]      ".$similarityMeasures["--tswf"]."\n
   Weitere Optionen:\n
   [--light]     Keine Mappinganalyse, keine HTML-Generierung
   [--help]      Hilfe\n\n");
}

$isLight = in_array("--light", $argv) ? true : false;

$doMapping = $argv[1] == "--ts" || $argv[1] == "--tswf" ? false : true;

/**
 * Einstellungen
 */

// Laden der Modelldateien
$content_file_1 = file_get_contents(Config::MODEL_FILE_1);
$xml1 = new SimpleXMLElement($content_file_1);
$content_file_2 = file_get_contents(Config::MODEL_FILE_2);
$xml2 = new SimpleXMLElement($content_file_2);

if (!$isLight) $html_analysis = HTMLComponents::AUTOMAPPING_HEADER;
$analysis_csv = "EPC1;#Functions in EPC1;#Events in EPC1;EPC2;#Functions in EPC2;#Events in EPC2;Eindeutig;".$similarityMeasures[$argv[1]]."\n";

// Aehnlichkeitsmatrix in CSV vorbereiten
$similarity_matrix_csv = "";
foreach ($xml2->xpath("//epc") as $xml_epc2) {
	$nameOfEPC2 = utf8_decode((string) $xml_epc2["name"]);
	$similarity_matrix_csv .= ";".$nameOfEPC2;
}
$similarity_matrix_csv .= "\n";

// Vorbereitung der Forschrittsanzeige
$modelsInFile1 = count($xml1->xpath("//epc"));
$modelsInFile2 = count($xml2->xpath("//epc"));
$countCombinations = $modelsInFile1 * $modelsInFile2;
$countCompletedCombinations = 0;
$progress = 0.1;

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("Modelldateien:\n");
print("  1. ".Config::MODEL_FILE_1." (".count($xml1->xpath("//epc"))." Modelle)\n");
print("  2. ".Config::MODEL_FILE_2." (".count($xml2->xpath("//epc"))." Modelle)\n\n");
print("Anzahl Modellkombinationen: ".$countCombinations."\n");
print("Gewaehltes Aehnlichkeitsmass: ".$similarityMeasures[$argv[1]]."\n\n");

// ReadMe.txt erzeugen
$readme = "-----------------------------------------------\r\n";
$readme .= "RefModMining - Mapping and Similarity Simulator\r\n";
$readme .= "-----------------------------------------------\r\n\r\n";
$readme .= "Ausfuehrungsinformationen:\r\n";
$readme .= " - Aehnlichkeitsmass: ".$similarityMeasures[$argv[1]]."\r\n";
$lightPrint = $isLight ? "Ja" : "Nein";
$readme .= " - Mapping-Analyse und Mappings (HTML) erstellt: ".$lightPrint."\r\n";
$readme .= " - Erste Modelldatei:  ".Config::MODEL_FILE_1." (".$modelsInFile1." Modelle)\r\n";
$readme .= " - Zweite Modelldatei: ".Config::MODEL_FILE_2." (".$modelsInFile2." Modelle)\r\n";
$readme .= " - Anzahl Modellkombinationen: ".$countCombinations."\r\n";
$readme .= " - Startzeit: ".date("d.m.Y H:i:s")."\r\n\r\n";

?>