<?php
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Model Analyzer \n-------------------------------------------------\n\n");

/**
 * Folgende Modelle werden ignoriert
*/
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

// Extrahiert den Namen des Modellrepositories anhand des Dateinamens
$repoName = Config::MODEL_ANALYSIS_FILE;
$lastSlash = strrpos($repoName, "/");
$repoName = substr($repoName, $lastSlash, -5);

$analysis_csv = "Modelldatei:;".Config::MODEL_ANALYSIS_FILE."\n";
$analysis_csv .= "#Modelle;".$modelsInFile1."\n";

$dataPart = "EPC;Nodetype;Object-Type;Symbol-Type\n";

$countCompletedCombinations = 0;
$progress = 0.1;

$modelsWithDuplicatedLabels = 0;
$modelsWithMultipleYesNo = 0;

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$epc1 = new EPCExtAnalysis($xml1, $xml_epc1["name"]);
	if ( !in_array($nameOfEPC1, $ignore_models) ) {
		$nodes = $epc1->getAllNodes();
		foreach ( $nodes as $id => $label ) {
			$nodeType = $epc1->getType($id);
			$dataPart .= $epc1->name.";".$nodeType.";".$epc1->objectTypes[$id].";".$epc1->symbolTypes[$id]."\n";
		}
		$dataPart .= "\n";
	}
	
	print(".");
	if ( ($countCompletedCombinations/$modelsInFile1) >= $progress ) {
		print(" ".($progress*100)."% ");
		$progress += 0.1;
	}
}

$analysis_csv .= $dataPart;
$fileGenerator = new FileGenerator("TypeAnalysis".$repoName.".csv", $analysis_csv);
$uri_analysis_csv = $fileGenerator->execute(false);

print("\n\nAnalysedatei wurden erfolgreich erstellt:\n\n");
print("CSV-File: ".$uri_analysis_csv."\n\n");

?>
