<?php

print("\n-------------------------------------------------\n RefModMining - Label Tagger \n-------------------------------------------------\n\n");

$start = time();
require 'autoloader.php';

// Verarbeitung der Modelldatei
$content_file = file_get_contents(Config::MODEL_ANALYSIS_FILE);
$xml = new SimpleXMLElement($content_file);
$modelsInFile = count($xml->xpath("//epc"));


// print infos to console
print("Model file: ".Config::MODEL_ANALYSIS_FILE."\n");
print("Number of models: ".$modelsInFile."\n\n");
print("Start analyzing...\n");

// initiate progress bar
$modelCount = 0;
$progressBar = new CLIProgressbar($modelsInFile, 0.1);
$progressBar->run($modelCount);

// Analyze all nodes in all models in the file
foreach ($xml->xpath("//epc") as $xml_epc) {
	$epc = new EPCNLP($xml, $xml_epc["epcId"], $xml_epc["name"]);
	$epc->loadLabelTags();
	$epc->generateHighLevelLabelTags();
	$epc->detectLableStyles();
	$epc->exportNLPAnalysisCSV();
	$modelCount++;
	$progressBar->run($modelCount);
}

// durations of analysis
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

// print infos to console
print("\n\nModel tags successfully persisted.\n\n");
print("Duration: ".$minutes." Min. ".$seconds." Sek.\n\n");
?>