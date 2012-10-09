<?php
include 'header.html';
require 'autoloader.php';

/**
 * Einstellungen
 */
$content_file_1 = file_get_contents(Config::MODEL_FILE_1);
$xml1 = new SimpleXMLElement($content_file_1);
$content_file_2 = file_get_contents(Config::MODEL_FILE_2);
$xml2 = new SimpleXMLElement($content_file_2);

echo "<form method='post' action='mapping.php'>";

// Auswahl der EPKs
$epcSelector = new EPCSelector($xml1, $xml2);
echo $epcSelector->getHtml();

// Welcher Werte sollen in der Tabelle angezeigt werden?
$mappingOptions = new MappingViewOptions();
echo $mappingOptions->getHtml();

echo "<input type='submit' value='ok'></form><br />";


/**
 * Mapping-Tabelle
 */
if ( isset($_POST['epc1']) && isset($_POST['epc1']) ) {

	// Matrix berechnen
	$epc1 = new EPC($xml1, $_POST['epc1']);
	$epc2 = new EPC($xml2, $_POST['epc2']);
	$levenshteinMapping = new LevenshteinMapping($epc1, $epc2);
	if ( $mappingOptions->posted == 'identity' ) {
		$levenshteinMapping->setParams(array('threshold' => 100));
	}
	$levenshteinMapping->map();
	$matrix = $levenshteinMapping->getMatrix();

	$isMappingPrecise = true;
	echo "<table border='1'>";
	echo "<tr><th></th>";
	foreach ($epc2->functions as $label) {
		echo "<th height=".((int) strlen($label)*8.5)."><div class='verticalText'>".$label."</div></th>";
	}
	echo "</tr>";
	foreach ( $matrix as $id1 => $arr ) {
		$label1 = $epc1->getNodeLabel($id1);
		echo "<tr><td>".$label1."</td>";
		$maxLevenshteinSimilarity = Tools::getMaxLevenshteinSimilarity($matrix[$id1]);
		foreach ( $arr as $id2 => $value ) {
			$label2 = $epc2->getNodeLabel($id2);
			if ( $levenshteinMapping->isMapped($id1, $id2) ) {
				if ( $levenshteinMapping->isMappedPrecisely($id1, $id2) ) {
					$highlight = "bgcolor=green";
				} else {
					$isMappingPrecise = false;
					$highlight = "bgcolor=red";
				}
			} else {
				$highlight = "bgcolor=white";
			}
			echo "<td align='center' ".$highlight.">".$value."</td>";

		}
		echo "</tr>";
	}
	echo "</table>";

	if ( $isMappingPrecise ) {
		echo "<br/><font color=green>Mapping ist eindeutig und kann für alle Algorithmen verwendet werden.</font>";
	} else {
		echo "<br/><font color=red>Mapping ist nicht eindeutig und kann für bestimmte Algorithmen nicht verwendet werden!</font>";
	}
	
	$SimilarityScoreBasedOnCommonActivityNames = new SimilarityScoreBasedOnCommonActivityNames($levenshteinMapping);
	$similarityValue = $SimilarityScoreBasedOnCommonActivityNames->calculate();
	echo "<br/><br/>Similarity score based on common activity names: ".$similarityValue."%";
	
	$labelMatchingSimilarity = new LabelMatchingSimilarity($levenshteinMapping);
	$similarityValue = $labelMatchingSimilarity->calculate();
	echo "<br/>Label Matching Similarity: ".$similarityValue."%";
}
?>
</body>
</html>
