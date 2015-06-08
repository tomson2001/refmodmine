<?php

// @TODO NOT TESTED AND NOT FINISHED!

/**
 * Wurde speziell fuer das Szenario im Rahmen des IWi Process Model Korpus entwickelt
 */
$start = time();
require 'autoloader.php';


print("\n-------------------------------------------------\n RefModMining - Mapping and Similarity Simulator \n-------------------------------------------------\n\n");

$similarityMeasures = array(
		"--ssbocan" => "Similarity Score Based On Common Activity Names",
		"--lms"		=> "Label Matching Similarity",
		"--fbse"    => "Feature Based Similarity Estimation",
		"--pocnae"	=> "Percentage Of Common Nodes And Edges",
		"--geds"	=> "Graph Edit Distance Similarity",
		"--amaged"  => "Activity Matching And Graph Edit Distance",
		"--cf"		=> "Causal Footprints",
		"--nscm"	=> "N-Ary Semantic Cluster Matching"
);

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !array_key_exists($argv[1], $similarityMeasures) ) {
	exit("   Verfuegbare Aehnlichkeitsmasse/Matching-Verfahren:\n
   [--ssbocan]       ".$similarityMeasures["--ssbocan"]."
   [--lms]           ".$similarityMeasures["--lms"]."
   [--fbse]          ".$similarityMeasures["--fbse"]."
   [--pocnae]        ".$similarityMeasures["--pocnae"]."
   [--geds]          ".$similarityMeasures["--geds"]."
   [--amaged]        ".$similarityMeasures["--amaged"]."
   [--cf]            ".$similarityMeasures["--cf"]."
   [--nscm]          ".$similarityMeasures["--nscm"]."\n

   Weitere Optionen:\n
   [--help]          Hilfe\n\n");
}

/**
 * Einstellungen
 */

// Laden der Modelldateien
$content_file_1 = file_get_contents(Config::MODEL_FILE_1);
$xml1 = new SimpleXMLElement($content_file_1);
$content_file_2 = file_get_contents(Config::MODEL_FILE_2);
$xml2 = new SimpleXMLElement($content_file_2);

$html_analysis = HTMLComponents::AUTOMAPPING_HEADER;
$analysis_csv = "EPC1;#Functions in EPC1;#Events in EPC1;EPC2;#Functions in EPC2;#Events in EPC2;#Gemappte Funktionen in EPC1;#Gemappte Funktionen in EPC2;#Matches;#Simple Matches;#Complex Matches;Model Similarity based on function labels\n";

// Vorbereitung der Forschrittsanzeige
$modelsInFile1 = count($xml1->xpath("//epc"));
$modelsInFile2 = count($xml2->xpath("//epc"));
$numOfAllModels = $modelsInFile1 + $modelsInFile2;
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
$readme .= "RefModMining - Mapping Generator\r\n";
$readme .= "-----------------------------------------------\r\n\r\n";
$readme .= "Ausfuehrungsinformationen:\r\n";
$readme .= " - Aehnlichkeitsmass: ".$similarityMeasures[$argv[1]]."\r\n";
$readme .= " - Erste Modelldatei:  ".Config::MODEL_FILE_1." (".$modelsInFile1." Modelle)\r\n";
$readme .= " - Zweite Modelldatei: ".Config::MODEL_FILE_2." (".$modelsInFile2." Modelle)\r\n";
$readme .= " - Anzahl Modellkombinationen: ".$countCombinations."\r\n";
$readme .= " - Startzeit: ".date("d.m.Y H:i:s")."\r\n\r\n";




// Ergebnisordner erstellen
$lastSlashPositionFile1 = strrpos(Config::MODEL_FILE_1, "/");
$lastSlashPositionFile2 = strrpos(Config::MODEL_FILE_2, "/");
$folderName = substr(Config::MODEL_FILE_1, $lastSlashPositionFile1+1, -5)."---".substr(Config::MODEL_FILE_2, $lastSlashPositionFile2+1, -5);
mkdir("files".DIRECTORY_SEPARATOR.$folderName);

$allFuncNodesOfModelFile1 = array();
$allFuncNodesOfModelFile2 = array();

/**
 * Multithread-Operation
*/
$numOfCoresToWorkOn = Config::NUM_CORES_TO_WORK_ON;

print("Vorbereitung des Multi-Core Processing auf ".$numOfCoresToWorkOn." Kernen... \n");

$naryMapping = null;
if ( in_array("--nscm", $argv) ) $naryMapping = new NAryWordstemMappingWithAntonyms();

$modelCount = 0;
$progressBar = new CLIProgressbar($numOfAllModels, 0.1);

// Vorbereitung der EPKs der ersten File
$epcsOfFile1 = array();
foreach ($xml1->xpath("//epc") as $xml_epc1) {
	// EPK-Objekt erstellen
	$epc = new EPC($xml1, $xml_epc1["epcId"], $xml_epc1["name"]);
	$epc->deleteDummyTransitions();
	// Funktionslabels fuer Auswertung auslesen
	foreach ( $epc->functions as $funcID => $funcLabel ) {
		array_push($allFuncNodesOfModelFile1, array("id" => $funcID, "label" => $funcLabel, "EPC_InternalID" => $epc->internalID, "EPC_Name" => $epc->name));
	}
	array_push($epcsOfFile1, $epc);
	if ( in_array("--nscm", $argv) ) $naryMapping->addEPC($epc);
	$modelCount++;
	$progressBar->run($modelCount);
}

// Vorbereitung der EPKs der zweiten File
// Splitten der Aufgaben auf die Anzahl der Kerne
$splitCount = round($modelsInFile2/$numOfCoresToWorkOn);
$nextSplit = $splitCount;
$epcsOfFile2Parts = array();
$epcsOfFile2Parts[0] = array();
$part = 0;
$i = 0;
foreach ($xml2->xpath("//epc") as $xml_epc2) {
	$i++;
	if ( $i == $nextSplit && $part < $numOfCoresToWorkOn-1 ) {
		$part++;
		$epcsOfFile2Parts[$part] = array();
		$nextSplit += $splitCount;
	}
	$epc = new EPC($xml2, $xml_epc2["epcId"], $xml_epc2["name"]);
	$epc->deleteDummyTransitions();
	// Funktionslabels fuer Auswertung auslesen
	foreach ( $epc->functions as $funcID => $funcLabel ) {
		array_push($allFuncNodesOfModelFile2, array("id" => $funcID, "label" => $funcLabel, "EPC_InternalID" => $epc->internalID, "EPC_Name" => $epc->name));
	}
	array_push($epcsOfFile2Parts[$part], $epc);
	if ( in_array("--nscm", $argv) ) $naryMapping->addEPC($epc);
	$modelCount++;
	$progressBar->run($modelCount);
}

print("\ndone\n\n");

$removedPossibleEvents = array();
if ( in_array("--nscm", $argv) ) {
	$naryMapping->mapMultiCore($folderName);
	$removedPossibleEvents = $naryMapping->removedPossibleEvents;
}

print("Berechnung der Mappings...\n");
$maxThread = 0;
$thread = array();
foreach ( $epcsOfFile2Parts as $threadID => $epcsOfFile2Part ) {
	$thread[$threadID+1] = new MultiThreadMappingOperation($threadID+1, $epcsOfFile1, $epcsOfFile2Part, $argv, $folderName, $numOfCoresToWorkOn, $naryMapping);
	$thread[$threadID+1]->start();
	$maxThread = $threadID+1;
}

$allMatchedFuncNodesOfModelFile1 = array();
$allMatchedFuncNodesOfModelFile2 = array();

$progressBar = new CLIProgressbar($countCombinations, 0.1);
$currentThread = 1;
while ( $currentThread <= $maxThread ) {
	if ( $thread[$currentThread]->isRunning() ) {
		sleep(1);
		$finishedOperations = 0;
		for ( $i=1; $i<=$maxThread; $i++ ) {
			$finishedOperations += $thread[$i]->finishedOperations;
		}
		$progressBar->run($finishedOperations);
	} else {
		$analysis_csv .= $thread[$currentThread]->analysis_csv_part;
		$html_analysis .= $thread[$currentThread]->html_analysis_part;

		foreach ( $thread[$currentThread]->allMatchedFuncNodesOfModelFile1 as $funcDef ) {
			array_push($allMatchedFuncNodesOfModelFile1, $funcDef);
		}

		foreach ( $thread[$currentThread]->allMatchedFuncNodesOfModelFile2 as $funcDef ) {
			array_push($allMatchedFuncNodesOfModelFile2, $funcDef);
		}

		//print("Thread ".$currentThread." of ".$numOfCoresToWorkOn." finished\n");
		$currentThread++;
	}
}
print("\ndone");


// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator("Mapping_Analysis.csv", $analysis_csv);
$fileGenerator->setPath($folderName);
$fileGenerator->setFilename("Mapping_Analysis.csv");
$uri_analysis_csv = $fileGenerator->execute(false);

$fileGenerator->setFilename("Mappings.html");
$fileGenerator->setContent($html_analysis);
$uri_html_analysis = $fileGenerator->execute(false);

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

// allMatchedFuncs so umberechnen, dass jeder Knoten nur maximal einmal enthalten ist.
$allMatchedFuncNodesOfModelFile1 = Tools::array_unique_complex($allMatchedFuncNodesOfModelFile1);
$allMatchedFuncNodesOfModelFile2 = Tools::array_unique_complex($allMatchedFuncNodesOfModelFile2);
$allFuncNodesOfModelFile1 = Tools::array_unique_complex($allFuncNodesOfModelFile1);
$allFuncNodesOfModelFile2 = Tools::array_unique_complex($allFuncNodesOfModelFile2);
$removedPossibleEvents = Tools::array_unique_complex($removedPossibleEvents);

$readme .= "Funktionen in Modellfile 1: ".count($allFuncNodesOfModelFile1)."\r\n";
$readme .= "Funktionen in Modellfile 2: ".count($allFuncNodesOfModelFile2)."\r\n";

if ( !empty($removedPossibleEvents) ) {
	$numOfAllFuncsInModelfile1 = count($allFuncNodesOfModelFile1);
	$numOfAllFuncsInModelfile2 = count($allFuncNodesOfModelFile2);
	$allFuncNodesOfModelFile1 = Tools::removeInvalidFunctions($allFuncNodesOfModelFile1, $removedPossibleEvents);
	$allFuncNodesOfModelFile2 = Tools::removeInvalidFunctions($allFuncNodesOfModelFile2, $removedPossibleEvents);
	$readme .= "Als Ereignis identifizierte Funktionen in Modellfile 1: ".($numOfAllFuncsInModelfile1-count($allFuncNodesOfModelFile1))."\r\n";
	$readme .= "Als Ereignis identifizierte Funktionen in Modellfile 2: ".($numOfAllFuncsInModelfile2-count($allFuncNodesOfModelFile2))."\r\n";
	$readme .= "Betrachtete Funktionen in Modellfile 1: ".count($allFuncNodesOfModelFile1)."\r\n";
	$readme .= "Betrachtete Funktionen in Modellfile 1: ".count($allFuncNodesOfModelFile2)."\r\n";
}

$readme .= "Gematchte Funktionen in Modellfile 1: ".count($allMatchedFuncNodesOfModelFile1)."\r\n";
$readme .= "Gematchte Funktionen in Modellfile 2: ".count($allMatchedFuncNodesOfModelFile2)."\r\n";

$numOfAllMatchedFuncs = count($allMatchedFuncNodesOfModelFile1) + count($allMatchedFuncNodesOfModelFile2);
$numOfAllFuncs = count($allFuncNodesOfModelFile1) + count($allFuncNodesOfModelFile2);
$repositorySimilarity = round(($numOfAllMatchedFuncs/$numOfAllFuncs)*100, 2);
$readme .= "\r\nAehnlichkeit der Modellrepositories: ".$repositorySimilarity."%\r\n";

if ( !empty($removedPossibleEvents) ) {
	$readme .= "\r\nWegen Verdacht auf Ereignis entfernte Funktionen:\r\n";
	foreach ($removedPossibleEvents as $funcNode) {
		$readme .= "   ".$funcNode->label." (".$funcNode->epc->name.")\r\n";
	}
}

if ( !empty($allMatchedFuncNodesOfModelFile1) && !empty($allMatchedFuncNodesOfModelFile1) ) {
	$readme .= "\r\nMatched Nodes of Model File 1:\r\n";
	foreach ($allMatchedFuncNodesOfModelFile1 as $funcDef) {
		$readme .= "   ".$funcDef["label"]." (".$funcDef["EPC_Name"].")\r\n";
	}

	$readme .= "\r\nMatched Nodes of Model File 2:\r\n";
	foreach ($allMatchedFuncNodesOfModelFile2 as $funcDef) {
		$readme .= "   ".$funcDef["label"]." (".$funcDef["EPC_Name"].")\r\n";
	}
}

$readme .= "\r\nNodes of Model File 1:\r\n";
foreach ($allFuncNodesOfModelFile1 as $funcDef) {
	$readme .= "   ".$funcDef["label"]." (".$funcDef["EPC_Name"].")\r\n";
}

$readme .= "\r\nNodes of Model File 2:\r\n";
foreach ($allFuncNodesOfModelFile2 as $funcDef) {
	$readme .= "   ".$funcDef["label"]." (".$funcDef["EPC_Name"].")\r\n";
}

$readme .= "\r\nEndzeit: ".date("d.m.Y H:i:s")."\r\n";
$readme .= "Dauer: ".$minutes." Min. ".$seconds." Sek.";
$fileGenerator->setFilename("ReadMe.txt");
$fileGenerator->setContent($readme);
$uri_readme_txt = $fileGenerator->execute(false);
// AUSGABEDATEIEN ERSTELLT

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nAnalysedateien wurden erfolgreich erstellt:\n\n");
print("ReadMe: ".$uri_readme_txt."\n");
print("HTML mit Mappings: ".$uri_html_analysis."\n");
print("CSV mit Analyseergebnissen: ".$uri_analysis_csv."\n\n");

print("Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n");

?>