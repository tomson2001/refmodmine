<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Trace Extractor \n-------------------------------------------------\n\n");

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

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$epc1 = new EPC($xml1, $xml_epc1["name"]);

	$traceExtractor = new TraceExtractor($epc1);
	$traces = $traceExtractor->execute();
	
	if (is_string($traces)) {
		exit($traces);
	}
	
	$analysis_csv .= "\n\n".$nameOfEPC1." (".count($traces)." Traces)\n\n";
	
	print("\n   ".$nameOfEPC1.": ".count($traces)." Traces");
	
	// Traces auf Konsole schreiben
	$tracePart = "";
	foreach ($traces as $trace) {
		foreach ($trace as $funcID) {
			$tracePart .= $epc1->functions[$funcID].";";
		}
		$tracePart .= "\n ";
	}
	$analysis_csv .= $tracePart;
	
	if ( ($countCompletedModels/$modelsInFile1) >= $progress ) {
		print(" ".($progress*100)."% ");
		$progress += 0.1;
	}
}

$fileGenerator = new FileGenerator("Trace_Extraction.csv", $analysis_csv);
$uri_analysis_csv = $fileGenerator->execute();

print("\n\nTraces wurden erfolgreich extrahiert!\n\n");
print("CSV mit Traces: ".$uri_analysis_csv."\n\n");

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);
print("Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n");

?>
