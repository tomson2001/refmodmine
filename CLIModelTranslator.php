<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Model Translator \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) ) {
	exit("   Please provide the following parameters:\n
   input=           path to input epml
   output=          path to output epml
   reportCSV=       path to report CSV for the output
   language_combination= de-en | en-de
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
$report   = substr($argv[3], 10,  strlen($argv[3]));
$lang   = strtolower(substr($argv[4], 21,  strlen($argv[4])));
$email   = substr($argv[5], 13, strlen($argv[5]));

print("
input: ".$input."
output: ".$output."
reportCSV: ".$report."
language combination: ".$lang."
notification: ".$email."

checking input parameters ...
");

// Check input
if ( file_exists($input) ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file does not exist)\n\n");
}

// Check lang combination
$possibleLangCombination = array("de-en", "en-de", "de-en", "de-fr", "de-fr", "en-nl",  "en-de", "fr-en", "fr-de", "it-de", "it-en", "nl-en");
if ( in_array($lang, $possibleLangCombination) ) {
	print "  language combination ... ok\n";
} else {
	exit("  language combination ... failed (language combination not available)\n\n");
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
$modelsInFile = count($xml->xpath("//epc"));

// print infos to console
print("\nModel file: ".$input."\n");
print("Number of models: ".$modelsInFile."\n\n");
print("Start translation ...\n");

// initiate progress bar
$modelCount = 0;
$progressBar = new CLIProgressbar($modelsInFile, 0.1);
$progressBar->run($modelCount);

$csv = "model;node-type;original_label;translation\n";

$epml =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$epml .= "<epml:epml xmlns:epml=\"http://www.epml.de\"\n";
$epml .= "  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"epml_1_draft.xsd\">\n";

// Analyze all nodes in all models in the file
foreach ($xml->xpath("//epc") as $xml_epc) {
	$epc = new EPCNLP($xml, $xml_epc["epcId"], $xml_epc["name"]);
	$oldLabels = $epc->functions;
	$epc->translate($lang);
	$newLabels = $epc->functions;
	
	foreach ( $oldLabels as $key => $oldLabel ) {
		$newLabel = $newLabels[$key];
		$csv .= $epc->name.";activity;".$oldLabel.";".$newLabel."\n";
	}
	
	$epml .= $epc->getEPMLCodePart();
	$modelCount++;
	$progressBar->run($modelCount);
}

// ERSTELLEN DER AUSGABEDATEIEN
$epml .= "</epml:epml>";
$fileGenerator = new FileGenerator($output, $epml);
$fileGenerator->setPathFilename($output);
$fileGenerator->setContent($epml);
$uri_file = $fileGenerator->execute(false);

$fileGenerator = new FileGenerator($report, $csv);
$fileGenerator->setPathFilename($report);
$fileGenerator->setContent($csv);
$uri_csv = $fileGenerator->execute(false);
// AUSGABEDATEIEN ERSTELLT

print(" done");

$readme  = "Model Translation (".$lang.") for model file ".$input." successfully finished.";
$sid = $uri_file;
$sid = str_replace("workspace/", "", $sid);
$pos = strpos($sid, "/");
$sid = $pos ? substr($sid, 0, $pos) : $sid;
$readme .= "\n\nYour workspace: ".Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme .= "\r\n\r\nDuration: ".$minutes." Min. ".$seconds." Sec.";

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLIModelTranslationNotification($email, $readme);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n\n");
Logger::log($email, "CLIModelTranslator finished: input=".$input." output=".$output." lang-combination=".$lang, "ACCESS");
?>