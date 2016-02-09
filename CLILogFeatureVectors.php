<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Log Feature Vectors \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) ) {
	exit("   Please provide the following parameters:\n
   input=           	 path to input mxml(s)
   output=          	 path to output csv
   notification=
      no
      [E-Mail adress]

   (aggregate) 	          duplicated traces are removed
   (with_event_counter)   counts the occurence of each events for each case
   (with_file_assignment) adds a columns with the filename
   

   please user the correct order!
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$input   = substr($argv[1], 6,  strlen($argv[1]));
$output   = substr($argv[2], 7,  strlen($argv[2]));
$email   = substr($argv[3], 13, strlen($argv[3]));

$aggregate = (in_array("aggregate", $argv)) ? true : false;
$withEventCounter = (in_array("with_event_counter", $argv)) ? true : false;
$withFileAssignment = (in_array("with_file_assignment", $argv)) ? true : false;

print("
input: ".$input."
output: ".$output."
notification: ".$email."

checking input parameters ...
");

// Check input
$input = explode(",", $input);
$inputOK = true;
foreach ( $input as $inputFilename ) {
	if ( !file_exists($inputFilename) ) $inputOK = false;
}
if ( $inputOK ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file(s) do not exist)\n\n");
}

$readme = "";

// Check aggregate
if ( $aggregate ) {
	$info = "  aggregate ... yes\n";
	print($info);
	$readme .= $info;
} else {
	$info = "  aggregate ... no\n";
	print($info);
	$readme .= $info;
}

// Check with_event_counter
if ( $withEventCounter ) {
	$info = "  with_event_counter ... yes\n";
	print($info);
	$readme .= $info;
} else {
	$info = "  with_event_counter ... no\n";
	print($info);
	$readme .= $info;
}

// Check with_file_assignment
if ( $withFileAssignment ) {
	$info = "  with_file_assignment ... yes\n";
	print($info);
	$readme .= $info;
} else {
	$info = "  with_file_assignment ... no\n";
	print($info);
	$readme .= $info;
}

// Check notification
$doNotify = true;
if ( empty($email) || $email == "no" ) {
	$doNotify = false;
	print "  notification ... ok (notification disabled)\n";
} else {
	print "  notification ... ok (mail to ".$email.")\n";
}

foreach ( $input as $inputFilename ) {
	$mxml_content = file_get_contents($inputFilename);
	$xml = new SimpleXMLElement($mxml_content);
	$containedProcessInstances = count($xml->xpath("//ProcessInstance"));
	
	$info = "\nNum Traces in ".$inputFilename.": ".$containedProcessInstances;
	print($info);
	$readme .= $info;
}

$loader = null;
$processLog = null;
foreach ( $input as $inputFilename ) {
	$mxml_content = file_get_contents($inputFilename);
	$xml = new SimpleXMLElement($mxml_content);
	
	$startLoadingTime = time();
	if ( is_null($loader) ) {
		$loader = new MXMLLoader($inputFilename, $xml, $aggregate);
		$processLog = $loader->load();
	} else {
		$loader->addLoadMXML($inputFilename, $xml);
	}

	$loadingDuration = time() - $startLoadingTime;
	$seconds = $loadingDuration % 60;
	$minutes = floor($loadingDuration / 60);
	
	print("done ".count($processLog->traces)." (".$minutes." Min. ".$seconds." Sek.)\n");
}

print("\n\nNum overall traces: ".$processLog->getNumTraces()."\n");

$featureVectors = $processLog->calculateFeatureVectors($withEventCounter, $withFileAssignment);

// making a CSV out of the feature vectors
$csv = "";
if ( $withFileAssignment ) {
	//var_dump($featureVectors);
	$csv .= ";;".implode(";", $featureVectors["FEATURE_VECTORS_HEADER"])."\r\n";
	unset($featureVectors["FEATURE_VECTORS_HEADER"]);
	foreach ( $featureVectors as $filename => $featuresVectorsContent ) {
		foreach ( $featuresVectorsContent as $traceID => $features ) {
			$csv .= $filename.";".$traceID.";".implode(";", $features)."\r\n";
		}
	}
} else {
	$csv .= ";".implode(";", $featureVectors["FEATURE_VECTORS_HEADER"])."\r\n";
	foreach ( $featureVectors as $traceID => $features ) {
		if ( $traceID == "FEATURE_VECTORS_HEADER" ) continue;
		$csv .= $traceID.";".implode(";", $features)."\r\n";
	}
}

// if ( in_array("--export", $argv) ) {
// 	print("save log as mxml ... ");
// 	$startSavingTime = time();
// 	$processLog->exportMXML();

// 	$savingDuration = time() - $startSavingTime;
// 	$seconds = $savingDuration % 60;
// 	$minutes = floor($savingDuration / 60);

// 	print("done (".$minutes." Min. ".$seconds." Sek.)\n");
// }

// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator($output, $csv);
$fileGenerator->setPathFilename($output);
$fileGenerator->setContent($csv);
$uri_csv = $fileGenerator->execute(false);
// AUSGABEDATEIEN ERSTELLT

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme .= "\n\nExtraction of feature vectors for Log file(s) ".implode(",", $input)." successfully finished.";
$sid = $uri_csv;
$sid = str_replace("workspace/", "", $sid);
$pos = strpos($sid, "/");
$sid = $pos ? substr($sid, 0, $pos) : $sid;
$readme .= "\n\nYour workspace: ".Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";

$readme .= "Num Traces overall: ".$processLog->getNumTraces()."\r\n\r\n";

$readme .= "\r\n\r\nDuration: ".$minutes." Min. ".$seconds." Sec.";

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLILogFeatureVectorsNotification($email, $readme);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n\n");
Logger::log($email, "CLILogFeatureVectors finished: input=".implode(",", $input)." output=".$output, "ACCESS");
?>