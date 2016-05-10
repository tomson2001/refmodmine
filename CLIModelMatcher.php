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
      ssbocan       ".$algorithms["ssbocan"]."
      lms           ".$algorithms["lms"]."
      fbse          ".$algorithms["fbse"]."
      pocnae        ".$algorithms["pocnae"]."
      geds          ".$algorithms["geds"]."
      amaged        ".$algorithms["amaged"]."
      cf            ".$algorithms["cf"]."
      nscm          ".$algorithms["nscm"]."\n
   input=           path to input epml
   output=          path to output file
   format=          zip (including rdf and txt matchings) | xml (rmm)
   notification=
      no
      [E-Mail adress]

   please user the correct order!
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$algorithm = substr($argv[1], 10, strlen($argv[1]));
$input     = substr($argv[2], 6, strlen($argv[2]));
$output    = substr($argv[3], 7, strlen($argv[3]));
$format    = substr($argv[4], 7,  strlen($argv[4]));
$email     = substr($argv[5], 13, strlen($argv[5]));

print("
measure: ".$algorithm."
input: ".$input."
output: ".$output."
format: ".$format."
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

// Vorbereitung der Forschrittsanzeige
$modelsInFile1 = count($xml1->xpath("//epc"));
$numOfAllModels = $modelsInFile1;
$countCombinations = (($modelsInFile1 * $modelsInFile1)/2)-($modelsInFile1/2);

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("\nNumber of models: ".count($xml1->xpath("//epc"))."\n");
print("Number of model permutations: ".$countCombinations);

// ReadMe.txt erzeugen
$readme = "--------------------------------------------------------------------------\r\n";
$readme .= " RMMaaS - Process Model Matcher\r\n";
$readme .= "--------------------------------------------------------------------------\r\n\r\n";
$readme .= "Log:\r\n";
$readme .= " - Model file:  ".$input." (".$modelsInFile1." models)\r\n";
$readme .= " - Number of model pairs: ".$countCombinations."\r\n";
$readme .= " - Matcher of similarity measure: ".$algorithms[$algorithm];

$generatedFiles = array();
$generatedRDFs = array();

print("\n\nCalculate matchings ...\n");

$i = 0;
$j = 0;

// Vorbereitung der Forschrittsanzeige
$max = $countCombinations;
$modelCounter = 0;
$progressBar = new CLIProgressbar($max, 0.1);

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	
	$i++;
	$nameOfEPC1 = (string) $xml_epc1["name"];
	$epc1 = new EPC($xml1, $xml_epc1["epcId"], $xml_epc1["name"]);

	foreach ($xml1->xpath("//epc") as $xml_epc2) {
		
		$j++;
		if ( $j < $i ) continue;
		$nameOfEPC2 = (string) $xml_epc2["name"];
		$epc2 = new EPC($xml1, $xml_epc2["epcId"], $xml_epc2["name"]);

		// Variablen-Initalisierung
		$mapping = null;

		// Auswahl der Mappings fuer die entsprechenden Aehnlichkeitsmasse
		switch ( $algorithm ) {

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
				
			case "lms":
				$mapping = new LevenshteinMapping($epc1, $epc2);
				$mapping->setParams(array('threshold_levenshtein' => 100));
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
		$mapping->deleteDummyTransitions();
		$genericMapping = $mapping->convertToGenericMapping();

		$file = $genericMapping->exportTXT_BPMContest2013($epc1, $epc2);
		array_push($generatedFiles, $file);
		
		$file = $genericMapping->exportRDF_BPMContest2015();
		array_push($generatedFiles, $file);
		array_push($generatedRDFs, $file);
		
		// FORTSCHRITTSANZEIGE
		$modelCounter++;
		$progressBar->run($modelCounter);
		// ENDE DER FORTSCHRITTSANZEIGE

	}
	
	$j = 0;

}
print(" done");

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme .= "\r\n\r\nEnd: ".date("d.m.Y H:i:s")."\r\n";
$readme .= "Duration: ".$minutes." Min. ".$seconds." Sec.";
$fileGenerator = new FileGenerator("ReadMe.txt", $readme);
$fileGenerator->setFilename("ReadMe.txt");
$fileGenerator->setContent($readme);
$uri_readme_txt = $fileGenerator->execute();
array_push($generatedFiles, $uri_readme_txt);
// AUSGABEDATEIEN ERSTELLT

if ( $format == "zip" ) {

	// ZIP ALL FILES
	print("\n\nZip files ... ");
	$zip = new ZipArchive();
	if ( $zip->open($output, ZipArchive::CREATE) ) {
	    foreach ( $generatedFiles as $filename ) {
			$pos = strrpos($filename, "/");
			$file = substr($filename, $pos+21);
			$zip->addFile($filename, $file);		
		}
		$zip->close();
		foreach ( $generatedFiles as $filename ) {
			unlink($filename);
		}
		$numFiles = count($generatedFiles);
		print("done (#files: ".$numFiles.", status".$zip->status.")");
	} else {
		exit("\nCannot open <".$output.">. Error creating zip file.\n");
	}
	// ZIP COMPLETED

} elseif ( $format == "xml" ) {
	
	// CREATE XML from single RDFs by RefMod-Miner Matching Converter
	print("\n\nCreating aggregated matching XML file . ");
	
	$_POST["matchings"] = implode(",", $generatedRDFs);
	$_POST["model_set"] = $input;
	$_POST["output_file"] = $output;
	$actionHandler = new WorkspaceActionHandler();
	$actionHandler->run("CONVERT_MATCHING");
		
	foreach ( $generatedFiles as $filename ) {
		unlink($filename);
	}
	
 	sleep(1); print("."); sleep(1); print("."); sleep(1);
	
	print(" done");
}

// Extract session ID from uri
$sid = $output;
$sid = str_replace("workspace/", "", $sid);
$pos = strpos($sid, "/");
$sid = $pos ? substr($sid, 0, $pos) : $sid;
$readme .= "Your workspace: ".Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLIModelMatcherNotification($email, $readme);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n");
print("Process Model Matching finished successfully.\n");

Logger::log($email, "CLIModelMatcher finished: algorithm=".$algorithm." input=".$input." output=".$output." format=".$format, "ACCESS");
?>