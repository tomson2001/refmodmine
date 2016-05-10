<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Remove Event Mappings from RDF \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3])|| !isset($argv[4])) {
	exit("   Please provide the following parameters:\n
   input=           the path of the rdf-mappings
   epml=			the EPML file, which contains the relevant models
   output=          the path to save the 'new' mappings
   removeUnknownNodes= true | false, if set to true, maps containing nodes (IDs) which are not available in the source model are removed.
   
   please user the correct order!
   Hint 1: This tools removed all maps which are not activity maps
   Hint 2: RDFs must be formatted like described in the Process Model Matching Contest 2015
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$input   = substr($argv[1], 6,  strlen($argv[1]));
$epml	 = substr($argv[2], 5,  strlen($argv[2]));
$output  = substr($argv[3], 7,  strlen($argv[3]));
$removeUnknownNodesString = substr($argv[4], 19,  strlen($argv[4]));
$removeUnknownNodes = $removeUnknownNodesString == "true" ? true : false;

print("
input: ".$input."
epml: ".$epml."
output: ".$output."
removeUnknownNodes: ".$removeUnknownNodesString."

checking input parameters ...
");

// Check input
if ( is_dir($input) ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file does not exist)\n\n");
}

// Check input
if ( file_exists($epml) ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file does not exist)\n\n");
}

// Check output
if ( is_dir($output) ) {
	print "  output ... ok\n";
} else {
	if ( mkdir($output) ) {
		print "  output ... directory created\n";
	} else {
		exit("  output ... failed (directory does not exist and cannot be created)\n\n");
	}
}
if ( !Tools::endsWith($output, "/") ) $output .= "/";

function getRDFsInDirWithSubs($dirPath) {
	$rdfs = array();
	$files = scandir($dirPath);
	foreach ( $files as $filename ) {
		$path = $dirPath.DIRECTORY_SEPARATOR.$filename;
		if ( substr($filename, -4) == ".rdf" ) {
			array_push($rdfs, $path);
		} elseif ( is_dir($path) && $filename != "." && $filename != ".." ) {
			$subDirRDFs = getRDFsInDirWithSubs($path);
			foreach ( $subDirRDFs as $addPath ) array_push($rdfs, str_repeat("//", "/", $addPath));
		}
	}
	return $rdfs;
}

print("\nDetecting Mapping files ... ");

$rdfs = getRDFsInDirWithSubs($input);
$numRDFs = count($rdfs);

print("done (".$numRDFs.")\n");

print("Loading EPCs ... ");

// Verarbeitung der Modelldatei
$content_file = file_get_contents($epml);
$xml = new SimpleXMLElement($content_file);

$modelCount = 0;
$numModels = count($xml->xpath("//epc"));
$progressBar = new CLIProgressbar($numModels, 0.1);
$progressBar->run($modelCount);

$epcs = array();

foreach ($xml->xpath("//epc") as $xml_epc) {
	$epcID = isset($xml_epc["epcId"]) ? (string) $xml_epc["epcId"] : (string) $xml_epc["EpcId"];
	$epc = new EPC($xml, $epcID, $xml_epc["name"]);
	
	$doConvertNames = true; // this is a special handling for KMSSS and MSSS
	$modelName = $doConvertNames ? str_replace(" ", "", str_replace(":", "", $epc->name)) : $epc->name;
	$epc->name = $modelName;
	
	$epcs[$modelName] = $epc;
	//print("   ".$epc->name."\n");
	$modelCount++;
	$progressBar->run($modelCount);
}

print("done (".$numModels.")\n\n");

print("Remove event maps ... \n");

// initiate progress bar
$rdfCount = 0;
$numOperations = $numRDFs;
$progressBar = new CLIProgressbar($numOperations, 0.1);
$progressBar->run($rdfCount);

foreach ( $rdfs as $rdfFilename ) {

	$saveRDFFilename = $output.basename($rdfFilename);
	
	$mapping = new GenericMapping();
	$mapping->loadRDF_BPMContest2015($rdfFilename);	
	$numRemoved = 0;
//	var_dump($mapping);
	if ( isset($mapping->maps[0]) ) {
		foreach ( $mapping->models as $modelName ) $mapping->assignEPC($epcs[$modelName]);
		$numRemoved = $mapping->removeAllButFunctionMappings($removeUnknownNodes);
	}
	$resultFile = $mapping->exportRDF_BPMContest2015(false, $saveRDFFilename);
	print("   file saved as: ".$resultFile." (".$numRemoved." maps removed)\n");
	
	$rdfCount++;
	$progressBar->run($rdfCount);

}

print("done\n");
?>