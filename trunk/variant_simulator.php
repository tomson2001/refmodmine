<?php
$start = time();
require 'autoloader.php';
include 'variant_simulator_settings.php';

print("Berechnung der Variantenaehnlichkeiten...\n");

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);

	if ( !in_array($nameOfEPC1, $ignore_models) ) {
		
		//print($nameOfEPC1);

		$epc1 = new EPC($xml1, $xml_epc1["name"]);
		if ( $argv[1] == "--lcsot") {
			$traceExtractor = new TraceExtractor($epc1);
			$epcTraces = $traceExtractor->execute();
			$epc1->traces = $epcTraces;
		}

		$similarity_matrix_csv .= $nameOfEPC1;

		// Erstellen der verschiedenen Varianten
		$variants = array();

		$variantGenerator = new VariantGenerator($epc1, 1, 0, 0, 0);
		$epc_variant = $variantGenerator->getVariant();
		$epc_variant->name .= " (Variante 1)";
		array_push($variants, $epc_variant);

		$variantGenerator = new VariantGenerator($epc1, 0, 1, 0, 0);
		$epc_variant = $variantGenerator->getVariant();
		$epc_variant->name .= " (Variante 2)";
		array_push($variants, $epc_variant);

		$variantGenerator = new VariantGenerator($epc1, 0, 0, 1, 0);
		$epc_variant = $variantGenerator->getVariant();
		$epc_variant->name .= " (Variante 3)";
		array_push($variants, $epc_variant);

		$variantGenerator = new VariantGenerator($epc1, 0, 0, 0, 1);
		$epc_variant = $variantGenerator->getVariant();
		$epc_variant->name .= " (Variante 4)";
		array_push($variants, $epc_variant);

		$variantGenerator = new VariantGenerator($epc1, 1, 1, 1, 1, 3);
		$epc_variant = $variantGenerator->getVariant();
		$epc_variant->name .= " (Variante 5)";
		array_push($variants, $epc_variant);
		// ENDE DER VARIANTENERSTELLUNG

		foreach ($variants as $index => $epc2) {
			
			$traceExtractor = null;
			
			if ( $argv[1] == "--lcsot") {
				$traceExtractor = new TraceExtractor($epc2);
				$epcTraces = $traceExtractor->execute();
				$epc2->traces = $epcTraces;
			}

			$nameOfEPC2 = $epc2->name;

			$html_analysis .= "<h3>".$nameOfEPC1." <=> ".$nameOfEPC2."</h3>";

			// Matrix berechnen
			$analysis_csv .= $nameOfEPC1.";".count($epc1->functions).";".count($epc1->events).";".$nameOfEPC2.";".count($epc2->functions).";".count($epc2->events).";";

			// Auswahl der Mappings fuer die entsprechenden Aehnlichkeitsmasse
			switch ( $argv[1] ) {

				// Funktionen ueber Levenshtein, Konnektoren ueber Ein- und Ausgehende Kanten
				case "--fbse":
					$mapping = new LevenshteinWithStructuralMapping($epc1, $epc2);
					//$mapping->setParams(array('threshold_levenshtein' => 91));
					break;

					// Identity
				case "--ssbocan":
				case "--pocnae":
				case "--cf":
					$mapping = new LevenshteinMapping($epc1, $epc2);
					$mapping->setParams(array('threshold_levenshtein' => 50));
					//$mapping->setParams(array('threshold_levenshtein' => 100));
					break;

					// Funktionen ueber Levenshtein und Ein- und Ausgehende Kanten
				case "--amaged":
					$mapping = new LevenshteinWithContextMapping($epc1, $epc2);
					break;

					// kein Mapping
				case "--ts":
					$mapping = null;
					break;

					// Funktionen ueber Levenshtein
				default:
					$mapping = new LevenshteinMapping($epc1, $epc2);
					// Grenze auf 50% Aehnlichkeit setzen
					$mapping->setParams(array('threshold_levenshtein' => 50));
					break;
			}

			/**
			 * Angabe des Algorithmus, der fuer das Mapping verwendet werden soll: "Greedy", "Simple"
			 */
			$mapping->map("Greedy");
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
				$html_analysis .= "<br/><font color=green>Mapping ist eindeutig und kann für alle Algorithmen verwendet werden.</font>";
			} else {
				$analysis_csv .= "Nein;";
				$html_analysis .= "<br/><font color=red>Mapping ist nicht eindeutig und kann für bestimmte Algorithmen nicht verwendet werden!</font>";
			}

			// Berechnung des Aehnlichkeitsmaßes
			$measure = null;
			if ( $doMapping ) {
				eval("\$measure = new ".str_replace(" ", "", trim($similarityMeasures[$argv[1]]))."(\$mapping);");
			} else {
				eval("\$measure = new ".str_replace(" ", "", trim($similarityMeasures[$argv[1]]))."(\$epc1, \$epc2);");
			}
			$similarityValue = $measure->calculate();

			// Aehnlichkeitswert in die Zieldateien schreiben
			$analysis_csv .= $similarityValue."\n";
			$html_analysis .= "<br/>".$similarityMeasures[$argv[1]].": ".$similarityValue."%<br/>";
			$similarity_matrix_csv .= ";".$similarityValue;

			// FORTSCHRITTSANZEIGE
			print(".");
			$countCompletedCombinations++;

			if ( ($countCompletedCombinations/$countCombinations) >= $progress ) {
				print(" ".($progress*100)."% ");
				$progress += 0.1;
			}
			// ENDE DER FORTSCHRITTSANZEIGE

		}
		$similarity_matrix_csv .= "\n";
	}
}
$html_analysis .= "</body></html>";

// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator("Mappings.html", $html_analysis);
$uri_html_analysis = $fileGenerator->execute();

$fileGenerator->setFilename("Mapping_Analysis.csv");
$fileGenerator->setContent($analysis_csv);
$uri_analysis_csv = $fileGenerator->execute();

$fileGenerator->setFilename("Model_Similarity_Matrix.csv");
$fileGenerator->setContent($similarity_matrix_csv);
$uri_similarity_matrix = $fileGenerator->execute();
// AUSGABEDATEIEN ERSTELLT

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nAnalysedateien wurden erfolgreich erstellt:\n\n");
print("HTML mit Mappings: ".$uri_html_analysis."\n");
print("CSV mit Analyseergebnissen: ".$uri_analysis_csv."\n");
print("Label Matching Similarity Matrix: ".$uri_similarity_matrix."\n\n");

$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);
print("Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n");

?>
