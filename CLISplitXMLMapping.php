<?php
/**
 * Splits a XML-Mapping files containing several Mappings into XML-Mapping files containing one mapping
 */
$start = time();
require 'autoloader.php';

print("\n----------------------------------------------------------\n RefMod-Miner (PHP) - XML-Mapping Splitter \n----------------------------------------------------------\n");

// Hilfeanzeige auf CLI
if ( !isset($argv[1]) || !isset($argv[2]) ) {
	exit("   Optionen:\n
   input=           path to input xml
   output=          output path

   please user the correct order!
			
ERROR: Parameters incomplete");
};

// Checking Parameters
$input   = substr($argv[1], 6,  strlen($argv[1]));
$output  = substr($argv[2], 7,  strlen($argv[2]));

print("
input: ".$input."
output: ".$output."

checking input parameters ...
");

// Check input file
if ( file_exists($input) ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file does not exist)");
}

// Check output path
if ( is_dir($output) ) {
	print "  output path ... ok\n";
} else {
	if ( mkdir($output) ) {
		print "  output path ... created\n";
	} else {
		exit("  output path ... failed (path does not exist and cannot be created)");
	}
}

// Laden der Modelldateien
$content_file_1 = file_get_contents($input);
$xml = new SimpleXMLElement($content_file_1);

// Vorbereitung der Forschrittsanzeige
$numMappings = count($xml->xpath("//matching"));
$mappingCounter = 0;
$progressBar = new CLIProgressbar($numMappings, 0.1);

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("\nNumber of mappings: ".$numMappings."\n");

$generatedFiles = array();

print("\n\nGenerating single XMLs for each mapping...\n");

$header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<matchings>";
$footer = "</matchings>";

foreach ($xml->xpath("//matching") as $xml_matching) {
	$matchingName = $xml_matching["name"];
	
	$content = $header."\n  ".$xml_matching->asXML()."\n".$footer;
	
	$fileGenerator = new FileGenerator($output."/".$matchingName.".xml", $content);
	$fileGenerator->setPathFilename($output."/".$matchingName.".xml");
	$fileGenerator->setContent($content);
	$file = $fileGenerator->execute(true, false);
	
	// FORTSCHRITTSANZEIGE
	$mappingCounter++;
	$progressBar->run($mappingCounter);
	// ENDE DER FORTSCHRITTSANZEIGE
}

print("\ndone");

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n");
print("XML-Mapping splitting successfully finished.\n");

Logger::log("CLI", "CLISplitXMLMapping finished: input=".$input." output=".$output, "ACCESS");
?>
