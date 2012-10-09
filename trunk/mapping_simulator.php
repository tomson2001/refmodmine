<?php
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Mapping and Similarity Simulator \n-------------------------------------------------\n\n");

$options = array(
	"--ssbocan" => "Similarity Score Based On Common Activity Names",
	"--lms"		=> "Label Matching Similarity",
	"--fbse"    => "Feature-based Similarity Estimation",
	"--pocnae"	=> "Percentage Of Common Nodes And Edges",
	"--geds"	=> "Graph Edit Distance Similarity",
	"--amaged"  => "Activity Matching And Graph Edit Distance",
    "--cf"		=> "Causal Footprints"
);

if ( !isset($argv[1]) || !array_key_exists($argv[1], $options) ) {
	exit("Folgende Aehnlichkeitsmasse stehen Ihenen zur Verfuegung:\n\n
  [--ssbocan]   ".$options["--ssbocan"]."
  [--lms]       ".$options["--lms"]."
  [--fbse]      ".$options["--fbse"]."
  [--pocnae]    ".$options["--pocnae"]."
  [--geds]      ".$options["--geds"]."
  [--amaged]    ".$options["--amaged"]."
  [--cf]        ".$options["--cf"]."\n
  [--help]      Hilfe\n\n");
}

/**
 * Einstellungen
*/
$content_file_1 = file_get_contents(Config::MODEL_FILE_1);
$xml1 = new SimpleXMLElement($content_file_1);
$content_file_2 = file_get_contents(Config::MODEL_FILE_2);
$xml2 = new SimpleXMLElement($content_file_2);

$html_analysis = HTMLComponents::AUTOMAPPING_HEADER;
$analysis_csv = "EPC1;#Functions in EPC1;#Events in EPC1;EPC2;#Functions in EPC2;#Events in EPC2;Eindeutig;".$options[$argv[1]]."\n";

$similarity_matrix_csv = "";
foreach ($xml2->xpath("//epc") as $xml_epc2) {
	$nameOfEPC2 = utf8_decode((string) $xml_epc2["name"]);
	$similarity_matrix_csv .= ";".$nameOfEPC2;
}
$similarity_matrix_csv .= "\n";

$modelsInFile1 = count($xml1->xpath("//epc"));
$modelsInFile2 = count($xml2->xpath("//epc"));
$countCombinations = $modelsInFile1 * $modelsInFile2;

print("Modelldateien:\n");
print("  1. ".Config::MODEL_FILE_1." (".$modelsInFile1." Modelle)\n");
print("  2. ".Config::MODEL_FILE_2." (".$modelsInFile2." Modelle)\n\n");
print("Anzahl Modellkombinationen: ".$countCombinations."\n");
print("Gewaehltes Aehnlichkeitsmass: ".$options[$argv[1]]."\n\n");


$countCompletedCombinations = 0;
$progress = 0.1;

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$similarity_matrix_csv .= $nameOfEPC1;
	foreach ($xml2->xpath("//epc") as $xml_epc2) {
		$nameOfEPC2 = utf8_decode((string) $xml_epc2["name"]);

		// 		print($nameOfEPC1." <=> ".$nameOfEPC2." ... ");

		$html_analysis .= "<h3>".$nameOfEPC1." <=> ".$nameOfEPC2."</h3>";

		// Matrix berechnen
		$epc1 = new EPC($xml1, $xml_epc1["name"]);
		$epc2 = new EPC($xml2, $xml_epc2["name"]);

		$analysis_csv .= $nameOfEPC1.";".count($epc1->functions).";".count($epc1->events).";".$nameOfEPC2.";".count($epc2->functions).";".count($epc2->events).";";

		// Auswahl der Mappings fuer die entsprechenden Aehnlichkeitsmasse
		switch ( $argv[1] ) {
			
			// Funktionen ueber Levenshtein, Konnektoren ueber Ein- und Ausgehende Kanten
			case "--fbse":
				$mapping = new LevenshteinWithStructuralMapping($epc1, $epc2);
				$mapping->setParams(array('threshold_levenshtein' => 91));
				break;
			
			// Identity
			case "--ssbocan":
			case "--pocnae":
			case "--cf":
				$mapping = new LevenshteinMapping($epc1, $epc2);
				$mapping->setParams(array('threshold_levenshtein' => 100));
				break;
			
			// Funktionen ueber Levenshtein und Ein- und Ausgehende Kanten
			case "--amaged":
				$mapping = new LevenshteinWithContextMapping($epc1, $epc2);
				break;
			
			// Funktionen ueber Levenshtein
			default:
				$mapping = new LevenshteinMapping($epc1, $epc2);
				break;
		}
		
		$mapping->map();
		$matrix = $mapping->getMatrix();

		// Matrix in HTML
		$isMappingPrecise = true;
		$html_analysis .= "<table border='1'>";
		$html_analysis .= "<tr><th></th>";
		foreach ($epc2->functions as $label) {
			$html_analysis .= "<th height=".((int) strlen($label)*8.5)."><div class='verticalText'>".$label."</div></th>";
		}
		$html_analysis .= "</tr>";
		foreach ( $matrix as $id1 => $arr ) {
			$label1 = $epc1->getNodeLabel($id1);
			$html_analysis .= "<tr><td>".$label1."</td>";
			$maxLevenshteinSimilarity = Tools::getMaxValueHorizontal($matrix[$id1]);
			foreach ( $arr as $id2 => $value ) {
				$label2 = $epc2->getNodeLabel($id2);
				if ( $mapping->isMapped($id1, $id2) ) {
					if ( $mapping->isMappedPrecisely($id1, $id2) ) {
						$highlight = "bgcolor=green";
					} else {
						$isMappingPrecise = false;
						$highlight = "bgcolor=red";
					}
				} else {
					$highlight = "bgcolor=white";
				}
				$html_analysis .= "<td align='center' ".$highlight.">".$value."</td>";

			}
			$html_analysis .= "</tr>";
		}
		$html_analysis .= "</table>";

		if ( $isMappingPrecise ) {
			$analysis_csv .= "Ja;";
			$html_analysis .= "<br/><font color=green>Mapping ist eindeutig und kann für alle Algorithmen verwendet werden.</font>";
		} else {
			$analysis_csv .= "Nein;";
			$html_analysis .= "<br/><font color=red>Mapping ist nicht eindeutig und kann für bestimmte Algorithmen nicht verwendet werden!</font>";
		}

		if ( $argv[1] == "--ssbocan" ) {
			$SimilarityScoreBasedOnCommonActivityNames = new SimilarityScoreBasedOnCommonActivityNames($mapping);
			$similarityValue = $SimilarityScoreBasedOnCommonActivityNames->calculate();
		}

		if ( $argv[1] == "--lms" ) {
			$labelMatchingSimilarity = new LabelMatchingSimilarity($mapping);
			$similarityValue = $labelMatchingSimilarity->calculate();
		}
		
		if ( $argv[1] == "--pocnae" ) {
			$PercentageOfCommonNodesAndEdges = new PercentageOfCommonNodesAndEdges($mapping);
			$similarityValue = $PercentageOfCommonNodesAndEdges->calculate();
		}
		
		if ( $argv[1] == "--geds" ) {
			$GraphEditDistanceSimilarity = new GraphEditDistanceSimilarity($mapping);
			$similarityValue = $GraphEditDistanceSimilarity->calculate();
		}
		
		if ( $argv[1] == "--cf" ) {
			$CausalFootprints = new CausalFootprints($mapping);
			$similarityValue = $CausalFootprints->calculate();
		}
		
		if ( $argv[1] == "--amaged" ) {
			$CombiningActivityMatchingAndAGraphEditDistance = new CombiningActivityMatchingAndAGraphEditDistance($mapping);
			$similarityValue = $CombiningActivityMatchingAndAGraphEditDistance->calculate();
		}
		
		if ( $argv[1] == "--fbse" ) {
			$SimilarityScoreBasedOnCommonActivityNames = new SimilarityScoreBasedOnCommonActivityNames($mapping);
			$similarityValue = $SimilarityScoreBasedOnCommonActivityNames->calculate();
		}
		
		$analysis_csv .= $similarityValue."\n";
		$html_analysis .= "<br/>".$options[$argv[1]].": ".$similarityValue."%<br/>";
		$similarity_matrix_csv .= ";".$similarityValue;

		print(".");
		$countCompletedCombinations++;

		if ( ($countCompletedCombinations/$countCombinations) >= $progress ) {
			print(" ".($progress*100)."% ");
			$progress += 0.1;
		}

	}
	$similarity_matrix_csv .= "\n";
}

$html_analysis .= "</body></html>";

$fileGenerator = new FileGenerator("Mappings.html", $html_analysis);
$uri_html_analysis = $fileGenerator->execute();

$fileGenerator->setFilename("Mapping_Analysis.csv");
$fileGenerator->setContent($analysis_csv);
$uri_analysis_csv = $fileGenerator->execute();

$fileGenerator->setFilename("Model_Similarity_Matrix.csv");
$fileGenerator->setContent($similarity_matrix_csv);
$uri_similarity_matrix = $fileGenerator->execute();

print("\n\nAnalysedateien wurden erfolgreich erstellt:\n\n");
print("HTML mit Mappings: ".$uri_html_analysis."\n");
print("CSV mit Analyseergebnissen: ".$uri_analysis_csv."\n");
print("Label Matching Similarity Matrix: ".$uri_similarity_matrix."\n\n");

?>
