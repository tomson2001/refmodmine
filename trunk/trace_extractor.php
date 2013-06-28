<?php
$start = time();
require 'autoloader.php';

$log = "";
$output = "\n-------------------------------------------------\n RefModMining - Trace Extractor \n-------------------------------------------------\n\n";
print($output);
$log .= $output;

if ( in_array("--help", $argv) || in_array("-help", $argv) || in_array("-?", $argv) ) {
	exit("   Optionen:\n
   [--export]   Export der reduzierten EPKs
   [--help]     Hilfe\n\n");
}

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
$summary_csv .= "EPK;#Traces;Duration of extraction (Microtime);Duration of extraction (Seconds);Duration of extraction (String);Abort\n";

$countCompletedModels = 0;
$progress = 0.1;

$timeExceeded = 0;
$i = 1;
$lastTime = time();
$lastMicroTime = microtime(true);
foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$epc1 = new EPC($xml1, $xml_epc1["name"]);

	$traceExtractor = null;
	if ( in_array("--export", $argv) ) {
		$traceExtractor = new TraceExtractor($epc1, true, Config::MAX_TIME_PER_TRACE_EXTRAKTION);
	} else {
		$traceExtractor = new TraceExtractor($epc1, false, Config::MAX_TIME_PER_TRACE_EXTRAKTION);
	}
	
	$traces = $traceExtractor->execute();
	
	// Berechnungdauer
	$duration = time() - $lastTime;
	$detailedDuration = microtime(true) - $lastMicroTime;
	$seconds = $duration % 60;
	$minutes = floor($duration / 60);
	$lastTime = time();
	$lastMicroTime = microtime(true);

	if (is_string($traces)) {
		
		if ( $traces == "Time exceeded" ) $timeExceeded++;
		$analysis_csv .= "\n\n".$i.": ".$nameOfEPC1." (".$traces.") - Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n";
		$output = "\n   ".$i.": ".$nameOfEPC1.": ".$traces." - Dauer: ".$minutes." Min. ".$seconds." Sek.";
		print($output);
		$log .= $output;
		$summary_csv .= $nameOfEPC1.";".$traces.";".$detailedDuration.";".$duration.";".$minutes." Min. ".$seconds." Sek.;Ja\n";
		
	} else {
		
		$analysis_csv .= "\n\n".$i.": ".$nameOfEPC1." (".count($traces)." Traces) - Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n";
		$output = "\n   ".$i.": ".$nameOfEPC1.": ".count($traces)." Traces - Dauer: ".$minutes." Min. ".$seconds." Sek.";
		print($output);	
		$log .= $output;
		$summary_csv .= $nameOfEPC1.";".count($traces).";".$detailedDuration.";".$duration.";".$minutes." Min. ".$seconds." Sek.;Nein\n";

		// Traces in CSV schreiben
		$tracePart = "";
		foreach ($traces as $trace) {
			foreach ($trace as $funcID) {
				$funcName = $epc1->getNodeLabel($funcID) ? $epc1->getNodeLabel($funcID) : "unknown";
				$tracePart .= str_replace("\n", " ", $funcName).";";
			}
			$tracePart .= "\n ";
		}
		$analysis_csv .= $tracePart;
	}
	if ( ($countCompletedModels/$modelsInFile1) >= $progress ) {
		print(" ".($progress*100)."% ");
		$progress += 0.1;
	}
	$i++;
}

$fileGenerator = new FileGenerator("Trace_Extraction.csv", $analysis_csv);
$uri_analysis_csv = $fileGenerator->execute();

$output = "\n\nTime Exceeded (".Config::MAX_TIME_PER_TRACE_EXTRAKTION." Sek.): ".$timeExceeded."\n\n";
$output .= "\n\nTraces wurden erfolgreich extrahiert!\n\n";
$output .= "CSV mit Traces: ".$uri_analysis_csv."\n\n";
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
