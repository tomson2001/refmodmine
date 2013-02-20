<?php
$start = time();
require 'autoloader.php';
include 'simulator_settings.php';

// Vorberechnung von Traces, falls diese benoetigt werden
$timeExceeded = 0;
$traces = array();
if ( $argv[1] == "--lcsot") {
	print("Berechnung aller moeglichen Traces...\n");
	$readme .= "Traces...\r\n";

	// CSV mit den Traces aller EPKs wird nach jeder Trace-Extraktion aktualisiert
	$traces_csv = "";
	$i = 1;
	foreach ($xml1->xpath("//epc") as $xml_epc1) {
		$lastTime = time();

		$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
		$epc1 = new EPC($xml1, $xml_epc1["name"]);

		if ( !in_array($nameOfEPC1, $ignore_models) ) {
			$traceExtractor = null;
			if ( in_array("--exportEPML", $argv) ) {
				$traceExtractor = new TraceExtractor($epc1, true, Config::MAX_TIME_PER_TRACE_EXTRAKTION);
			} else {
				$traceExtractor = new TraceExtractor($epc1, false, Config::MAX_TIME_PER_TRACE_EXTRAKTION);
			}
			$epcTraces = $traceExtractor->execute();

			// Berechnungdauer
			$duration = time() - $lastTime;
			$seconds = $duration % 60;
			$minutes = floor($duration / 60);
			
			if (is_string($epcTraces)) {
				if ( $epcTraces == "Time exceeded" ) $timeExceeded++;
				$traces_csv .= "   ".$i.": ".$nameOfEPC1." (".$epcTraces.") - Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n";
				$readme .= "   ".$i.": ".$nameOfEPC1." (".$epcTraces.") - Dauer: ".$minutes." Min. ".$seconds." Sek.\r\n";
				print("   ".$i.": ".$nameOfEPC1.": ".$epcTraces." - Dauer: ".$minutes." Min. ".$seconds." Sek.\n");
			} else {
				$traces_csv .= "   ".$i.": ".$nameOfEPC1." (".count($epcTraces)." Traces) - Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n";
				if ( in_array("--exportTraces", $argv) ) {
					$exportFile = $traceExtractor->exportCSV();
				}

				foreach ($epcTraces as $trace) {
					foreach ($trace as $funcID) {
						$traces_csv .= str_replace("\n", " ", str_replace(";", ",", $epc1->getNodeLabel($funcID))).";";
					}
					$traces_csv .= "\n";
				}
				$traces_csv .= "\n\n";
				$fileGenerator = new FileGenerator(date('Y-m-d_H-i-s', $start)."_Traces.csv", $traces_csv);
				$file = $fileGenerator->execute(false);

				$readme .= "   ".$i.": ".$nameOfEPC1." (".count($epcTraces)." Traces) - Dauer: ".$minutes." Min. ".$seconds." Sek.\r\n";
				print("   ".$i.": ".$nameOfEPC1.": ".count($epcTraces)." Traces - Dauer: ".$minutes." Min. ".$seconds." Sek.\n");

				$traces[$nameOfEPC1] = $epcTraces;
			}
		} else {
			$traces_csv .= "   ".$i.": ".$nameOfEPC1." IGNORE\n\n";
			$readme .= "   ".$i.": ".$nameOfEPC1." IGNORE\r\n";
			print("   ".$i.": ".$nameOfEPC1.": IGNORE\n");
		}
		$i++;
	}
	print("... done (ignored: ".count($ignore_models).", time exceeded: ".$timeExceeded.", overall passed: ".(count($ignore_models)+$timeExceeded).") \n\n");
}

print("Berechnung der Modellaehnlichkeiten...\n");

// Aehnlichkeitsmatrix in CSV vorbereiten
$similarity_matrix_csv = "";
foreach ($xml2->xpath("//epc") as $xml_epc2) {
	$nameOfEPC2 = utf8_decode((string) $xml_epc2["name"]);
	if ( !in_array($nameOfEPC2, $ignore_models) || 
		($argv[1] == "--lcsot" && array_key_exists($nameOfEPC2, $traces) 
		 && !is_string($traces[$nameOfEPC2]) && !empty($traces[$nameOfEPC2])) 
	) $similarity_matrix_csv .= ";".$nameOfEPC2;
}
$similarity_matrix_csv .= "\n";

foreach ($xml1->xpath("//epc") as $xml_epc1) {
	$nameOfEPC1 = utf8_decode((string) $xml_epc1["name"]);
	$epc1 = new EPC($xml1, $xml_epc1["name"]);

	if ( $argv[1] == "--lcsot" ) {
		if ( array_key_exists($nameOfEPC1, $traces) 
			&& !is_string($traces[$nameOfEPC1]) 
			&& !empty($traces[$nameOfEPC1]) 
			&& !in_array($nameOfEPC1, $ignore_models) 
		) $similarity_matrix_csv .= $nameOfEPC1;
	} else {
		if ( !in_array($nameOfEPC1, $ignore_models) ) $similarity_matrix_csv .= $nameOfEPC1;
	}


	foreach ($xml2->xpath("//epc") as $xml_epc2) {
		$nameOfEPC2 = utf8_decode((string) $xml_epc2["name"]);
		$epc2 = new EPC($xml2, $xml_epc2["name"]);

		if ( ($argv[1] == "--lcsot" 
			&& array_key_exists($nameOfEPC1, $traces) && !is_string($traces[$nameOfEPC1]) && !empty($traces[$nameOfEPC1]) 
			&& array_key_exists($nameOfEPC2, $traces) && !is_string($traces[$nameOfEPC2]) && !empty($traces[$nameOfEPC2])
			&& !in_array($nameOfEPC2, $ignore_models) && !in_array($nameOfEPC2, $ignore_models)) 
			|| ($argv[1] != "--lcsot" && !in_array($nameOfEPC1, $ignore_models) && !in_array($nameOfEPC2, $ignore_models)) ) 
		{

			if (!$isLight) $html_analysis .= "<h3>".$nameOfEPC1." <=> ".$nameOfEPC2."</h3>";

			// Traces falls notwendig an die EPKs dranhaengen
			if ( $argv[1] == "--lcsot") {
				if ( is_string($traces[$nameOfEPC1]) || is_string($traces[$nameOfEPC2]) ) {

					// FORTSCHRITTSANZEIGE
					print("drop(".$nameOfEPC1.", ".$nameOfEPC2.")");
					$countCompletedCombinations++;

					if ( ($countCompletedCombinations/$countCombinations) >= $progress ) {
						print(" ".($progress*100)."% ");
						$progress += 0.1;
					}
					// ENDE DER FORTSCHRITTSANZEIGE

					continue;
				}
				$epc1->traces = $traces[$nameOfEPC1];
				$epc2->traces = $traces[$nameOfEPC2];
			}

			// Matrix berechnen
			$analysis_csv .= $nameOfEPC1.";".count($epc1->functions).";".count($epc1->events).";".$nameOfEPC2.";".count($epc2->functions).";".count($epc2->events).";";

			// Variablen-Initalisierung
			$mapping = null;

			if ( $doMapping ) {

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
						$mapping->setParams(array('threshold_levenshtein' => 100));
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
				if (!$isLight) {
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
					
					$analysis_csv .= count($mapping->mapping).";";
					if ( $argv[1] == "--lcsot" ) $analysis_csv .= count($epc1->traces).";".count($epc2->traces).";";
				}

			} else {
				if (!$isLight) $analysis_csv .= "-/-;";
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
			if (!$isLight) $analysis_csv .= $similarityValue."\n";
			if (!$isLight) $html_analysis .= "<br/>".$similarityMeasures[$argv[1]].": ".$similarityValue."%<br/>";
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
	}
	if ( ($argv[1] == "--lcsot" 
		&& array_key_exists($nameOfEPC1, $traces) && !is_string($traces[$nameOfEPC1]) && !empty($traces[$nameOfEPC1]) 
		&& array_key_exists($nameOfEPC2, $traces) && !is_string($traces[$nameOfEPC2]) && !empty($traces[$nameOfEPC2])) 
		|| ($argv[1] != "--lcsot" && !in_array($nameOfEPC1, $ignore_models) && !in_array($nameOfEPC2, $ignore_models)) ) {
		$similarity_matrix_csv .= "\n";
	}
}
print(" done");
if (!$isLight) $html_analysis .= "</body></html>";

// ERSTELLEN DER AUSGABEDATEIEN
$fileGenerator = new FileGenerator("Model_Similarity_Matrix.csv", $similarity_matrix_csv);
if ( $doMapping ) $uri_similarity_matrix = $fileGenerator->execute();

if (!$isLight) {
	$fileGenerator->setFilename("Mapping_Analysis.csv");
	$fileGenerator->setContent($analysis_csv);
	$uri_analysis_csv = $fileGenerator->execute();

	$fileGenerator->setFilename("Mappings.html");
	$fileGenerator->setContent($html_analysis);
	$uri_html_analysis = $fileGenerator->execute();
}

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
print("Aehnlichkeits-Matrix: ".$uri_similarity_matrix."\n");
print("ReadMe: ".$uri_similarity_matrix."\n\n");

if (!$isLight) {
	print("HTML mit Mappings: ".$uri_html_analysis."\n");
	print("CSV mit Analyseergebnissen: ".$uri_analysis_csv."\n");
}

print("Dauer: ".$minutes." Min. ".$seconds." Sek.\n\n");

?>
