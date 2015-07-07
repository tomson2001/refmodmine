<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - PetriNet (PNML) to EPC (EPML) Converter \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) ) {
	exit("   Please provide the following parameters:\n
   input=           path to input pnml
   output=          path to output epml
   notification=
      no
      [E-Mail adress]

   please user the correct order!
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$input   = substr($argv[1], 6,  strlen($argv[1]));
$output   = substr($argv[2], 7,  strlen($argv[2]));
$email   = substr($argv[3], 13, strlen($argv[3]));

print("
input: ".$input."
output: ".$output."
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
if ( empty($email) || $email == "no" ) {
	$doNotify = false;
	print "  notification ... ok (notification disabled)\n";
} else {
	print "  notification ... ok (mail to ".$email.")\n";
}


// Verarbeitung der Modelldatei
$content_file = file_get_contents($input);
$xml = new SimpleXMLElement($content_file);
$modelsInFile = count($xml->xpath("//net"));

// print infos to console
print("\nModel file: ".$input."\n");
print("Number of models: ".$modelsInFile."\n\n");
print("Start PNML to EPML transformation and conversion ...\n");

// initiate progress bar
$modelCount = 0;
$progressBar = new CLIProgressbar($modelsInFile, 0.1);
$progressBar->run($modelCount);

$generatedFiles = array();

// Analyze all nodes in all models in the file
foreach ($xml->xpath("//net") as $xml_pn) {
	$modelName = basename($input, ".pnml");
	$modelName = str_replace("workspace.epml.pnml.", "", $modelName);
	$modelName = str_replace("custom.", "", $modelName);	
	$petriNet = new PetriNet($xml, $xml_pn["id"], $modelName);
	$epc = $petriNet->transform2EPC();
	$generatedFiles[$modelCount] = $epc->exportEPML();
	rename($generatedFiles[$modelCount], $output);
	
	$modelCount++;
	$progressBar->run($modelCount);
}
print(" done");

// ERSTELLEN DER AUSGABEDATEIEN
// // ZIP ALL FILES
// print("\n\nZip files ... ");
// $zip = new ZipArchive();
// if ( $zip->open($output, ZipArchive::CREATE) ) {
// 	foreach ( $generatedFiles as $filename ) {
// 		$pos = strrpos($filename, "/");
// 		$file = substr($filename, $pos+21);
// 		$zip->addFile($filename, $file);
// 	}
// 	$zip->close();
// 	foreach ( $generatedFiles as $filename ) {
// 		unlink($filename);
// 	}
// 	print("done (#files: ".$zip->numFiles.", status".$zip->status.")");
// } else {
// 	exit("\nCannot open <".$output.">. Error creating zip file.\n");
// }
// // ZIP COMPLETED
// AUSGABEDATEIEN ERSTELLT

$readme  = "PetriNet in ".$input." successfully converted to EPC (EPML).";
$sid = $output;
$sid = str_replace("workspace/", "", $sid);
$pos = strpos($sid, "/");
$sid = $pos ? substr($sid, 0, $pos) : $sid;
$readme .= "\n\nYour workspace: ".Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";
//$readme .= "\r\n\r\nGenerated files:";
//$readme .= implode("\r\n   ", $generatedFiles);

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme .= "\r\n\r\nDuration: ".$minutes." Min. ".$seconds." Sec.";

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLIConvertPNML2EPMLNotification($email, $readme);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n\n");
Logger::log($email, "CLIConvertPNML2EPML finished: input=".$input." output=".$output, "ACCESS");
?>