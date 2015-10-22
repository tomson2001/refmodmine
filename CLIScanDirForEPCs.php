<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Scan Directory for EPCs in EPMLs \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) ) {
	exit("   Please provide the following parameters:\n
   input=           the directory path to scan
   output=          the filename of the ouput csv
   notification=
      no
      [E-Mail adress]

   please user the correct order!
			
ERROR: Parameters incomplete
");
}

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
if ( is_dir($input) ) {
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

function getEPMLsInDirWithSubs($dirPath) {
	$epmls = array();
	$files = scandir($dirPath);
	foreach ( $files as $filename ) {
		$path = $dirPath.DIRECTORY_SEPARATOR.$filename;
		if ( substr($filename, -5) == ".epml" ) {
			array_push($epmls, $path);
		} elseif ( is_dir($path) && $filename != "." && $filename != ".." ) {
			$subDirEPMLs = getEPMLsInDirWithSubs($path);
			foreach ( $subDirEPMLs as $addPath ) array_push($epmls, $addPath);
		}
	}
	return $epmls;
}

print("Start EPML detection ...\n");

$epmls = getEPMLsInDirWithSubs($input);
$numEPMLs = count($epmls);

// initiate progress bar
$epmlCount = 0;
$progressBar = new CLIProgressbar($numEPMLs, 0.1);
$progressBar->run($epmlCount);
$csv = "filepath;model-name\n";

foreach ( $epmls as $epml ) {
	$content_file = file_get_contents($epml);
	$xml = new SimpleXMLElement($content_file);
	
	foreach ($xml->xpath("//epc") as $xml_epc) {
		$epcName = $xml_epc["name"];
		// 	$epc = new EPCNLP($xml, $xml_epc["epcId"], $xml_epc["name"]);
		$csv .= $epml.";".$epcName."\n";
	}
	
	$epmlCount++;
	$progressBar->run($epmlCount);
}

// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator($output, $csv);
$fileGenerator->setPathFilename($output);
$fileGenerator->setContent($csv);
$uri_file = $fileGenerator->execute(false);

print(" done\n");

// $readme  = "Model Translation (".$lang.") for model file ".$input." successfully finished.";
// $sid = $uri_file;
// $sid = str_replace("workspace/", "", $sid);
// $pos = strpos($sid, "/");
// $sid = $pos ? substr($sid, 0, $pos) : $sid;
// $readme .= "\n\nYour workspace: ".Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";

// // Berechnungdauer
// $duration = time() - $start;
// $seconds = $duration % 60;
// $minutes = floor($duration / 60);

// $readme .= "\r\n\r\nDuration: ".$minutes." Min. ".$seconds." Sec.";

// if ( $doNotify ) {
// 	print("\n\nSending notification ... ");
// 	$notificationResult = EMailNotifyer::sendCLIModelTranslationNotification($email, $readme);
// 	if ( $notificationResult ) {
// 		print("ok");
// 	} else {
// 		print("error");
// 	}
// }

// // Ausgabe der Dateiinformationen auf der Kommandozeile
// print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n\n");
// Logger::log($email, "CLIModelTranslator finished: input=".$input." output=".$output." lang-combination=".$lang, "ACCESS");
?>