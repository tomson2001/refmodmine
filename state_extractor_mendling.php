<?php
$start = time();
require 'autoloader.php';

$log = "";
$output = "\n-----------------------------------------------------------\n RefModMining - State Extractor (Reachability Graph)\n-----------------------------------------------------------\n\n";
print($output);
$log .= $output;

if ( in_array("--help", $argv) || in_array("-help", $argv) || in_array("-?", $argv) ) {
	exit("   Optionen:\n
   [--export]   Exportiert die Zustandsgraphen als .fsm-File
   [--help]     Hilfe\n\n");
}

/**
$doExtract = array(
	"Admission FU Berlin", "Admission IIS Erlangen", "Admission Muenster", "Admission Potsdam",
	"birhtCertificate_p33",
	"Abb.128_EPK umweltrechte Berichte definieren", "Abb.145_EPK Instrumente vorbereiten", "Abb.172_EPK Vollerfassung diskreter und stetiger Bewegungen",
	"EPK_Einbürgerungsurkunde", "EPK_Schulbuchverleih_Homburg", "Schulbuch_Rücknahme_Merzig_V1", "Schulbuch_Verleih_Merzig_V1", "Schulbuchverleih_RVSB_V1",
	"Abb.5.010_Prozessmodell Liefernatenstammdatenpflege", "Abb.5.013_Prozessmodell Konditionspflege", "Abb.5.024_Prozessmodell mehrstufige Disposition", 
		"Abb.5.027_Prozessmodell Disposition Lager", "Abb.5.038_Prozessmodell Überprüfung der Lieferscheinangaben", "Abb.5.050_Prozessmodell Rechnungsprüfung",
		"Abb.5.051_Prozessmodell Abweichungskontrolle", "Abb.5.104_Prozessmodell Kommissionierung", "Abb.6.012_Prozessmodell Zantralregulierung -Abrechnung",
		"Abb.6.020_Prozessmodell der operativen Aktionsplanung",
	"1An_kc5k", "1An_knwl", "1An_ks6c", "1An_kv7b", "1An_kzoq", "1An_l2cf", "1Ar_ma2i", "1Be_2ork", "1Be_38qs", "1Er_h4fo", "1Er_hhi9", "1Er_hsc3", "1Er_hsto", 
		"1Er_ixgh", "1Er_ixso", "1Ex_dxa3", "1Ex_dyea", "1Ex_dzq9", "1Ex_e1oz", "1Ex_e43l", "1Ex_e76a", "1Ex_e8vj", "1Ex_esd0", "1Ex_evsj", "1In_agyu", "1In_ahnr", 
		"1In_aklk", "1In_apbf", "1In_aslk", "1In_awpb", "1In_b19m", "1In_b3z3", "1In_b8et", "1In_bb6y", "1In_be6n", "1In_bip2", "1Ku_903f", "1Ku_97uj", "1Ku_9mgu", 
		"1Ku_9soy", "1Ku_a6af", "1Pe_lrja", "1Pe_lsz3", "1Pe_lx1m", "1Pe_max4", "1Pe_mbsh", "1Pe_mgei", "1Pe_mie0", "1Pr_10om", "1Pr_afh-", "1Pr_d1ur", "1Pr_dkfa", 
		"1Pr_smx-", "1Qu_btq3", "1Qu_bxuo", "1Qu_bywg", "1Qu_c5we", "1Qu_c8yd", "1Qu_cb8m", "1Qu_ce0j", "1Qu_cjnw", "1Tr_g68b", "1Tr_gjiw", "1Un_j73d", "1Un_jh6h", 
		"1Un_jqw9", "1Un_k30q", "1Ve_7c1w", "1Ve_7uuo", "1Ve_musj", "1Ve_mxed",
	"Kernprozess_8.06_Kostenstellenplanung_bei_Grenzplankostenrechnung", "Kernprozess_8.09_Plankalkulation_mit_Mengengerüst", "Kernprozess_8.10_Ergebnisplanung", 
		"Kernprozess_8.11_Materialstammbearbeitung_aus_Sicht_der_Produktionsplanung ", "Kernprozess_8.13_Arbeitsplatzbearbeitung", "Kernprozess_8.16_Programmplanung", 
		"Kernprozess_8.17_Leitteileplanung_Einzel", "Kernprozess_8.18_Materialbedarfsplanung-_Gesamt", "Kernprozess_8.32_Fertigungsauftragseröffnung", 
		"Kernprozess_8.34_Fertigungsauftragsfreigabe", "Kernprozess_8.35_Fertigungsauftragsdurchführung", "Kernprozess_8.38_Wareneingangsbearbeitung_aus_Produktion", 
		"Kernprozess_8.47_Warenausgangsbearbeitung", 
	"KL_07", "KL_28", "KL_36", "KL_41", "K_09", "KL_03", 
	"B.I.037_Ereignissteuerung der Bedarfsauflösung", "B.I.091_Trigger- und Aktionssteuerung der Bedarfsplanung", "B.I.132.a_Ereignissteuerung Kapazitätsabgleichs", 
		"B.I.222_Ereignisgesteuerte Cam Prozeßkette", "C.I.007_Ereignissteuerung der Produktionsentwicklung", "D.II.009_ARIS-Vorgehensmodell als ereignisgesteuerte Prozeßkette", 
		"D.II.019_Grobes ARIS-Unternhmungsmodell (UM) der Fachkonzeptebene (Ausschnitt)"
);
 */

/**
 * Einstellungen
*/
$content_file_1 = file_get_contents(Config::TRACE_EXTRACTOR_FILE);
$xml1 = new SimpleXMLElement($content_file_1);

$modelsInFile1 = count($xml1->xpath("//epc"));

$output = "Modelldatei:\n";
$output .=  "  ".Config::TRACE_EXTRACTOR_FILE." (".$modelsInFile1." Modelle)\n\n";
$output .= "Anzahl Modelle: ".$modelsInFile1."\n\n";

print($output);
$log .= $output;

$analysis_csv = "Modelldatei:;".Config::TRACE_EXTRACTOR_FILE."\n";
$analysis_csv .= "#Modelle;".$modelsInFile1."\n";
$summary_csv = $analysis_csv;
$summary_csv .= "MAX_TIME_PER_TRACE_EXTRAKTION: ".Config::MAX_TIME_PER_TRACE_EXTRAKTION." Sec.\n\n";
$summary_csv .= "EPC;#Nodes;#Functions;#Events;#XORs;#ANDs;#ORs;#States;Duration of extraction (Microtime);Duration of extraction (Seconds);Duration of extraction (String);Abort;Syntax-Error;Time-Exceeded;Curious-State-Count;AutoCorrected;CorrectedSplitJoinConnectors;CorrectedSenselessConnectors;CorrectedStartAndEndFunctions\n";

$countCompletedModels = 0;
$progress = 0.1;

$timeExceeded = 0;
$syntaxError = 	0;
$correctedModels = 0;
$i = 1;
$lastTime = time();
$lastMicroTime = microtime(true);
foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	
	/**
	 * Nur bestimmte Modelle berechnen
	 
	if ( !in_array($nameOfEPC1, $doExtract)) {
		if ( ($i/$modelsInFile1) >= $progress ) {
			print(" ".($progress*100)."% ");
			$progress += 0.1;
		}
		$output = "\n   ".$i.": ".$nameOfEPC1.": Skipped";
		print($output);
		$i++;
		continue;
	}
	*/
	
	$epc1 = new EPC($xml1, $xml_epc1["name"]);
	$stateExtractor = new ReachabilityGraphGeneratorMendling($epc1, Config::MAX_TIME_PER_TRACE_EXTRAKTION);
	
	$reachabilityGraph = $stateExtractor->execute();
	if ( in_array("--export", $argv) ) {
		if ( !is_string($reachabilityGraph) ) $reachabilityGraph->exportFSM();
	}
	$autoCorrected = $epc1->autoCorrected ? "Ja" : "Nein";
	if ( $epc1->autoCorrected ) $correctedModels++;
	$correctSplitJoin = $epc1->correctSplitJoinConnectors ? "Ja" : "Nein";
	$correctSenselessConnectors = $epc1->correctSenselessConnectors ? "Ja" : "Nein";
	$correctStartAndEndFunctions = $epc1->correctStandAndEndFunctions ? "Ja" : "Nein";
	$numOfNodes = $epc1->getNumOfNodes();
	$epc_analysis = $numOfNodes.";".count($epc1->functions).";".count($epc1->events).";".count($epc1->xor).";".count($epc1->and).";".count($epc1->or);
	
	// Berechnungdauer
	$duration = time() - $lastTime;
	$detailedDuration = microtime(true) - $lastMicroTime;
	$seconds = $duration % 60;
	$minutes = floor($duration / 60);
	$lastTime = time();
	$lastMicroTime = microtime(true);

	if ( is_string($reachabilityGraph) ) {
		
		$syntaxError++;
		$output = "\n   ".$i.": ".$nameOfEPC1.": ".$reachabilityGraph."";
		print($output);
		$log .= $output;
		$summary_csv .= $nameOfEPC1.";".$epc_analysis.";0;".str_replace(".", ",", $detailedDuration).";".$duration.";".$minutes." Min. ".$seconds." Sek.;Ja;Ja;Nein;Nein;".$autoCorrected.";".$correctSplitJoin.";".$correctSenselessConnectors.";".$correctStartAndEndFunctions."\n";
		
	} elseif (!$reachabilityGraph->complete) {
		
		$output = "\n   ".$i.": ".$nameOfEPC1.": Time exceeded (".$reachabilityGraph->getNumOfStates()." States)";
		print($output);	
		$log .= $output;
		$summary_csv .= $nameOfEPC1.";".$epc_analysis.";".$reachabilityGraph->getNumOfStates().";".str_replace(".", ",", $detailedDuration).";".$duration.";".$minutes." Min. ".$seconds." Sek.;Ja;Nein;Ja;Nein;".$autoCorrected.";".$correctSplitJoin.";".$correctSenselessConnectors.";".$correctStartAndEndFunctions."\n";
		$timeExceeded++;
		
	} else {
		
		$output = "\n   ".$i.": ".$nameOfEPC1.": ".$reachabilityGraph->getNumOfStates()." States - Dauer: ".$minutes." Min. ".$seconds." Sek.";
		print($output);	
		$log .= $output;
		$curiousStateCount = $reachabilityGraph->getNumOfStates() < $epc1->getNumOfNodes()+1 ? "Ja" : "Nein";
		$summary_csv .= $nameOfEPC1.";".$epc_analysis.";".$reachabilityGraph->getNumOfStates().";".str_replace(".", ",", $detailedDuration).";".$duration.";".$minutes." Min. ".$seconds." Sek.;Nein;Nein;Nein;".$curiousStateCount.";".$autoCorrected.";".$correctSplitJoin.";".$correctSenselessConnectors.";".$correctStartAndEndFunctions."\n";

	}
	if ( ($i/$modelsInFile1) >= $progress ) {
		print(" ".($progress*100)."% ");
		$progress += 0.1;
	}
	$i++;
}

$fileGenerator = new FileGenerator("State_Extraction.csv", $analysis_csv);
$uri_analysis_csv = $fileGenerator->execute();

$output = "\n\nTime Exceeded (".Config::MAX_TIME_PER_TRACE_EXTRAKTION." Sek.): ".$timeExceeded."/".$modelsInFile1."\n";
$output .= "Models with Syntax Error: ".$syntaxError."/".$modelsInFile1."\n";
$output .= "Auto-Corrected Models: ".$correctedModels."/".$modelsInFile1."\n";
$output .= ($modelsInFile1-$timeExceeded-$syntaxError)." von ".$modelsInFile1." Erreichbarkeitsgraphen wurden erfolgreich berechnet!\n\n";
print($output);
$log .= $output;

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);
$output = "Gesamtdauer: ".$minutes." Min. ".$seconds." Sek.\n\n";
print($output);
$log .= $output;


$fileGenerator->setFilename("Log.txt");
$fileGenerator->setContent($log);
$uri_log_txt = $fileGenerator->execute();

$fileGenerator->setFilename("Summary.csv");
$fileGenerator->setContent($summary_csv);
$uri_summary_csv = $fileGenerator->execute();

?>
