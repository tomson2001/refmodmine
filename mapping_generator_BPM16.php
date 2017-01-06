<?php
/**
 * Wurde speziell fuer das Szenario im Rahmen des IWi Process Model Korpus entwickelt
 */
$start = time();
require 'autoloader.php';
include 'mapping_generator_settings.php';

// Ergebnisordner erstellen
$lastSlashPositionFile1 = strrpos(Config::MODEL_FILE_1, "/");
$lastSlashPositionFile2 = strrpos(Config::MODEL_FILE_2, "/");
$folderName = substr(Config::MODEL_FILE_1, $lastSlashPositionFile1+1, -5)."---".substr(Config::MODEL_FILE_2, $lastSlashPositionFile2+1, -5);
mkdir("files".DIRECTORY_SEPARATOR.$folderName);

print("Berechnung der Mappings...\n");

$allFuncNodesOfModelFile1 = array();
$allFuncNodesOfModelFile2 = array();

$allMatchedFuncNodesOfModelFile1 = array();
$allMatchedFuncNodesOfModelFile2 = array();

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$epc1 = new EPC($xml1, $xml_epc1["epcId"], $xml_epc1["name"]);

	foreach ( $epc1->functions as $funcLabel ) {
		$allFuncNodesOfModelFile1[$funcLabel] = true;
	}

	foreach ($xml2->xpath("//epc") as $xml_epc2) {
		$nameOfEPC2 = utf8_decode((string) $xml_epc2["name"]);
		$epc2 = new EPC($xml2, $xml_epc2["epcId"], $xml_epc2["name"]);

		foreach ( $epc2->functions as $funcLabel ) {
			$allFuncNodesOfModelFile2[$funcLabel] = true;
		}

		$html_analysis .= "<h3>".$nameOfEPC1." <=> ".$nameOfEPC2."</h3>";

		// Traces falls notwendig an die EPKs dranhaengen
		if ( $argv[1] == "--lcsot") {
			if ( is_string($traces[$nameOfEPC1]) || is_string($traces[$nameOfEPC2]) ) {

				// FORTSCHRITTSANZEIGE
				print("drop(".$nameOfEPC1.", ".$nameOfEPC2.")");
				$countCompletedCombinations++;

				if ( ($countCompletedCombinations/$countCombinations) >= $progress ) {
					print(" ".($progress*100)."% ");
					$progress += 0.1;
				}
				// ENDE DER FORTSCHRITTSANZEIGE

				continue;
			}
			$epc1->traces = $traces[$nameOfEPC1];
			$epc2->traces = $traces[$nameOfEPC2];
		}

		// Matrix berechnen
		$analysis_csv .= $nameOfEPC1.";".count($epc1->functions).";".count($epc1->events).";".$nameOfEPC2.";".count($epc2->functions).";".count($epc2->events).";";

		// Variablen-Initalisierung
		$mapping = null;

		// Auswahl der Mappings fuer die entsprechenden Aehnlichkeitsmasse
		switch ( $argv[1] ) {

			// Funktionen ueber Levenshtein, Konnektoren ueber Ein- und Ausgehende Kanten
			case "--fbse":
				$mapping = new LevenshteinWithStructuralMapping($epc1, $epc2);
				//$mapping->setParams(array('threshold_levenshtein' => 91));
				break;

				// Identity
			case "--ssbocan":
			case "--pocnae":
			case "--cf":
				$mapping = new LevenshteinMapping($epc1, $epc2);
				$mapping->setParams(array('threshold_levenshtein' => 50));
				//$mapping->setParams(array('threshold_levenshtein' => 100));
				break;

				// Funktionen ueber Levenshtein und Ein- und Ausgehende Kanten
			case "--amaged":
				$mapping = new LevenshteinWithContextMapping($epc1, $epc2);
				break;

				// kein Mapping
			case "--ts":
				$mapping = null;
				break;

				// Funktionen ueber Levenshtein
			default:
				$mapping = new LevenshteinMapping($epc1, $epc2);
				// Grenze auf 50% Aehnlichkeit setzen
				$mapping->setParams(array('threshold_levenshtein' => 90));
				break;
		}

		/**
		 * Angabe des Algorithmus, der fuer das Mapping verwendet werden soll: "Greedy", "Simple"
		 */
		$mapping->map("Greedy");
		
		// Export Mapping
		$genericMapping = $mapping->convertToGenericMapping();
		$genericMapping->exportRDF_BPMContest2015();

		$matchedFuncs = count($mapping->mapping);
		$analysis_csv .= $matchedFuncs.";";
		if ( min(count($epc1->functions), count($epc2->functions)) == 0 ) {
			$relationToMin = "n/a";
		} else {
			$relationToMin = str_replace(".", ",", round(($matchedFuncs/min(count($epc1->functions), count($epc2->functions)))*100, 2));
		}
		
		if ( max(count($epc1->functions), count($epc2->functions)) == 0 ) {
			$relationToMax = "n/a";
		} else {
			$relationToMax = str_replace(".", ",", round(($matchedFuncs/max(count($epc1->functions), count($epc2->functions)))*100, 2));
		}
				
		$analysis_csv .= $relationToMin.";".$relationToMax."\n";

		if ( $relationToMin != 0 ) $mapping->export2($folderName."/", false);
		$matrix = $mapping->getMatrix();

		// Schreiben der insgesamt gematchten Funktionen
		foreach ( $epc1->functions as $id => $label ) {
			if ( $mapping->mappingExistsFrom($id) ) $allMatchedFuncNodesOfModelFile1[$label] = true;
		}

		foreach ( $epc2->functions as $id => $label ) {
			if ( $mapping->mappingExistsTo($id) ) $allMatchedFuncNodesOfModelFile2[$label] = true;
		}

		// FORTSCHRITTSANZEIGE
		print(".");
		$countCompletedCombinations++;

		if ( ($countCompletedCombinations/$countCombinations) >= $progress ) {
			print(" ".($progress*100)."% ");
			$progress += 0.1;
		}
		// ENDE DER FORTSCHRITTSANZEIGE

	}

}
print(" done");

// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator("Mapping_Analysis.csv", $analysis_csv);
$fileGenerator->setPath($folderName);
$fileGenerator->setFilename("Mapping_Analysis.csv");
$uri_analysis_csv = $fileGenerator->execute(false);

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme .= "\nFunktionen in Modellfile 1: ".count($allFuncNodesOfModelFile1)."\r\n";
$readme .= "\nGematchte Funktionen in Modellfile 1: ".count($allMatchedFuncNodesOfModelFile1)."\r\n";
$readme .= "\nFunktionen in Modellfile 2: ".count($allFuncNodesOfModelFile2)."\r\n";
$readme .= "\nGematchte Funktionen in Modellfile 2: ".count($allMatchedFuncNodesOfModelFile2)."\r\n";

$readme .= "\nEndzeit: ".date("d.m.Y H:i:s")."\r\n";
$readme .= "Dauer: ".$minutes." Min. ".$seconds." Sek.";
$fileGenerator->setFilename("ReadMe.txt");
$fileGenerator->setContent($readme);
$uri_readme_txt = $fileGenerator->execute(false);
// AUSGABEDATEIEN ERSTELLT

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nAnalysedateien wurden erfolgreich erstellt:\n\n");
print("ReadMe: ".$uri_readme_txt."\n\n");
print("CSV mit Analyseergebnissen: ".$uri_analysis_csv."\n");

print("Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n");

?>