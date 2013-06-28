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
   [--ssbocan]       ".$similarityMeasures["--ssbocan"]."
   [--lms]           ".$similarityMeasures["--lms"]."
   [--fbse]          ".$similarityMeasures["--fbse"]."
   [--pocnae]        ".$similarityMeasures["--pocnae"]."
   [--geds]          ".$similarityMeasures["--geds"]."
   [--amaged]        ".$similarityMeasures["--amaged"]."
   [--cf]            ".$similarityMeasures["--cf"]."
   [--lcsot]         ".$similarityMeasures["--lcsot"]."
   [--ts]            ".$similarityMeasures["--ts"]."
   [--tswf]          ".$similarityMeasures["--tswf"]."\n

   Zusatzoptionen fuer ".$similarityMeasures["--lcsot"].":\n
   [--exportEPML]    Export der reduzierten EPML-Dateien
   [--exportTraces]  CSV-Export der Traces\n

   Weitere Optionen:\n
   [--light]         Keine Mappinganalyse, keine HTML-Generierung
   [--help]          Hilfe\n\n");
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
if ( $argv[1] == "--lcsot" ) {
	$analysis_csv = "EPC1;#Functions in EPC1;#Events in EPC1;EPC2;#Functions in EPC2;#Events in EPC2;Eindeutig;#Gemappte Funktionen;#Traces EPC1;#Traces EPC2;".$similarityMeasures[$argv[1]]."\n";
} else {
	$analysis_csv = "EPC1;#Functions in EPC1;#Events in EPC1;EPC2;#Functions in EPC2;#Events in EPC2;Eindeutig;#Gemappte Funktionen;".$similarityMeasures[$argv[1]]."\n";
}

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


/**
 * SAP-Ignore: Folgende Modelle sollen beim SAP Referenzmodell aufgrund der zu hohen Laufzeit ignoriert werden (77 Modelle) => 88% laufen durch!
 */
$ignore_models = array(
		// SAP Modelle
		"1An_kc5k", "1An_knwl", "1An_ks6c", "1An_kv7b", "1An_kzoq", "1An_l2cf", "1Ar_ma2i", "1Be_2ork", "1Be_38qs", "1Er_h4fo", "1Pr_smx-",
		"1Er_hhi9", "1Er_hsc3", "1Er_hsto", "1Er_ixgh", "1Er_ixgh", "1Er_ixso", "1Ex_dxa3", "1Ex_dyea", "1Ex_dzq9", "1Ex_e1oz", "1Ex_e43l",
		"1Ex_e76a", "1Ex_e8vj", "1Ex_esd0", "1Ex_evsj", "1Im_ljm4", "1Im_lmu3", "1In_agyu", "1In_ahnr", "1In_aklk", "1In_apbf", "1In_aslk", 
		"1In_at4y", "1In_awpb", "1In_b19m", "1In_b3z3", "1In_b8et", "1In_bb6y", "1In_be6n", "1In_bip2", "1Ku_903f", "1Ku_97uj", "1Ku_9mgu",
		"1Ku_9soy", "1Ku_a6af", "1Pe_lrja", "1Pe_lsz3", "1Pe_lx1m", "1Pe_max4", "1Pe_mbsh", "1Pe_mgei", "1Pe_mie0", "1Pr_10om", "1Pr_afh",
		"1Pr_d1ur", "1Pr_dkfa", "1Pr_smx", "1Qu_btq3", "1Qu_bxuo", "1Qu_bywg", "1Qu_c5we", "1Qu_c8yd", "1Qu_cb8m", "1Qu_ce0j", "1Qu_cjnw",
		"1Tr_g68b", "1Tr_gjiw", "1Un_j73d", "1Un_jh6h", "1Un_jqw9", "1Un_k30q", "1Ve_7c1w", "1Ve_7uuo", "1Ve_musj", "1Ve_mxed", "1Pr_afh-");

//$ignore_models = array();
?>