<?php
$start = time();
$lastTime = $start;
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Trace Extractor \n-------------------------------------------------\n\n");

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

print("Modelldatei:\n");
print("  ".Config::TRACE_EXTRACTOR_FILE." (".$modelsInFile1." Modelle)\n\n");
print("Anzahl Modelle: ".$modelsInFile1."\n\n");

$analysis_csv = "Modelldatei:;".Config::TRACE_EXTRACTOR_FILE."\n";
$analysis_csv .= "#Modelle;".$modelsInFile1."\n";

$countCompletedModels = 0;
$progress = 0.1;

$timeExceeded = 0;
$i = 1;
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
	$seconds = $duration % 60;
	$minutes = floor($duration / 60);
	$lastTime = time();

	if (is_string($traces)) {
		
		if ( $traces == "Time exceeded" ) $timeExceeded++;
		$analysis_csv .= "\n\n".$i.": ".$nameOfEPC1." (".$traces.") - Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n";
		print("\n   ".$i.": ".$nameOfEPC1.": ".$traces." - Dauer: ".$minutes." Min. ".$seconds." Sek.");
		
	} else {
		
		$analysis_csv .= "\n\n".$i.": ".$nameOfEPC1." (".count($traces)." Traces) - Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n";
		print("\n   ".$i.": ".$nameOfEPC1.": ".count($traces)." Traces - Dauer: ".$minutes." Min. ".$seconds." Sek.");

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

print("\n\nTime Exceeded (".Config::MAX_TIME_PER_TRACE_EXTRAKTION." Sek.): ".$timeExceeded."\n\n");
print("\n\nTraces wurden erfolgreich extrahiert!\n\n");
print("CSV mit Traces: ".$uri_analysis_csv."\n\n");

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);
print("Gesamtdauer: ".$minutes." Min. ".$seconds." Sek.\n\n");

?>
