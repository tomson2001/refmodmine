<?php

print("\n-------------------------------------------------\n RefModMining - Label Analyzer \n-------------------------------------------------\n\n");

$start = time();
require 'autoloader.php';
$tagger = new StanfordPOSTagger(Config::STANDFORD_POS_TAGGER_PATH);
$NLPHighLevelTagTransformator = new NLPHighLevelTagTransformator();
$NLPLabelStyleVerifier = new NLPLableStyleVerifier();

// Verarbeitung der Modelldatei
$content_file = file_get_contents(Config::MODEL_ANALYSIS_FILE);
$xml = new SimpleXMLElement($content_file);

$modelsInFile = count($xml->xpath("//epc"));

// Count functions and events in the model file
$numFunctionsAndEvents = 0;
foreach ($xml->xpath("//epc") as $xml_epc) {
	$epc = new EPCExt($xml, $xml_epc["name"]);
	$numFunctionsAndEvents += count($epc->functions) + count($epc->events);
}

// Node NLP-Analysis CSV Header
$nodeNLPAnalysisCSVTop = "Model file:;".Config::MODEL_ANALYSIS_FILE."\n";
$nodeNLPAnalysisCSVTop .= "#models:;".$modelsInFile."\n";
$nodeNLPAnalysisCSVTop .= "#nodes:;".$numFunctionsAndEvents."\n";
$nodeNLPAnalysisCSV = "model;node-type;label;tag-set;high-level-tag-set;label-style";

// print infos to console
print("Model file: ".Config::MODEL_ANALYSIS_FILE."\n");
print("Number of models: ".$modelsInFile."\n");
print("Number of nodes (activities and events): ".$numFunctionsAndEvents."\n\n");
print("Start analyzing...\n");

// initiate progress bar
$nodeCount = 0;
$progressBar = new CLIProgressbar($numFunctionsAndEvents, 0.1);

// Analyze all nodes in all models in the file
$styleUnknown = "";
$styleKnown = "";
foreach ($xml->xpath("//epc") as $xml_epc) {
	$nameOfEPC = utf8_decode((string) $xml_epc["name"]);
	$epc = new EPCExt($xml, $xml_epc["name"]);
	foreach ( $epc->functions as $label ) {
		if ( preg_match("/^t[0-9]*$/", $label) ) { $nodeCount++; continue; }
		$taggedLabel = $tagger->array_tag($label);
		$highLevelTagSetString = $NLPHighLevelTagTransformator->transformTagSetString($taggedLabel[0]["tag_set_clean"]);
		$styleKey = $NLPLabelStyleVerifier->getLableStyleKey($highLevelTagSetString);
		$line = "\n".$nameOfEPC.";activity;".$taggedLabel[0]["sentence"].";".$taggedLabel[0]["tag_set_clean"].";".$highLevelTagSetString.";".$styleKey;
		if ( $styleKey === false ) {
			$styleUnknown .= $line;
		} else {
			$styleKnown .= $line;
		}
		$nodeCount++;
		$progressBar->run($nodeCount);
	}
	foreach ( $epc->events as $label ) {
		if ( preg_match("/^t[0-9]*$/", $label) ) { $nodeCount++; continue; }
		$taggedLabel = $tagger->array_tag($label);
		$highLevelTagSetString = $NLPHighLevelTagTransformator->transformTagSetString($taggedLabel[0]["tag_set_clean"]);
		$styleKey = $NLPLabelStyleVerifier->getLableStyleKey($highLevelTagSetString);
		$line = "\n".$nameOfEPC.";event;".$taggedLabel[0]["sentence"].";".$taggedLabel[0]["tag_set_clean"].";".$highLevelTagSetString.";".$styleKey;
		if ( $styleKey === false ) {
			$styleUnknown .= $line;
		} else {
			$styleKnown .= $line;
		}
		$nodeCount++;
		$progressBar->run($nodeCount);
	}
}

$nodeNLPAnalysisCSV .= $styleKnown."\n".$styleUnknown;

// durations of analysis
$duration = time() - $start;
$seconds = $duration % 60;
$secondsPerModel = ($duration/$modelsInFile) % 60;
$secondsPerNode = ($duration/$numFunctionsAndEvents) % 60;
$minutes = floor($duration / 60);
$minutesPerModel = floor(($duration/$modelsInFile) / 60);
$minutesPerNode = floor(($duration/$numFunctionsAndEvents) / 60);
$nodeNLPAnalysisCSVTop .= "Duration:;".$minutes." Min. ".$seconds." Sek\n";
$nodeNLPAnalysisCSVTop .= "Duration per model:;".$minutesPerModel." Min. ".$secondsPerModel." Sek\n";
$nodeNLPAnalysisCSVTop .= "Duration per node:;".$minutesPerNode." Min. ".$secondsPerNode." Sek\n\n";

// Generate Node NLP-Analysis CSV
$nodeNLPAnalysisCSV = $nodeNLPAnalysisCSVTop.$nodeNLPAnalysisCSV;
$fileGenerator = new FileGenerator("NodeNLPAnalysis.csv", $nodeNLPAnalysisCSV);
$fileGenerator->setFilename("NodeNLPAnalysis.csv");
$fileGenerator->setContent($nodeNLPAnalysisCSV);
$uri_analysis_csv = $fileGenerator->execute(false);

// print infos to console
print("\n\nAnalysis file (CSV) successfully created: ".$uri_analysis_csv."\n\n");
print("Duration: ".$minutes." Min. ".$seconds." Sek.\n\n");
print("Duration per model: ".$minutesPerModel." Min. ".$secondsPerModel." Sek\n");
print("Duration per node: ".$minutesPerNode." Min. ".$secondsPerNode." Sek\n\n");
?>