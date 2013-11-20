<?php
/**
 * Mapping Kalkulator - Speziell fuer den Mapping-Contest der BPM2013 konzipiert:
 * http://processcollections.org/matching-contest
 */
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Mapping Calculator \n-------------------------------------------------\n\n");

// Hilfeanzeige auf CLI
if ( in_array("--help", $argv) || in_array("-help", $argv) || in_array("-?", $argv) || in_array("--?", $argv) ) {
	exit("   Optionen:\n
   [--nary]     N-Aeres Mapping
   [--help]     Hilfe\n\n");
}

// Laden der Modelldateien
$content_file_1 = file_get_contents(Config::MODEL_FILE_1);
$xml1 = new SimpleXMLElement($content_file_1);
$content_file_2 = file_get_contents(Config::MODEL_FILE_2);
$xml2 = new SimpleXMLElement($content_file_2);

// Vorbereitung der Forschrittsanzeige
$modelsInFile1 = count($xml1->xpath("//epc"));
$modelsInFile2 = count($xml2->xpath("//epc"));
$countCombinations = $modelsInFile1 * $modelsInFile2;
$countCompletedCombinations = 0;
$progress = 0.1;

// Ausgabe der Informationen zum Skript-Run auf der Kommandozeile
print("Modelldateien:\n");
print("  1. ".Config::MODEL_FILE_1." (".count($xml1->xpath("//epc"))." Modelle)\n");
print("  2. ".Config::MODEL_FILE_2." (".count($xml2->xpath("//epc"))." Modelle)\n\n");
print("Anzahl Modellkombinationen: ".$countCombinations."\n");

// ReadMe.txt erzeugen
$readme =  "-----------------------------------\r\n";
$readme .= " RefModMining - Mapping Calculator\r\n";
$readme .= "-----------------------------------\r\n\r\n";
$readme .= "Ausfuehrungsinformationen:\r\n";
$readme .= " - Erste Modelldatei:  ".Config::MODEL_FILE_1." (".$modelsInFile1." Modelle)\r\n";
$readme .= " - Zweite Modelldatei: ".Config::MODEL_FILE_2." (".$modelsInFile2." Modelle)\r\n";
$readme .= " - Anzahl Modellkombinationen: ".$countCombinations."\r\n";
$readme .= " - Startzeit: ".date("d.m.Y H:i:s")."\r\n\r\n";

$analysis_csv = "EPC1;#Functions in EPC1;#Events in EPC1;EPC2;#Functions in EPC2;#Events in EPC2;1:1-Mapping;#Node-Mapping\n";
$html_analysis = HTMLComponents::AUTOMAPPING_HEADER;

print("Berechnung der Mappings...\n");

// Bei Bedarf N-Aeres Mapping erstellen
//$naryMapping = new NAryOntologyMappingContestGold();
//$naryMapping = new NAryWordstemMapping();
$naryMapping = new NAryWordstemMappingWithAntonyms();
//$naryMapping = new NAryOntologyMappingContestGoldV2();
if ( in_array("--nary", $argv) ) {
	foreach ($xml1->xpath("//epc") as $xml_epc1) {
		$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
		$epc = new EPC($xml1, $xml_epc1["name"]);
		$naryMapping->addEPC($epc);
	}
	foreach ($xml2->xpath("//epc") as $xml_epc2) {
		$nameOfEPC2 = utf8_decode((string) $xml_epc2["name"]);
		$epc = new EPC($xml2, $xml_epc2["name"]);
		$naryMapping->addEPC($epc);
	}
	$naryMapping->map();
	//$naryMapping->exportDebug();
}

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$epc1 = new EPC($xml1, $xml_epc1["name"]);

	foreach ($xml2->xpath("//epc") as $xml_epc2) {
		$nameOfEPC2 = utf8_decode((string) $xml_epc2["name"]);
		$epc2 = new EPC($xml2, $xml_epc2["name"]);
		
		if ($nameOfEPC1 != $nameOfEPC2) continue;

		$html_analysis .= "<h3>".$nameOfEPC1." <=> ".$nameOfEPC2."</h3>";

		// Matrix berechnen
		$analysis_csv .= $nameOfEPC1.";".count($epc1->functions).";".count($epc1->events).";".$nameOfEPC2.";".count($epc2->functions).";".count($epc2->events).";";

		// Auswahl des Mappings
		$mapping = null;
		if ( in_array("--nscm", $argv) ) {
			$mapping = $naryMapping->extractBinaryMapping($epc1, $epc2);
		} else {
			//$mapping = new LevenshteinWithStructuralMapping($epc1, $epc2);
			//$mapping->setParams(array('threshold_levenshtein' => 91));
			$mapping = new LevenshteinMapping($epc1, $epc2);
			$mapping->setParams(array('threshold_levenshtein' => 100));
			//$mapping->setParams(array('threshold_levenshtein' => 50));
			//$mapping = new LevenshteinWithContextMapping($epc1, $epc2);
			//$mapping->setParams(array('threshold_levenshtein' => 30));
			//$mapping = new OntologyWithWordstemMapping($epc1, $epc2);
			//$mapping->setParams(array('threshold_ontology_quote' => 51));
		}

		/**
		 * Angabe des Algorithmus, der fuer das Mapping verwendet werden soll: "Greedy", "Simple"
		*/
		$mapping->map("Greedy");
		//$mapping->map("AllOne");
		$mapping->deleteDummyTransitions();
		$mapping->export();
		$matrix = $mapping->getMatrix();

		// Matrix in HTML
		$isMappingPrecise = true;
		$html_analysis .= "<table border='1'>";
		$html_analysis .= "<tr><th></th>";
		foreach ($epc2->functions as $func_id => $label) {
			$html_analysis .= "<th height=".((int) strlen($label)*8.5)."><div class='verticalText'>".$label." (".$func_id.")</div></th>";
		}
		$html_analysis .= "</tr>";
		foreach ( $matrix as $id1 => $arr ) {
			$label1 = $epc1->getNodeLabel($id1);
			$html_analysis .= "<tr><td>".$label1." (".$id1.")</td>";
			$maxLevenshteinSimilarity = Tools::getMaxValueHorizontal($matrix[$id1]);
			foreach ( $arr as $id2 => $value ) {
				$label2 = $epc2->getNodeLabel($id2);
				if ( $mapping->isMapped($id1, $id2) ) {
					$highlight = "bgcolor=green";
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
			$html_analysis .= "<br/>Es handelt sich um ein 1:1 Mapping.";
		} else {
			$analysis_csv .= "Nein;";
			$html_analysis .= "<br/>Es handelt sich um ein m:n Mapping.";
		}

		$analysis_csv .= count($mapping->mapping)."\n";

		// FORTSCHRITTSANZEIGE
		print(".");
		$countCompletedCombinations++;

		if ( ($countCompletedCombinations/$countCombinations) >= $progress ) {
			print(" ".($progress*100)."% ");
			$progress += 0.1;
		}
		// ENDE DER FORTSCHRITTSANZEIGE

	}
}

print(" done");
$html_analysis .= "</body></html>";

// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator("Mapping_Analysis.csv", $analysis_csv);
$uri_analysis_csv = $fileGenerator->execute();

$fileGenerator->setFilename("Mappings.html");
$fileGenerator->setContent($html_analysis);
$uri_html_analysis = $fileGenerator->execute();

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme .= "\nEndzeit: ".date("d.m.Y H:i:s")."\r\n";
$readme .= "Dauer: ".$minutes." Min. ".$seconds." Sek.";
$fileGenerator->setFilename("ReadMe.txt");
$fileGenerator->setContent($readme);
$uri_readme_txt = $fileGenerator->execute();
// AUSGABEDATEIEN ERSTELLT

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nAnalysedateien wurden erfolgreich erstellt:\n\n");
print("ReadMe: ".$uri_readme_txt."\n\n");
print("HTML mit Mappings: ".$uri_html_analysis."\n");
print("CSV mit Analyseergebnissen: ".$uri_analysis_csv."\n");

print("Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n");

?>
