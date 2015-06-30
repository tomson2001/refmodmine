<?php

// @TODO NOT TESTED AND NOT FINISHED!

$start = time();
require 'autoloader.php';

print("\n--------------------------------------------------------------------\n RefMod-Miner (PHP) - Business Process Model Matcher \n--------------------------------------------------------------------\n");

$algorithms = array(
		"ssbocan" => "Similarity Score Based On Common Activity Names",
		"lms"  	  => "Label Matching Similarity",
		"fbse"    => "Feature Based Similarity Estimation",
		"pocnae"  => "Percentage Of Common Nodes And Edges",
		"geds"	  => "Graph Edit Distance Similarity",
		"amaged"  => "Activity Matching And Graph Edit Distance",
		"cf"	  => "Causal Footprints",
		"nscm"	  => "N-Ary Semantic Cluster Matching"
);

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) || !isset($argv[4]) ) {
	exit("   Please provide the following parameters:\n
   algorithm=
      ssbocan       ".$similarityMeasures["ssbocan"]."
      lms           ".$similarityMeasures["lms"]."
      fbse          ".$similarityMeasures["fbse"]."
      pocnae        ".$similarityMeasures["pocnae"]."
      geds          ".$similarityMeasures["geds"]."
      amaged        ".$similarityMeasures["amaged"]."
      cf            ".$similarityMeasures["cf"]."
      nscm          ".$similarityMeasures["nscm"]."\n
   input=           path to input epml
   output=          path to output file
   notification=
      no
      [E-Mail adress]

   please user the correct order!
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$algorithm = substr($argv[1], 8, strlen($argv[1]));
$input     = substr($argv[2], 6, strlen($argv[2]));
$output    = substr($argv[3], 7, strlen($argv[3]));
$email     = substr($argv[4], 13, strlen($argv[4]));

print("
measure: ".$measure."
input: ".$input."
output: ".$output."
notification: ".$email."

checking input parameters ...
");

// Check algorithm
if ( array_key_exists($algorithm, $algorithms) ) {
	print "  algorithm ... ok\n";
} else {
	exit("  algorithm ... failed (algorithm does not exist)");
}

// Check input
if ( file_exists($input) ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file does not exist)");
}

// Check notification
$doNotify = true;
if ( empty($email) ) {
	$doNotify = false;
	print "  notification ... ok (notification disabled)\n";
} else {
	print "  input ... ok (mail to ".$email.")\n";
}

// Laden der Modelldateien
$content_file_1 = file_get_contents($input);
$xml1 = new SimpleXMLElement($content_file_1);
$content_file_2 = file_get_contents($input);
$xml2 = new SimpleXMLElement($content_file_2);

// Vorbereitung der Forschrittsanzeige
$modelsInFile1 = count($xml1->xpath("//epc"));
$modelsInFile2 = count($xml2->xpath("//epc"));
$numOfAllModels = $modelsInFile1 + $modelsInFile2;
$countCombinations = $modelsInFile1 * $modelsInFile2;
$countCompletedCombinations = 0;
$progress = 0.1;

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("\nNumber of models: ".count($xml1->xpath("//epc"))."\n");
print("Number of model permutations: ".$countCombinations."\n");
print("Matching algorithm: ".$algorithms[$algorithm]."\n\n");

// ReadMe.txt erzeugen
$readme = "--------------------------------------------------------------------\r\n";
$readme .= " RefMod-Miner as a Service - Business Process Model Matcher\r\n";
$readme .= "--------------------------------------------------------------------\r\n\r\n";
$readme .= "Log:\r\n";
$readme .= " - Matching algorithm: ".$algorithms[$algorithm]."\r\n";
$readme .= " - Model file:  ".$input." (".$modelsInFile1." models)\r\n";
$readme .= " - Number of model permutations: ".$countCombinations."\r\n";
$readme .= " - Start: ".date("d.m.Y H:i:s")."\r\n\r\n";

print("Calculate matchings ...\n");

$allFuncNodesOfModelFile1 = array();
$allFuncNodesOfModelFile2 = array();

$allMatchedFuncNodesOfModelFile1 = array();
$allMatchedFuncNodesOfModelFile2 = array();

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = (string) $xml_epc1["name"];
	$epc1 = new EPC($xml1, $xml_epc1["epcId"], $xml_epc1["name"]);

	foreach ( $epc1->functions as $funcLabel ) {
		$allFuncNodesOfModelFile1[$funcLabel] = true;
	}

	foreach ($xml2->xpath("//epc") as $xml_epc2) {
		$nameOfEPC2 = (string) $xml_epc2["name"];
		$epc2 = new EPC($xml2, $xml_epc2["epcId"], $xml_epc2["name"]);

		foreach ( $epc2->functions as $funcLabel ) {
			$allFuncNodesOfModelFile2[$funcLabel] = true;
		}

		// Variablen-Initalisierung
		$mapping = null;

		// Auswahl der Mappings fuer die entsprechenden Aehnlichkeitsmasse
		switch ( $measure ) {

			// Funktionen ueber Levenshtein, Konnektoren ueber Ein- und Ausgehende Kanten
			case "fbse":
				$mapping = new LevenshteinWithStructuralMapping($epc1, $epc2);
				//$mapping->setParams(array('threshold_levenshtein' => 91));
				break;

				// Identity
			case "ssbocan":
			case "pocnae":
			case "cf":
				$mapping = new LevenshteinMapping($epc1, $epc2);
				$mapping->setParams(array('threshold_levenshtein' => 50));
				//$mapping->setParams(array('threshold_levenshtein' => 100));
				break;

				// Funktionen ueber Levenshtein und Ein- und Ausgehende Kanten
			case "amaged":
				$mapping = new LevenshteinWithContextMapping($epc1, $epc2);
				break;

				// kein Mapping
			case "ts":
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

		$matchedFuncs = count($mapping->mapping);
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

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme .= "\nFunctions in model file: ".count($allFuncNodesOfModelFile1)."\r\n";
$readme .= "\nMatched Functions in model file: ".count($allMatchedFuncNodesOfModelFile1)."\r\n";

$readme .= "\nEnd: ".date("d.m.Y H:i:s")."\r\n";
$readme .= "Duration: ".$minutes." Min. ".$seconds." Sec.";

// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator("Mapping_Analysis.csv", $analysis_csv);
$fileGenerator->setFilename("ReadMe.txt");
$fileGenerator->setContent($readme);
$uri_readme_txt = $fileGenerator->execute(false);
// AUSGABEDATEIEN ERSTELLT

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nAnalysedateien wurden erfolgreich erstellt:\n\n");
print("ReadMe: ".$uri_readme_txt."\n\n");

print("HTML mit Mappings: ".$uri_html_analysis."\n");
print("CSV mit Analyseergebnissen: ".$uri_analysis_csv."\n");

print("Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n");




?>