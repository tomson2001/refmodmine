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

print("Clone detection... \n");

// Anzahl Tasks berechnen
$tasks = $countCombinations;
if ( in_array("--onefile", $argv) ) {
	if ( ($modelsInFile1 % 2) == 0 ) {
		$tasks = ($countCombinations/2)-($modelsInFile1/2);
	} else {
		$tasks = $modelsInFile1 * floor($modelsInFile1);
	}
	
}
$progressBar = new CLIProgressbar($tasks, 0.1);
$finishedTasks = 0;

$identicalCloneClusters = array();
$approximateClones = array();
$approximateCloneThreshold = 90;

if ( in_array("--onefile", $argv) ) {
	$i = 0;
	$j = 0;
	$max = $modelsInFile1;
	while ( $i < $max ) {
		while ( $j < $max ) {
			if ( $i != $j ) {
				$epc1 = $epcsOfFile1[$i];
				$epc2 = $epcsOfFile1[$j];
				$sim = $epc1->compareTo($epc2);
				if ( $sim == 100 ) {
					$added = false;
					foreach ( $identicalCloneClusters as $index => $cloneCluster ) {
						if ( $cloneCluster->contains($epc1) || $cloneCluster->contains($epc2) ) {
							$identicalCloneClusters[$index]->add($epc1);
							$identicalCloneClusters[$index]->add($epc2);
							$added = true;
							break;
						}
					}
					if ( !$added ) {
						$newCluster = new CloneEPCCluster();
						$newCluster->add($epc1);
						$newCluster->add($epc2);
						array_push($identicalCloneClusters, $newCluster);
					}
				} elseif ( $sim >= 90 ) {
					array_push($approximateClones, array($epc1, $epc2, $sim));
				}
				$finishedTasks++;
				$progressBar->run($finishedTasks);
			}
			$j++;
		}
		$i++;
		$j = $i;
	}
} else {
	foreach ( $epcsOfFile1 as $epc1 ) {
		foreach ( $epcsOfFile2 as $epc2 ) {
			$sim = $epc1->compareTo($epc2);
			if ( $sim == 100 ) {
				$added = false;
				foreach ( $identicalCloneClusters as $index => $cloneCluster ) {
					if ( $cloneCluster->contains($epc1) || $cloneCluster->contains($epc2) ) {
						$identicalCloneClusters[$index]->add($epc1);
						$identicalCloneClusters[$index]->add($epc2);
						$added = true;
						break;
					}
				}
				if ( !$added ) {
					$newCluster = new CloneEPCCluster();
					$newCluster->add($epc1);
					$newCluster->add($epc2);
					array_push($identicalCloneClusters, $newCluster);
				}
			} elseif ( $sim >= 90 ) {
				array_push($approximateClones, array($epc1, $epc2, $sim));
			}
			$finishedTasks++;
			$progressBar->run($finishedTasks);
		}
	}
}

/**
 * Approximate Cluster Clones identifizieren
 */ 
$approximateClusterClones = array();
$approximateClusterClonesEPCs = array();
$i = 0;
$j = 0;
$max = count($identicalCloneClusters);
while ( $i < $max ) {
	while ( $j < $max ) {
		if ( $i != $j ) {
			$epc1 = $identicalCloneClusters[$i]->epcs[0];
			$epc2 = $identicalCloneClusters[$j]->epcs[0];
			$sim = $epc1->compareTo($epc2);
			if ( $sim >= 90 ) {
				array_push($approximateClusterClones, array($i+1, $j+1, $sim));
				foreach ( $identicalCloneClusters[$i]->epcs as $epc ) {
					array_push($approximateClusterClonesEPCs, $epc);
				}
				foreach ( $identicalCloneClusters[$j]->epcs as $epc ) {
					array_push($approximateClusterClonesEPCs, $epc);
				}
			}
		}
		$j++;
	}
	$i++;
	$j = $i;
}

// Approximate Clones, die in $approximateClusterClonesEPCs enthalten sind entfernen
foreach ( $approximateClones as $index => $approximateClone ) {
	$epc1 = $approximateClone[0];
	$epc2 = $approximateClone[1];
	$found1 = false;
	$found2 = false;
	foreach ( $approximateClusterClonesEPCs as $epc ) {
		if ( $epc->name == $epc1->name && $epc->id == $epc1->id ) $found1 = true;
		if ( $epc->name == $epc2->name && $epc->id == $epc2->id ) $found2 = true;
	}
	if ( $found1 && $found2 ) {
		unset($approximateClones[$index]);
	}
}


// Sortierfunktion fuer die Cluster nach Anzahl der enthaltenen Modelle
function compareClusterSize($cluster1, $cluster2) {
	$size1 = count($cluster1->epcs);
	$size2 = count($cluster2->epcs);
	if ( $size1 == $size2 ) return 0;
	return ($size1 < $size2) ? 1: -1;
}

usort($identicalCloneClusters, 'compareClusterSize');

// Sortierfunktion fuer die approximate Clones anhand des sim-Wertes
function compareAppromiateClones($aclone1, $aclone2) {
	//if ( $aclone1[2] == $aclone2[2] ) return 0;
	//return ($aclone1[2] < $aclone2[2]) ? 1: -1;
	return strcmp($aclone1[0]->name, $aclone2[0]->name);
}

usort($approximateClones, 'compareAppromiateClones');

// Aufbereiten einer CSV
$clone_detection_csv = "Model-File 1:;".substr(Config::MODEL_FILE_1, $lastSlashPositionFile1+1, -5)."\n";
$clone_detection_csv .= in_array("--onefile", $argv) ? "\n" : "Model-File 2:;".substr(Config::MODEL_FILE_2, $lastSlashPositionFile2+1, -5)."\n\n";
$clone_detection_csv .= "Identical Clones\n";
foreach ( $identicalCloneClusters as $index => $cloneCluster ) {
	$clone_detection_csv .= ";Cluster ".($index+1)." (".count($cloneCluster->epcs)." EPKs)\n";
	foreach ( $cloneCluster->epcs as $epc ) {
		$clone_detection_csv .= ";;".$epc->modelPath."\n";
	}
	$clone_detection_csv .= "\n";
}

if ( !empty($approximateClusterClones) ) $clone_detection_csv .= "Approximate Clones between clusters\n";
foreach ( $approximateClusterClones as $approximateClusterClone ) {
	$clone_detection_csv .= ";Cluster ".$approximateClusterClone[0].";Cluster ".$approximateClusterClone[1].";".$approximateClusterClone[2]."\n";
}
if ( !empty($approximateClusterClones) ) $clone_detection_csv .= "\n";

$clone_detection_csv .= "Approximate Clones (at least ".$approximateCloneThreshold."% common edges)\n";
foreach ( $approximateClones as $clonedPair ) {
	$sim = is_int($clonedPair[2]) ? $clonedPair[2].",00" : str_replace(".", ",", $clonedPair[2]);
	$clone_detection_csv .= ";".$clonedPair[0]->modelPath.";".$clonedPair[1]->modelPath.";".$sim."\n";
}
print("\ndone");


// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator("Clones.csv", $clone_detection_csv);
$fileGenerator->setPath($folderName);
$fileGenerator->setFilename("Clones.csv");
$uri_clone_detection_csv = $fileGenerator->execute(false);

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nAnalysedateien wurden erfolgreich erstellt:\n\n");
print("CSV mit Identical und Approximate Clones: ".$uri_clone_detection_csv."\n\n");

print("Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n");

?>