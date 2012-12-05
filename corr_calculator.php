<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Correlation Calculator \n-------------------------------------------------\n\n");

$similarityMeasures = array(
		"ssbocan" 	=> "similarity score based on common activity names",
		"lms"		=> "label matching similarity",
		"fbse"    	=> "feature based similarity estimation",
		"pocnae"	=> "percentage of common nodes and edges",
		"geds"		=> "graph edit distance similarity",
		"amaged"  	=> "activity matching and graph edit distance",
		"cf"		=> "causal footprints"
);

$flippedSimilarityMeasures = array_flip($similarityMeasures);

$corrArr = array();
$foundMeasures = array();
$empiricValueSeries = array();

print("Quelldatei: ".Config::CORR_INPUT_FILE."\n\n");

// Laden des CSV-File
print("CSV wird geladen... ");
if (($handle = fopen(Config::CORR_INPUT_FILE, "r")) !== FALSE) {
	print("ok\n");
	
	// Header leaden
	$header = fgetcsv($handle, 0, ";");
	$fields = count($header);

	print("Erkennung der Aehnlichkeitsmasse... ");
	// Identifizierung der im CSV enthaltenen Aehnlichkeitsmaﬂe
	for ( $fieldNum = 0; $fieldNum < $fields; $fieldNum++ ) {
		if ( array_key_exists(strtolower($header[$fieldNum]), $similarityMeasures) ) {
			$foundMeasures[$fieldNum] = strtoupper($header[$fieldNum]);
		} elseif ( array_key_exists(strtolower($header[$fieldNum]), $flippedSimilarityMeasures) ) {
			$foundMeasures[$fieldNum] = strtoupper($flippedSimilarityMeasures[strtolower($header[$fieldNum])]);
		}
	}
	
	// Header in die Korrelationsmatrix schreiben und Empirische Reihen erstellen
	foreach ( $foundMeasures as $fieldNum => $measure ) {
		$corrArr[$measure] = array();
		$empiricValueSeries[$fieldNum] = new EmpiricValueSeries($measure);
		print($measure." ");
	}
	print("\n\n");
	if ( empty($foundMeasures) ) {
		exit("Es konnte keine Aehnlichkeitsmasse gefunden werden.\n\n");
	}
	
	// Auslesen der empirischen Werte
	print("Einlesen empirische Wertereihen... ");
	$counter = 0;
	while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
		foreach ( $foundMeasures as $fieldNum => $measure ) {
			$empiricValueSeries[$fieldNum]->add($data[$fieldNum]);
		}
		$counter++;
	}
	print($counter."\n");
	
	// Berechnung der empirischen Korrelationsmatrix
	print("Berechnung der Korrelationsmatrix");
	// immer nach zwei Aehnlichkeitsmassen einen Fortschrittspunkt anzeigen 
	$printPoint = true;
	foreach ( $foundMeasures as $fieldNum1 => $measure1 ) {
		foreach ( $foundMeasures as $fieldNum2 => $measure2 ) {
			// Berechnung des empirischen Korrelationskoeffizienten zwischen Measure1 und Measure2
			$calculator = new EmpiricCorrelationCalculator($empiricValueSeries[$fieldNum1], $empiricValueSeries[$fieldNum2]);
			$corrArr[$measure1][$fieldNum2] = round($calculator->getCorrelation(), 2);
			if ( $printPoint ) {
				print(".");
			}
			$printPoint = !$printPoint;
		}
	}
	print(" ok\n");
	
	// CSV-File schliessen
	fclose($handle);
	
	// Schreiben der Korrelationsmatrix als CSV
	print("Generiere CSV... ");
	$csv = "";
	foreach ( $foundMeasures as $measure ) {
		$csv .= ";".$measure;
	}
	
	foreach ( $corrArr as $measure => $corrValues ) {
		$csv .= "\n".$measure;
		foreach ( $corrValues as $measureIndex => $corrValue ) {
			$csv .= ";".str_replace(".", ",", $corrValue);
		}
	}
	
	$fileGenerator = new FileGenerator("CorrelationMatrix.csv", $csv);
	$uri_corr_matrix = $fileGenerator->execute();
	print("ok\n\n");
	print("Ergebnisdatei: ".$uri_corr_matrix."\n\n");
	
} else {
	print("failure\n\n");
}



?>