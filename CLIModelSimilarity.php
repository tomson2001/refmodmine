<?php
$start = time();
require 'autoloader.php';

print("\n--------------------------------------------------------------------\n RefMod-Miner (PHP) - Business Process Model Similarity Measuerment \n--------------------------------------------------------------------\n");

$similarityMeasures = array(
		"ssbocan" => "Similarity Score Based On Common Activity Names",
		"lms"  	  => "Label Matching Similarity",
		"fbse"    => "Feature Based Similarity Estimation",
		"pocnae"  => "Percentage Of Common Nodes And Edges",
		"geds"	  => "Graph Edit Distance Similarity",
		"amaged"  => "Activity Matching And Graph Edit Distance",
		"cf"	  => "Causal Footprints"
);

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) || !isset($argv[4]) ) {
	exit("   Please provide the following parameters:\n
   measure=
      ssbocan       ".$similarityMeasures["ssbocan"]."
      lms           ".$similarityMeasures["lms"]."
      fbse          ".$similarityMeasures["fbse"]."
      pocnae        ".$similarityMeasures["pocnae"]."
      geds          ".$similarityMeasures["geds"]."
      amaged        ".$similarityMeasures["amaged"]."
      cf            ".$similarityMeasures["cf"]."\n
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
$measure = substr($argv[1], 8,  strlen($argv[1]));
$input   = substr($argv[2], 6,  strlen($argv[2]));
$output  = substr($argv[3], 7,  strlen($argv[3]));
$email   = substr($argv[4], 13, strlen($argv[4]));

print("
measure: ".$measure."
input: ".$input."
output: ".$output."
notification: ".$email."

checking input parameters ...
");

// Check measure
if ( array_key_exists($measure, $similarityMeasures) ) {
	print "  measure ... ok\n";
} else {
	exit("  measure ... failed (measure does not exist)");
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
	print "  notification ... ok (mail to ".$email.")\n";
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
print("Similarity measure: ".$similarityMeasures[$measure]."\n\n");

// ReadMe.txt erzeugen
$readme = "--------------------------------------------------------------------------\r\n";
$readme .= " RMMaaS - Business Process Model Similarity Measuerment\r\n";
$readme .= "--------------------------------------------------------------------------\r\n\r\n";
$readme .= "Log:\r\n";
$readme .= " - Similarity Measure: ".$similarityMeasures[$measure]."\r\n";
$readme .= " - Model file:  ".$input." (".$modelsInFile1." models)\r\n";
$readme .= " - Number of model pairs: ".$countCombinations;

print("Calculate similarity values ...\n");

// Prepare Similarity-Matrix CSV
$similarity_matrix_csv = "";
foreach ($xml2->xpath("//epc") as $xml_epc2) {
	$nameOfEPC2 = utf8_decode((string) $xml_epc2["name"]);
	$similarity_matrix_csv .= ";".$nameOfEPC2;
}
$similarity_matrix_csv .= "\n";

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
	
	$similarity_matrix_csv .= $nameOfEPC1;

	foreach ($xml2->xpath("//epc") as $xml_epc2) {
		$nameOfEPC2 = utf8_decode((string) $xml_epc2["name"]);
		$epc2 = new EPC($xml2, $xml_epc2["epcId"], $xml_epc2["name"]);

		foreach ( $epc2->functions as $funcLabel ) {
			$allFuncNodesOfModelFile2[$funcLabel] = true;
		}

		// Traces falls notwendig an die EPKs dranhaengen
		if ( $measure == "lcsot") {
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
		
		// Berechnung des Aehnlichkeitsma�es
		$simMeasure = null;
		eval("\$simMeasure = new ".str_replace(" ", "", trim($similarityMeasures[$measure]))."(\$mapping);");
		$similarityValue = $simMeasure->calculate();
		$similarity_matrix_csv .= ";".$similarityValue;

		// FORTSCHRITTSANZEIGE
		print(".");
		$countCompletedCombinations++;

		if ( ($countCompletedCombinations/$countCombinations) >= $progress ) {
			print(" ".($progress*100)."% ");
			$progress += 0.1;
		}
		// ENDE DER FORTSCHRITTSANZEIGE

	}
	$similarity_matrix_csv .= "\n";

}
print(" done");

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme .= "\n - Functions in model file: ".count($allFuncNodesOfModelFile1)."\r\n";
$readme .= " - Matched Functions in model file: ".count($allMatchedFuncNodesOfModelFile1)."\r\n\r\n";
$readme .= "Duration: ".$minutes." Min. ".$seconds." Sec.\r\n\r\n";

// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator($output, $similarity_matrix_csv);
$fileGenerator->setPathFilename($output);
$fileGenerator->setContent($similarity_matrix_csv);
$uri_sim_matrix_csv = $fileGenerator->execute(false);
// AUSGABEDATEIEN ERSTELLT

// Extract session ID from uri
$sid = $uri_sim_matrix_csv;
$sid = str_replace("workspace/", "", $sid);
$pos = strpos($sid, "/");
$sid = $pos ? substr($sid, 0, $pos) : $sid;
$readme .= "Your workspace: ".Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLIModelSimilarityNotification($email, $readme);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n");
print("Similarity matrix for ".$input." successfully created (".$uri_sim_matrix_csv.").\n\n");

Logger::log($email, "CLIModelSimilarity finished: measure=".$measure." input=".$input." output=".$output, "ACCESS");
?>