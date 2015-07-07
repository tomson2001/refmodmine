<?php
$start = time();
require 'autoloader.php';

print("\n-------------------------------------------------\n RefModMining - Correlation Calculator \n-------------------------------------------------\n\n");

// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) ) {
	exit("   Please provide the following parameters:\n
   input=           path to input csv (different similarity values for model combinations)
                    (possible values: ssbocan, lms, fbse, pocnae, geds, amaged, cf, lcsot, ts, tswf, reihe1, reihe2, time, traces)
   output=          path to output csv (correlation matrix)
   notification=
      no
      [E-Mail adress]

   please user the correct order!
		
ERROR: Parameters incomplete
");
}

$similarityMeasures = array(
		"ssbocan" 	=> "similarity score based on common activity names",
		"lms"		=> "label matching similarity",
		"fbse"    	=> "feature based similarity estimation",
		"pocnae"	=> "percentage of common nodes and edges",
		"geds"		=> "graph edit distance similarity",
		"amaged"  	=> "activity matching and graph edit distance",
		"cf"		=> "causal footprints",
		"lcsot"		=> "longest common subsequence of traces",
		"ts"		=> "terminology similarity",
		"tswf"		=> "terminology similarity with frequencies",
		"reihe1"    => "reihe1",
		"reihe2"    => "reihe2",
		"time"		=> "time",
		"traces"    => "traces"
);

$flippedSimilarityMeasures = array_flip($similarityMeasures);

$readme = "";

// Checking Parameters
$input   = substr($argv[1], 6,  strlen($argv[1]));
$output   = substr($argv[2], 7,  strlen($argv[2]));
$email   = substr($argv[3], 13, strlen($argv[3]));

print("
input: ".$input."
output: ".$output."
notification: ".$email."

checking input parameters ...
");

// Check input
if ( file_exists($input) ) {
	print "  input ... ok\n";
} else {
	exit("  input ... failed (file does not exist)\n\n");
}

// Check notification
$doNotify = true;
if ( empty($email) || $email == "no" ) {
	$doNotify = false;
	print "  notification ... ok (notification disabled)\n";
} else {
	print "  notification ... ok (mail to ".$email.")\n";
}



$corrArr = array();
$foundMeasures = array();
$empiricValueSeries = array();

print("Quelldatei: ".$input."\n\n");

// Laden des CSV-File
print("CSV wird geladen... ");
if (($handle = fopen($input, "r")) !== FALSE) {
	print("ok\n");
	
	// Header leaden
	$header = fgetcsv($handle, 0, ";");
	$fields = count($header);

	print("Erkennung der Aehnlichkeitsmasse... ");
	// Identifizierung der im CSV enthaltenen Aehnlichkeitsma�e
	for ( $fieldNum = 0; $fieldNum < $fields; $fieldNum++ ) {
		if ( array_key_exists(strtolower($header[$fieldNum]), $similarityMeasures) ) {
			$foundMeasures[$fieldNum] = strtoupper($header[$fieldNum]);
		} elseif ( array_key_exists(strtolower($header[$fieldNum]), $flippedSimilarityMeasures) ) {
			$foundMeasures[$fieldNum] = strtoupper($flippedSimilarityMeasures[strtolower($header[$fieldNum])]);
		}
	}
	
	// Header in die Korrelationsmatrix schreiben und Empirische Reihen erstellen
	foreach ( $foundMeasures as $fieldNum => $measure ) {
		$corrArr[$measure] = array();
		$empiricValueSeries[$fieldNum] = new EmpiricValueSeries($measure);
		print($measure." ");
	}
	print("\n\n");
	if ( empty($foundMeasures) ) {
		exit("Es konnte keine Aehnlichkeitsmasse gefunden werden.\n\n");
	}
	
	// Auslesen der empirischen Werte
	print("Einlesen empirische Wertereihen... ");
	$counter = 0;
	while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
		foreach ( $foundMeasures as $fieldNum => $measure ) {
			$empiricValueSeries[$fieldNum]->add($data[$fieldNum]);
		}
		$counter++;
	}
	print($counter."\n");
	
	// Berechnung der empirischen Korrelationsmatrix
	print("Berechnung der Korrelationsmatrix");
	// immer nach zwei Aehnlichkeitsmassen einen Fortschrittspunkt anzeigen 
	$printPoint = true;
	foreach ( $foundMeasures as $fieldNum1 => $measure1 ) {
		foreach ( $foundMeasures as $fieldNum2 => $measure2 ) {
			// Berechnung des empirischen Korrelationskoeffizienten zwischen Measure1 und Measure2
			$calculator = new EmpiricCorrelationCalculator($empiricValueSeries[$fieldNum1], $empiricValueSeries[$fieldNum2]);
			$corrArr[$measure1][$fieldNum2] = round($calculator->getCorrelation(), 2);
			if ( $printPoint ) {
				print(".");
			}
			$printPoint = !$printPoint;
		}
	}
	print(" ok\n");
	
	// CSV-File schliessen
	fclose($handle);
	
	// Schreiben der Korrelationsmatrix als CSV
	print("Generiere CSV... ");
	$csv = "";
	foreach ( $foundMeasures as $measure ) {
		$csv .= ";".$measure;
	}
	
	foreach ( $corrArr as $measure => $corrValues ) {
		$csv .= "\n".$measure;
		foreach ( $corrValues as $measureIndex => $corrValue ) {
			$csv .= ";".str_replace(".", ",", $corrValue);
		}
	}
	
	$fileGenerator = new FileGenerator($output, $csv);
	$fileGenerator->setPathFilename($output);
	$fileGenerator->setContent($csv);
	$uri_corr_matrix = $fileGenerator->execute(false);
	print("ok\n\n");
	print("Correlation matrix file: ".$uri_corr_matrix);
	
	// Berechnungdauer
	$duration = time() - $start;
	$seconds = $duration % 60;
	$minutes = floor($duration / 60);
	print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n\n");
	
	$readme  = "Correlation calculation ".$input." successfully finished.";
	$sid = $uri_corr_matrix;
	$sid = str_replace("workspace/", "", $sid);
	$pos = strpos($sid, "/");
	$sid = $pos ? substr($sid, 0, $pos) : $sid;
	$readme .= "\n\nYour workspace: ".Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";
	
	Logger::log($email, "CLICorrelationCalculator finished: input=".$input." output=".$output, "ACCESS");
	
} else {
	$readme  = "Correlation calculation ".$input." failed.";
	Logger::log($email, "CLICorrelationCalculator failed: input=".$input." output=".$output, "ACCESS");
	Logger::log($email, "CLICorrelationCalculator failed: input=".$input." output=".$output, "ERROR");
	print("failure\n\n");
}

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLICorrelationCalculatorNotification($email, $readme);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

?>