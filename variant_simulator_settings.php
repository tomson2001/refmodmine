<?php
print("\n-----------------------------------------------------------\n RefModMining - Mapping, Similarity and Variant Simulator \n-----------------------------------------------------------\n\n");

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
	exit("Folgende Aehnlichkeitsmasse stehen Ihenen zur Verfuegung:\n\n
    [--ssbocan]   ".$similarityMeasures["--ssbocan"]."
    [--lms]       ".$similarityMeasures["--lms"]."
    [--fbse]      ".$similarityMeasures["--fbse"]."
    [--pocnae]    ".$similarityMeasures["--pocnae"]."
    [--geds]      ".$similarityMeasures["--geds"]."
    [--amaged]    ".$similarityMeasures["--amaged"]."
    [--cf]        ".$similarityMeasures["--cf"]."
    [--lcsot]     ".$similarityMeasures["--lcsot"]."
    [--ts]        ".$similarityMeasures["--ts"]."
    [--tswf]      ".$similarityMeasures["--tswf"]."\n\n
    [--help]      Hilfe\n\n");
}

$doMapping = $argv[1] == "--ts" || $argv[1] == "--tswf" ? false : true;

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
		"1Tr_g68b", "1Tr_gjiw", "1Un_j73d", "1Un_jh6h", "1Un_jqw9", "1Un_k30q", "1Ve_7c1w", "1Ve_7uuo", "1Ve_musj", "1Ve_mxed", "1Pr_afh-",
		
		// Dauert zu lange fuer Varianten
		"1An_l8wo", "1Ar_m7re", "1Ar_m8qw", "1Er_iqc9", "1Er_h8hr", "1Er_j49j", "1Ex_elth", "1Im_lcbm", "1Im_lhqh", "1In_b7s7", "1In_bi2g",
		"1Ku_8w9x", "1Ku_8y3g", "1Pe_ly3r", "1Pr_1bt1", "1Pr_1kwn", "1Pr_3t2p", "1Pr_43cw", "1Pr_diw6", "1Pr_f7e-", "1Ve_6294", 
);

//$ignore_models = array();
?>