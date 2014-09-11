<?php
/**
 * Erkennt Modellklone
 *
 * Anderforderungen an EPKs: zusammenhaengend
 */
$start = time();
require 'autoloader.php';
include 'model_clone_detector_settings.php';

// Ergebnisordner erstellen
$lastSlashPositionFile1 = strrpos(Config::MODEL_FILE_1, "/");
$lastSlashPositionFile2 = in_array("--onefile", $argv) ? $lastSlashPositionFile1 : strrpos(Config::MODEL_FILE_2, "/");
$file1 = Config::MODEL_FILE_1;
$file2 = in_array("--onefile", $argv) ? $file1 : Config::MODEL_FILE_2;
$folderName = "CloneDetection_".substr($file1, $lastSlashPositionFile1+1, -5)."---".substr($file2, $lastSlashPositionFile2+1, -5);
mkdir("files".DIRECTORY_SEPARATOR.$folderName);

/**
 * Daten laden
 */
$modelCount = 0;
$tasks = in_array("--onefile", $argv) ? $numOfAllModels/2 : $numOfAllModels;
$progressBar = new CLIProgressbar($tasks, 0.1);

// Vorbereitung der EPKs der ersten File
print("\nLaden der Modelldaten... \n");
$epcsOfFile1 = array();
foreach ($xml1->xpath("//epc") as $xml_epc1) {
	// EPK-Objekt erstellen
	$epc = new EPC($xml1, $xml_epc1["epcId"], $xml_epc1["name"]);
	array_push($epcsOfFile1, $epc);
	$modelCount++;
	$progressBar->run($modelCount);
}

$epcsOfFile2 = array();
if ( !in_array("--onefile", $argv) ) {
	foreach ($xml2->xpath("//epc") as $xml_epc2) {
		// EPK-Objekt erstellen
		$epc = new EPC($xml2, $xml_epc2["epcId"], $xml_epc2["name"]);
		array_push($epcsOfFile2, $epc);
		$modelCount++;
		$progressBar->run($modelCount);
	}
}
print("\ndone\n\n");

/**
 * Vorbereitung des Clone Detection
 */
$numOfCoresToWorkOn = Config::NUM_CORES_TO_WORK_ON;
print("Preparing Clone Detection on ".$numOfCoresToWorkOn." Cores... \n");

$progressBar = new CLIProgressbar($countCombinations, 0.1);

$splitCount = round($tasks/$numOfCoresToWorkOn);
$nextSplit = $splitCount;
$cloneDetectionCombinations = array();
$cloneDetectionCombinations[0] = array();
$part = 0;
$currentTask = 0;

if ( in_array("--onefile", $argv) ) {
	$i = 0;
	$j = 0;
	$max = $modelsInFile1;
	while ( $i < $max ) {
		while ( $j < $max ) {
			if ( $i != $j ) {
				$currentTask++;
				if ( $currentTask == $nextSplit && $part < $numOfCoresToWorkOn-1 ) {
					$part++;
					$cloneDetectionCombinations[$part] = array();
					$nextSplit += $splitCount;
				}
				array_push($cloneDetectionCombinations[$part], array($epcsOfFile1[$i], $epcsOfFile1[$j]));
				$progressBar->run($currentTask);
			}
			$j++;
		}
		$i++;
		$j = $i;
	}
} else {
	foreach ( $epcsOfFile1 as $epc1 ) {
		foreach ( $epcsOfFile2 as $epc2 ) {
			$currentTask++;
			if ( $currentTask == $nextSplit && $part < $numOfCoresToWorkOn-1 ) {
				$part++;
				$cloneDetectionCombinations[$part] = array();
				$nextSplit += $splitCount;
			}
			array_push($cloneDetectionCombinations[$part], array($epc1, $epc2));
			$finishedTasks++;
			$progressBar->run($finishedTasks);
		}
	}
}
print("\ndone\n\n");

print("Proceeding Clone Detection... \n");

$progressBar = new CLIProgressbar($countCombinations, 0.1);

$maxThread = 0;
$thread = array();
foreach ( $cloneDetectionCombinations as $threadID => $cloneDetectionCombinationsPart ) {
	$thread[$threadID+1] = new MultiThreadCloneDetectionOperation($cloneDetectionCombinationsPart, $folderName);
	$thread[$threadID+1]->start();
	$maxThread = $threadID+1;
}

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
		$currentThread++;
	}
}

print("\ndone\n\n");

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

print("Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n");

?>