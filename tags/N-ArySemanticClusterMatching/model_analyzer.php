<?php
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Model Analyzer \n-------------------------------------------------\n\n");

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

$analysis_csv = "Modelldatei:;".Config::MODEL_ANALYSIS_FILE."\n";
$analysis_csv .= "#Modelle;".$modelsInFile1."\n";
$dataPart = "EPK;#XOR;#OR;#AND;#Funktionen;#Gleichbeschriftete Funktionen;Gleichbeschriftete Funktionen;#Events;#Gleichbeschriftete Ereignisse;Gleichbeschriftete Ereignisse;Mehrfachbeschriftungen vorhanden;Ja-Nein-Problem vorhanden;isSESE\n";

$countCompletedCombinations = 0;
$progress = 0.1;

$modelsWithDuplicatedLabels = 0;
$modelsWithMultipleYesNo = 0;

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$epc1 = new EPC($xml1, $xml_epc1["name"]);
	if ( !in_array($nameOfEPC1, $ignore_models) ) {
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
			$dataPart .= "ja;";
			$modelsWithMultipleYesNo++;
		} else {
			$dataPart .= "nein;";
		}

		if ( $epc1->isSESE() ) {
			$dataPart .= "ja";
		} else {
			$dataPart .= "nein";
		}

		// 	if ( $epc1->checkANDSoundness() ) {
		// 		$dataPart .= "ja";
		// 	} else {
		// 		$dataPart .= "nein | ".implode($epc1->warnings, " | ");
		// 		//print_r($epc1->warnings);
		// 	}

		$dataPart .= "\n";
	}
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
