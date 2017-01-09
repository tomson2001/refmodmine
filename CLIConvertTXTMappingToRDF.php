<?php
// Convertes TXT Mappings of the Contest 2013 to the RDFs of contest 2015
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Create ID conversion of models \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) ) {
	exit("   Please provide the following parameters:\n
   epml=           path to the first epml
   mapping=         path to rdf mapping files, which IDs should be converted

   please user the correct order!
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$epml1   = substr($argv[1], 5,  strlen($argv[1]));
$mappingPath   = substr($argv[2], 8,  strlen($argv[2]));

print("
epml1: ".$epml1."
mapping: ".$mappingPath."

checking input parameters ...
");

// Check input
if ( file_exists($epml1) ) {
	print "  epml1 ... ok\n";
} else {
	exit("  epml1 ... failed (file does not exist)\n\n");
}

// Check input
if ( $mappingPath != "no" ) {
	if ( is_dir($mappingPath) ) {
		print "  mapping directory ... ok\n";
	} else {
		exit("  mapping directory ... failed (path does not exist)\n\n");
	}
}


// Verarbeitung der Modelldateien
$content_file1 = file_get_contents($epml1);
$xml1 = new SimpleXMLElement($content_file1);
$modelsInFile1 = count($xml1->xpath("//epc"));

// print infos to console
print("\nLoad EPCs ...");

$epcs = array();

// Load EPCs
foreach ($xml1->xpath("//epc") as $xml_epc) {
	$epcID = isset($xml_epc["epcId"]) ? (string) $xml_epc["epcId"] : (string) $xml_epc["EpcId"];
	$epc = new EPC($xml1, $epcID, $xml_epc["name"]);
	$epcs[$epc->name] = $epc;
	//print($epc->name."\n");
}
$numEPCs = count($epcs);
print(" done (".$numEPCs.")");

function getFiles($dirPath) {
	$txtMappings = array();
	$files = scandir($dirPath);
	foreach ( $files as $filename ) {
		$path = $filename;
		if ( !is_dir($path) && $filename != "." && $filename != ".." && substr($filename, -4) != ".rdf" ) {
			array_push($txtMappings, $path);
		}
	}
	return $txtMappings;
}

print("\nDetecting Mapping files ... ");

$txtMappings = getFiles($mappingPath);
$numMappingFiles = count($txtMappings);

print("done (".$numMappingFiles.")");

print("\n\nConvert mappings to RDF ...\n");

foreach ( $txtMappings as $filename ) {
	$path = $mappingPath.DIRECTORY_SEPARATOR.$filename;
	$mapping = new GenericMapping();
	$mapping->loadTXT_BPMContest2013($path, $epcs);
	$newFilename = implode("-", $mapping->models).".rdf";
	$rdfFileName = $mappingPath.DIRECTORY_SEPARATOR.$newFilename;
	$mapping->exportRDF_BPMContest2015(false, $rdfFileName);
}

print("done\n");
?>