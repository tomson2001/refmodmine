<?php
/**
 * Splits EPML files into single epmls for each model and zips it
 */
$start = time();
require 'autoloader.php';

print("\n----------------------------------------------------------\n RefMod-Miner (PHP) - EPML Splitter \n----------------------------------------------------------\n");

// Hilfeanzeige auf CLI
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) ) {
	exit("   Optionen:\n
   input=           path to input epml
   output=          path to output file
   notification=
      no
      [E-Mail adress]

   please user the correct order!
			
ERROR: Parameters incomplete");
};

// Checking Parameters
$input   = substr($argv[1], 6,  strlen($argv[1]));
$output  = substr($argv[2], 7,  strlen($argv[2]));
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
	exit("  input ... failed (file does not exist)");
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
$content_file_1 = file_get_contents($input);
$xml = new SimpleXMLElement($content_file_1);

// Vorbereitung der Forschrittsanzeige
$numModels = count($xml->xpath("//epc"));
$modelCounter = 0;
$progressBar = new CLIProgressbar($numModels, 0.1);

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("\nNumber of models: ".$numModels."\n");

$generatedFiles = array();

print("\n\nGenerating single EPMLs for each model...\n");

foreach ($xml->xpath("//epc") as $xml_epc) {
	$epc = new EPC($xml, $xml_epc["epcId"], $xml_epc["name"]);
	$file = $epc->exportEPML();
	array_push($generatedFiles, $file);
	
	// FORTSCHRITTSANZEIGE
	$modelCounter++;
	$progressBar->run($modelCounter);
	// ENDE DER FORTSCHRITTSANZEIGE
}

print("\ndone");

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

print("\ndone");

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

// Extract session ID from uri
$sid = $output;
$sid = str_replace("workspace/", "", $sid);
$pos = strpos($sid, "/");
$sid = $pos ? substr($sid, 0, $pos) : $sid;
$readme = "Your workspace: ".Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLIEPMLSplitMNotification($email, $readme);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n");
print("EPML splitting successfully finished.\n");

Logger::log($email, "CLISplitAndZipEPMLFile finished: input=".$input." output=".$output, "ACCESS");
?>
