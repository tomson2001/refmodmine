<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Create Matching Batch Script \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) ) {
	exit("   Please provide the following parameters:\n
   input=           the directory path to scan
   output=          the filename of the ouput file
   
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

// initiate progress bar
$epmlCount = 0;
$numOperations = ($numEPMLs * $numEPMLs) / 2 + 1;
$progressBar = new CLIProgressbar($numOperations, 0.1);
$progressBar->run($epmlCount);
$outputContent = "";

foreach ( $epmls as $index1 => $epml1 ) {
	
	foreach ( $epmls as $index2 => $epml2 ) {
		
		if ( $index2 <= $index1 ) continue;
		
		$name1 = substr(basename($epml1), 0, -5);
		$name2 = substr(basename($epml2), 0, -5);
		
		//$outputContent .= "\nMATCH model_set=".$epml1.",".$epml2." matching=files/diss/0_linguistically_harmonized/Mappings/ESGM/".$name1."-".$name2.".rdf function ESGM similarity=esgm_sim dictionary=WORDNET threshold=0.65";
		//$outputContent .= "\nMATCH model_set=".$epml1.",".$epml2." matching=files/diss/1_controlled_modelling/Mappings/ESGM/".$name1."-".$name2.".rdf function ESGM similarity=esgm_sim dictionary=WORDNET threshold=0.65";
		//$outputContent .= "\nMATCH model_set=".$epml1.",".$epml2." matching=files/diss/1_controlled_modelling/Mappings/NLM/".$name1."-".$name2.".rdf function NLM language=en";
		//$outputContent .= "\nMATCH model_set=".$epml1.",".$epml2." matching=files/diss/2_uncontrolled_modelling-birth_registration/Mappings/NLM/".$name1."-".$name2.".rdf function NLM language=en";
		//$outputContent .= "\nMATCH model_set=".$epml1.",".$epml2." matching=files/diss/3_uncontrolled_modelling-university_admission/Mappings/ESGM/".$name1."-".$name2.".rdf function ESGM similarity=esgm_sim dictionary=WORDNET threshold=0.65";
		//$outputContent .= "\nMATCH model_set=".$epml1.",".$epml2." matching=files/diss/3_uncontrolled_modelling-university_admission/Mappings/NLM/".$name1."-".$name2.".rdf function NLM language=en";
		
		if ( file_exists("files/diss/3_uncontrolled_modelling-university_admission/Mappings/SMSL_XML_multipleFiles/".$name1."-".$name2.".xml") ) {
			$mappingFile = $name1."-".$name2.".xml";
			$resultFile = $name1."-".$name2.".rdf";
		} elseif ( file_exists("files/diss/3_uncontrolled_modelling-university_admission/Mappings/SMSL_XML_multipleFiles/".$name2."-".$name1.".xml") ) {
			$mappingFile = $name2."-".$name1.".xml";
			$resultFile = $name2."-".$name1.".rdf";
		} else {
			$mappingFile = null;
		}
		if ( !is_null($mappingFile) ) {
			$outputContent .= "\nCONVERT_MATCHING matchings=files/diss/3_uncontrolled_modelling-university_admission/Mappings/SMSL_XML_multipleFiles/".$mappingFile." model_set=".$epml1.",".$epml2." output_file=files/diss/3_uncontrolled_modelling-university_admission/Mappings/SMSL/".$resultFile." format=rdf";
		} else {
			print("\n".$name1."-".$name2.".rdf skipped");
		}
		
		$epmlCount++;
		$progressBar->run($epmlCount);
	}
	
}

// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator($output, $outputContent);
$fileGenerator->setPathFilename($output);
$fileGenerator->setContent($outputContent);
$uri_file = $fileGenerator->execute(false);

print(" done\n");
?>