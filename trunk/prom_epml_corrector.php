<?php
$start = time();
require 'autoloader.php';

$log = "";
$output = "\n-------------------------------------------------\n RefMod-Mining - ProM EPC Label Corrector \n-------------------------------------------------\n\n";
print($output);
$log .= $output;

if ( in_array("--help", $argv) || in_array("-help", $argv) || in_array("-?", $argv) ) {
	exit("   Optionen:\n
   <filename>   Dateiname der epml-Datei (im Ordner input)
   [--help]               Hilfe\n\n");
}

/**
 * Einstellungen
*/
$filename = $argv[1];
$filepath = Config::INPUT_FOLDER."".$filename;
if ( !file_exists($filepath) ) exit("File not found: ".$filepath);

$content_file_1 = file_get_contents($filepath);
$xml1 = new SimpleXMLElement($content_file_1);

$modelsInFile1 = count($xml1->xpath("//epc"));

$output = "Modelldatei: ".$filename." (".$modelsInFile1." Modelle)\n\n";
$output = "Anpassen der Knoten-Labels ";

print($output);
$log .= $output."... ";

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$epc1 = new EPC($xml1, $xml_epc1["epcId"], $nameOfEPC1);
	$epc1->removeProMLabelSuffix();
	$epc1->exportEPML();
	print(".");
}

$output = " done\n\n";
$log .= $output;
print($output);

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);
$output = "Gesamtdauer: ".$minutes." Min. ".$seconds." Sek.\n\n";
print($output);
$log .= $output;

//$fileGenerator->setFilename("Log.txt");
//$fileGenerator->setContent($log);
//$uri_log_txt = $fileGenerator->execute();

?>
