<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Label Tagger \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) ) {
	exit("   Please provide the following parameters:\n
   input=           path to input epml
   notification=
      no
      [E-Mail adress]

   please user the correct order!
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$input   = substr($argv[1], 6,  strlen($argv[1]));
$email   = substr($argv[2], 13, strlen($argv[2]));

print("
input: ".$input."
notification: ".$email."

checking input parameters ...
");

// Check input
if ( file_exists($input) ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file does not exist)\n\n");
}

// Check notification
$doNotify = true;
if ( empty($email) ) {
	$doNotify = false;
	print "  notification ... ok (notification disabled)\n";
} else {
	print "  input ... ok (mail to ".$email.")\n";
}


// Verarbeitung der Modelldatei
$content_file = file_get_contents($input);
$xml = new SimpleXMLElement($content_file);
$modelsInFile = count($xml->xpath("//epc"));

// print infos to console
print("Model file: ".$input."\n");
print("Number of models: ".$modelsInFile."\n\n");
print("Start analyzing...\n");

// initiate progress bar
$modelCount = 0;
$progressBar = new CLIProgressbar($modelsInFile, 0.1);
$progressBar->run($modelCount);

$generatedFiles = array();

// Analyze all nodes in all models in the file
foreach ($xml->xpath("//epc") as $xml_epc) {
	$epc = new EPCNLP($xml, $xml_epc["epcId"], $xml_epc["name"]);
	$epc->loadLabelTags();
	$epc->generateHighLevelLabelTags();
	$epc->detectLableStyles();
	$file_uri = $epc->exportNLPAnalysisCSV();
	array_push($generatedFiles, $file_uri);
	$modelCount++;
	$progressBar->run($modelCount);
}

print("\n\nGenerated files:");
foreach ( $generatedFiles as $uri ) print("\n   ".$uri);

$readme  = "NLP-Tagging for model file ".$input." successfully finished.\r\n\r\nGenerated files:";
$readme .= implode("\r\n   ", $generatedFiles);

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme .= "\r\n\r\nDuration: ".$minutes." Min. ".$seconds." Sec.";

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLIModelNLPTaggingNotification($email, $readme);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n\n");
?>