<?php

// @TODO NOT TESTED AND NOT FINISHED!

/**
 * Mapping Kalkulator - Speziell fuer den Mapping-Contest der BPM2013 konzipiert:
 * http://processcollections.org/matching-contest
 */
$start = time();
require 'autoloader.php';

print("\n----------------------------------------------------------\n RefModMining - N-Ary Semantic Cluster Matching (BPM2013) \n----------------------------------------------------------\n");

// Hilfeanzeige auf CLI
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) ) {
	exit("   Optionen:\n
   input1=          path to first input epml
   input2=          path to second input epml
   output=          path to output file
   notification=
      no
      [E-Mail adress]

   please user the correct order!
			
ERROR: Parameters incomplete");
};

// Checking Parameters
$input1   = substr($argv[1], 7,  strlen($argv[1]));
$input2   = substr($argv[2], 7,  strlen($argv[2]));
$output  = substr($argv[3], 7,  strlen($argv[3]));
$email   = substr($argv[4], 13, strlen($argv[4]));

print("
input1: ".$input1."
input2: ".$input2."
output: ".$output."
notification: ".$email."

checking input parameters ...
");

// Check input1
if ( file_exists($input1) ) {
	print "  input1 ... ok\n";
} else {
	exit("  input1 ... failed (file does not exist)");
}

// Check input2
if ( file_exists($input2) ) {
	print "  input2 ... ok\n";
} else {
	exit("  input2 ... failed (file does not exist)");
}

// Check notification
$doNotify = true;
if ( empty($email) || $email == "no" ) {
	$doNotify = false;
	print "  notification ... ok (notification disabled)\n";
} else {
	print "  notification ... ok (mail to ".$email.")\n";
}

// Laden der Modelldateien
$content_file_1 = file_get_contents($input1);
$xml1 = new SimpleXMLElement($content_file_1);
$content_file_2 = file_get_contents($input2);
$xml2 = new SimpleXMLElement($content_file_2);

// Vorbereitung der Forschrittsanzeige
$modelsInFile1 = count($xml1->xpath("//epc"));
$modelsInFile2 = count($xml2->xpath("//epc"));
$numOfAllModels = $modelsInFile1+$modelsInFile2;
$countCombinations = $modelsInFile1*$modelsInFile2;
$countCompletedCombinations = 0;
$progress = 0.1;

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("\nNumber of models in file 1: ".count($xml1->xpath("//epc"))."\n");
print("Number of models in file 2: ".count($xml2->xpath("//epc"))."\n");
print("Number of model combinations: ".$countCombinations);

// ReadMe.txt erzeugen
$readme = "--------------------------------------------------------------------------\r\n";
$readme .= " RMMaaS - Process Matching - N-Ary Semantic Cluster Matching\r\n";
$readme .= "--------------------------------------------------------------------------\r\n\r\n";
$readme .= "Log:\r\n";
$readme .= " - Model file1:  ".$input1." (".$modelsInFile1." models)\r\n";
$readme .= " - Model file2:  ".$input2." (".$modelsInFile2." models)\r\n";
$readme .= " - Number of model pairs: ".$countCombinations;

$generatedFiles = array();
$naryMapping = new NAryWordstemMappingWithAntonyms();
foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$epc = new EPC($xml1, $xml_epc1["epcId"], $xml_epc1["name"]);
	$naryMapping->addEPC($epc);
}
$epcIndexCut = count($naryMapping->epcs);
foreach ($xml2->xpath("//epc") as $xml_epc2) {
	$epc = new EPC($xml2, $xml_epc2["epcId"], $xml_epc2["name"]);
	$naryMapping->addEPC($epc);
}
$clusterFiles = $naryMapping->map();
foreach ( $clusterFiles as $filename ) {
	array_push($generatedFiles, $filename);
}
$numEPCs = count($naryMapping->epcs);

print("\n\nCalculate matchings...\n");
// Extract binary mappings
for ( $i=0; $i<$epcIndexCut; $i++ ) {
	for ( $j=$epcIndexCut; $j<$numEPCs; $j++ ) {
		$mapping = $naryMapping->extractBinaryMapping($naryMapping->epcs[$i], $naryMapping->epcs[$j]);
		$mapping->map("Greedy");
		$mapping->deleteDummyTransitions();
		//$file = $mapping->export();
		$file = $mapping->exportWithPipes();
		//$file = $mapping->exportAndreasSonntag();
		array_push($generatedFiles, $file);
		
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

print("\ndone");

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
	print("done (#files: ".$zip->numFiles.", status".$zip->status.")");
} else {
	exit("\nCannot open <".$output.">. Error creating zip file.\n");
}
// ZIP COMPLETED

// Extract session ID from uri
$sid = $output;
$sid = str_replace("workspace/", "", $sid);
$pos = strpos($sid, "/");
$sid = $pos ? substr($sid, 0, $pos) : $sid;
$readme .= "Your workspace: ".Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLINSCMNotification($email, $readme);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n");
print("N-Ary Semantic Cluster Matching for two model files finished successfully.\n");

Logger::log($email, "CLINarYSemanticClusterMatchingTwoModelFiles finished: input1=".$input1." input2=".$input2." output=".$output, "ACCESS");
?>
