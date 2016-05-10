<?php
// @TODO: At moment functions IDs are supported only!!!
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Create ID conversion of models \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) ) {
	exit("   Please provide the following parameters:\n
   epml1=           path to the first epml
   epml2=           path to the second epml
   mapping=         path to rdf mapping files, which IDs should be converted

   please user the correct order!
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$epml1   = substr($argv[1], 6,  strlen($argv[1]));
$epml2   = substr($argv[2], 6,  strlen($argv[2]));
$mappingPath   = substr($argv[3], 8,  strlen($argv[3]));

print("
epml1: ".$epml1."
epml2: ".$epml2."
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
if ( file_exists($epml2) ) {
	print "  epml2 ... ok\n";
} else {
	exit("  epml2 ... failed (file does not exist)\n\n");
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

$content_file2 = file_get_contents($epml2);
$xml2 = new SimpleXMLElement($content_file2);
$modelsInFile2 = count($xml2->xpath("//epc"));

$numCombinations = $modelsInFile1*$modelsInFile2;

// print infos to console
print("\nLoad EPCs ...");

$epcs1 = array();
$epcs2 = array();

// Load EPCs
foreach ($xml1->xpath("//epc") as $xml_epc) {
	$epcID = isset($xml_epc["epcId"]) ? (string) $xml_epc["epcId"] : (string) $xml_epc["EpcId"];
	$epc = new EPC($xml1, $epcID, $xml_epc["name"]);
	$epcs1[$epc->name] = $epc;
	//print($epc->name."\n");
}

foreach ($xml2->xpath("//epc") as $xml_epc) {
	$epcID = isset($xml_epc["epcId"]) ? (string) $xml_epc["epcId"] : (string) $xml_epc["EpcId"];
	$epc = new EPC($xml2, $epcID, $xml_epc["name"]);
	$epcs2[$epc->name] = $epc;
}

print(" done");

print("\n\nGenerate conversion maps (".$numCombinations.") ...\n");

$conversionMap = array();

foreach ( $epcs1 as $modelName => $epc1 ) {
	$epc2 = $epcs2[$modelName];

	$conversionMap[$modelName] = array();

	$isPrecise = true;
	$matchCouter = 0;

	foreach ( $epc1->functions as $id1 => $label1 ) {
		foreach ( $epc2->functions as $id2 => $label2 ) {
			if ( $label1 == $label2 ) {
				//if ( $label1 == "claim mileage credit by mail" ) print("\n  claim mileage credit by mail => ".$label2.": ");
				$preds1 = $epc1->getPredecessor($id1);
				foreach ( $preds1 as $index => $currID ) $preds1[$index] = $epc1->getLabel($currID);
				sort($preds1);
				$pred1 = implode("-", $preds1);
				$preds2 = $epc2->getPredecessor($id2);
				foreach ( $preds2 as $index => $currID ) $preds2[$index] = $epc2->getLabel($currID);
				sort($preds2);
				$pred2 = implode("-", $preds2);
				$succs1 = $epc1->getSuccessor($id1);
				foreach ( $succs1 as $index => $currID ) $succs1[$index] = $epc1->getLabel($currID);
				sort($succs1);
				$succ1 = implode("-", $succs1);
				$succs2 = $epc2->getSuccessor($id2);
				foreach ( $succs2 as $index => $currID ) $succs2[$index] = $epc2->getLabel($currID);
				sort($succs2);
				$succ2 = implode("-", $succs2);
				
				$pred1 = strtolower(trim($pred1));
				$pred2 = strtolower(trim($pred2));
				$succ1 = strtolower(trim($succ1));
				$succ2 = strtolower(trim($succ2));

				$cmpPred = strcmp($pred1, $pred2);
				$cmpSucc = strcmp($succ1, $succ2);
					
				if ( $cmpPred == 0 && $cmpSucc == 0 ) {
					//if ( $label1 == "claim mileage credit by mail" ) print("ok\n");
					if ( isset($conversionMap[$modelName][$id1]) ) $isPrecise = false;
					if ( in_array($id2, array_values($conversionMap[$modelName])) ) $isPrecise = false;
					$conversionMap[$modelName][$id1] = $id2;
					$matchCouter++;
				} else {
					
					// check for uniqueness in first model
					$isUniqueInFirstModel = true;
					foreach ( $epc1->functions as $currID => $currLabel ) {
						if ( $label1 == $currLabel and $currID != $id1 ) $isUniqueInFirstModel = false;
					}
					
					// check for uniqueness in second model
					$isUniqueInSecondModel = true;
					foreach ( $epc2->functions as $currID => $currLabel ) {
						if ( $label2 == $currLabel and $currID != $id2 ) $isUniqueInSecondModel = false;
					}
					
					if ( $isUniqueInFirstModel && $isUniqueInSecondModel ) {
						if ( isset($conversionMap[$modelName][$id1]) ) $isPrecise = false;
						if ( in_array($id2, array_values($conversionMap[$modelName])) ) $isPrecise = false;
						$conversionMap[$modelName][$id1] = $id2;
						$matchCouter++;
					} else {
						//if ( $label1 == "Wait for results" )
						//print("(".$label1.") not ok\n   Pred1: ".$pred1."\n   Pred2: ".$pred2."\n   Succ1: ".$succ1."\n   Succ2: ".$succ2."\n");
						//var_dump(utf8_decode($pred1));
						//var_dump(utf8_decode($pred2));
						//print ("\"".$pred1."\" vs. \"".$pred2."\" (".$cmpPred.") & \"".$succ1."\" vs. \"".$succ2."\" (".$cmpSucc.")\n");
					}
					
				}
			}
		}
	}

	if ( !$isPrecise ) {
		print("Conversion table for \"".$modelName."\" is not precise!\n");
	} else {
		//print("Conversion table for \"".$name1."\" and \"".$name2."\" is ok!\n");
	}

	$numFuncs1 = count($epc1->functions);
	$numFuncs2 = count($epc2->functions);

	if ( $numFuncs1 != $numFuncs2 || $numFuncs1 != $matchCouter ) {
		print(" Conversion table for \"".$modelName."\" is incomplete (".$numFuncs1."|".$numFuncs2."|".$matchCouter.")!\n");

		foreach ( $epc1->functions as $id1 => $label1 ) {
			if ( !isset($conversionMap[$modelName][$id1]) ) print("    Missing map for \"".utf8_encode($label1)."\" in 1\n");
		}

		foreach ( $epc2->functions as $id2 => $label2 ) {
			if ( !in_array($id2, array_values($conversionMap[$modelName])) ) print("    Missing map for \"".utf8_encode($label2)."\" in 2\n");
		}
	}

	if ( $numFuncs1 == $numFuncs2 && $numFuncs1 == $matchCouter && $isPrecise ) {
		print(" Conversion table for \"".$modelName."\" is precise and complete!\n");
	}
}

print("done\n");

if ( $mappingPath != "no" ) {

	$doConvertNames = true; // this is a special handling for KMSSS and MSSS
	if ( $doConvertNames ) {
		foreach ( $epcs1 as $modelName => $epc ) {
			$newModelName = $doConvertNames ? str_replace(" ", "", str_replace(":", "", $modelName)) : $modelName;
			$epcs1[$newModelName] = $epc;
		}
			
		foreach ( $epcs2 as $modelName => $epc ) {
			$newModelName = $doConvertNames ? str_replace(" ", "", str_replace(":", "", $modelName)) : $modelName;
			$epcs2[$newModelName] = $epc;
		}
	}

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

	$rdfs = getRDFsInDirWithSubs($mappingPath);
	$numRDFs = count($rdfs);

	print("done\n\n");

	print("Update IDs in mappings ... \n");

	// initiate progress bar
	$rdfCount = 0;
	$numOperations = $numRDFs;
	$progressBar = new CLIProgressbar($numOperations, 0.1);
	$progressBar->run($rdfCount);

	foreach ( $rdfs as $rdfFilename ) {

		$mapping = new GenericMapping();
		$mapping->loadRDF_BPMContest2015($rdfFilename);

		if ( count($mapping->models) != 2 ) {
			print("  ".$rdfFilename." skipped (mapping is empty)!\n");
			continue;
		}

		$mappingModelKeys = array_keys($mapping->models);
		
		$conversionMap1 = null;
		$conversionMap2 = null;

		foreach ( $epcs1 as $modelName => $epc ) {
				
			if ( !is_null($conversionMap1) && !is_null($conversionMap2) ) {
				//print("   Conversion maps for ".$modelName." found.\n");
				break;
			}
			
			if ( $modelName == $mappingModelKeys[0] ) $conversionMap1 = $conversionMap[$modelName];
			if ( $modelName == $mappingModelKeys[1] ) $conversionMap2 = $conversionMap[$modelName];
		}
		
		if ( is_null($conversionMap1) || is_null($conversionMap2) ) exit("  Error: Conversion Map not found! (".implode(",",array_keys($conversionMap)).", searched for ".$mapping->models[1]." and ".$mapping->models[2].")\n\n");

		foreach ( $mapping->maps as $mapID => $map ) {
			// Precheck of ID conversion
			$oldNode1 = $epcs1[$mappingModelKeys[0]]->getLabel($map["nodeIDs"][0]);
			$newNode1 = $epcs2[$mappingModelKeys[0]]->getLabel($conversionMap1[$map["nodeIDs"][0]]);
			$oldNode2 = $epcs1[$mappingModelKeys[1]]->getLabel($map["nodeIDs"][1]);
			$newNode2 = $epcs2[$mappingModelKeys[1]]->getLabel($conversionMap2[$map["nodeIDs"][1]]);
				
			if ( !( $oldNode1 == $newNode1 && $oldNode2 == $newNode2 ) )
			exit("  There is an error!\n    ".$oldNode1." vs. ".$newNode1."\n    ".$oldNode2." vs. ".$newNode2."\n");
				
			// Do ID Conversion
			$mapping->maps[$mapID]["nodeIDs"][0] = $conversionMap1[$map["nodeIDs"][0]];
			$mapping->maps[$mapID]["nodeIDs"][1] = $conversionMap2[$map["nodeIDs"][1]];
		}

		$resultFile = $mapping->exportRDF_BPMContest2015(false, $rdfFilename);
		print("  IDs in ".$resultFile." successfully updated and validated\n");

		$rdfCount++;
		//$progressBar->run($rdfCount);

	}

	print("done\n");
}
?>