<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Create Matching Batch Script \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) ) {
	exit("   Please provide the following parameters:\n
   input=           the directory path to scan for epmls
   output=          the output directory
   
   please user the correct order!
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$input   = substr($argv[1], 6,  strlen($argv[1]));
$output  = substr($argv[2], 7,  strlen($argv[2]));

print("
input: ".$input."
output: ".$output."

checking input parameters ...
");

// Check input
if ( is_dir($input) ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file does not exist)\n\n");
}

// Check input
if ( is_dir($output) ) {
	print "  output ... ok\n";
} else {
	if ( mkdir($output) ) {
		print "  output ... directory created\n";
	} else {
		exit("  output ... failed (path does not exist and cannot be created)\n\n");
	}
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

print("Start EPML detection ... ");

$epmls = getEPMLsInDirWithSubs($input);
$numEPMLs = count($epmls);

print("done\n");

print("\nStart making EPC names URI valid ... ");

foreach ( $epmls as $epml ) {
	
	$basename = basename($epml);
	
	$content_file = file_get_contents($epml);
	$xml = new SimpleXMLElement($content_file);
	
	// Load EPCs
	foreach ($xml->xpath("//epc") as $xml_epc) {
		$epcID = isset($xml_epc["epcId"]) ? (string) $xml_epc["epcId"] : (string) $xml_epc["EpcId"];
		$newModelName = str_replace(" ", "", str_replace(":", "", $xml_epc["name"]));
		$epc = new EPC($xml, $epcID, $newModelName);
		
		$filename = $epc->exportEPML();
		//print($filename."\n");
		rename($filename, $output."/".$basename);
	}
	
}

print(" done\n\n");
?>