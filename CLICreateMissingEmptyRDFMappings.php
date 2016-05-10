<?php
/**
 * Based on a EPML file, this script checks whether all expected mapping are available. If not, empty mappings are created.
 */
$start = time();
require 'autoloader.php';

print("\n----------------------------------------------------------\n RefModMining - Create missing empty mappings \n----------------------------------------------------------\n");

// Hilfeanzeige auf CLI
if ( !isset($argv[1]) || !isset($argv[2]) ) {
	exit("   Optionen:\n
   input=           path to input epml file
   mapping_path=	path, where the mapping files are

   please user the correct order!
			
ERROR: Parameters incomplete");
};

// Checking Parameters
$input   	  = substr($argv[1], 6,  strlen($argv[1]));
$mappingPath  = substr($argv[2], 13,  strlen($argv[2]));

print("
input: ".$input."
mapping path: ".$mappingPath."

checking input parameters ...
");

// Check input
if ( file_exists($input) ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file does not exist)");
}

// Check mapping path
if ( is_dir($mappingPath) ) {
	print "  mapping path ... ok\n";
} else {
	exit("  mapping path ... failed (directory does not exist)");
}

// Laden der Modelldateien
$content_file_1 = file_get_contents($input);
$xml1 = new SimpleXMLElement($content_file_1);

// Vorbereitung der Forschrittsanzeige
$modelsInFile1 = count($xml1->xpath("//epc"));
$numOfAllModels = $modelsInFile1;
$countCombinations = (($modelsInFile1 * $modelsInFile1)/2)-($modelsInFile1/2);
$countCompletedCombinations = 0;
$progress = 0.1;

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("\nNumber of models: ".count($xml1->xpath("//epc"))."\n");
print("Number of model permutations: ".$countCombinations);

$generatedRDFs = array();

print("\n\nLoad models ... ");
$epcs = array();
foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = (string) $xml_epc1["name"];
	$epc = new EPC($xml1, $xml_epc1["epcId"], $xml_epc1["name"]);

	$doConvertNames = true; // this is a special handling for KMSSS and MSSS
	if ( $doConvertNames ) {
		$epc->name = $doConvertNames ? str_replace(" ", "", str_replace(":", "", $epc->name)) : $epc->name;
	}

	array_push($epcs, $epc);
}
print("done");

function getRDFsInDirWithSubs($dirPath) {
	$rdfs = array();
	$files = scandir($dirPath);
	foreach ( $files as $filename ) {
		$path = $dirPath.DIRECTORY_SEPARATOR.$filename;
		if ( substr($filename, -4) == ".rdf" ) {
			array_push($rdfs, basename($path));
		} elseif ( is_dir($path) && $filename != "." && $filename != ".." ) {
			$subDirRDFs = getRDFsInDirWithSubs($path);
			foreach ( $subDirRDFs as $addPath ) array_push($rdfs, $addPath);
		}
	}
	return $rdfs;
}

print("\nStart RDF detection ... ");

$rdfs = getRDFsInDirWithSubs($mappingPath);
$numRDFs = count($rdfs);

print("done (".$numRDFs.")\n");

$addRDFs = 0;
print("\n\nCheck and add missing rdf mappings ... ");
// Extract binary mappings
for ( $i=0; $i<$modelsInFile1; $i++ ) {
	for ( $j=$i+1; $j<$modelsInFile1; $j++ ) {
		if ( $j <= $i ) continue;

		$rdfNameOption1 = $epcs[$i]->name."-".$epcs[$j]->name.".rdf";
		$rdfNameOption2 = $epcs[$j]->name."-".$epcs[$i]->name.".rdf";

		if ( file_exists($mappingPath."/".$rdfNameOption1) ) continue;
		if ( file_exists($mappingPath."/".$rdfNameOption2) ) continue;

		$mapping = new GenericMapping();
		$mapping->addModel($epcs[$i]->id, $epcs[$j]->name);
		$mapping->addModel($epcs[$j]->id, $epcs[$j]->name);

		$file = $mapping->exportRDF_BPMContest2015(false, $mappingPath."/".$rdfNameOption1);
		array_push($generatedRDFs, $file);

		$addRDFs++;
	}
}

print("done (".$addRDFs.")\n\n");
?>
