<?php
/**
 * Konfigurationsklasse
 * 
 * @author Tom Thaler
 *
 */
final class Config {
	
	// ------- GENERAL SETTINGS --------------------------------------------------------------------------------
	
	// Path and Mail Configuration
	const WEB_PATH = "http://rmm.dfki.de/";
	const NO_REPLY_MAIL = "no-reply@rmm.dfki.de";
	const ABS_PATH = "/var/www";
	const FILES_PATH = "files";
	const WORKSPACE_PATH = "workspace";
	const REPOSITORY_PATH = "repository";
	const STANDFORD_POS_TAGGER_PATH = "/var/www/lib/stanford-postagger-full-2015-01-30/";
	const WORDNET_EXE = "wordnet";
	//const WORDNET_EXE = "\"c:\\Program Files (x86)\\WordNet\\2.1\\bin\\wn.exe\"";
	const REFMOD_MINER_JAVA_PATH_WITH_FILENAME = "/var/www/lib/refmod-miner/master.jar";
	
	// Some Settings
	const NUM_CORES_TO_WORK_ON = 1;
	const WORDNET_SYNONYM_LIMIT = 7; // Anzahl der zu beruecksichtigen Bedeutungen. Ersten Tests zufolge mindestens 7, MUSS < 10 sein!!!!
	const MAX_TIME_PER_TRACE_EXTRAKTION = 360; // in Sekunden, 0=keine Beschraenkung
	const FIX_POINT_ARITHMETIC = false; // Einstellen einer Fixpunktarithmetik: false => Ausgeschaltet, [0-x] => Fixpunkt
	const STANFORD_POS_TAGGER_MODEL = 'english-left3words-distsim.tagger'; // german-hgc.tagger | german-fast.tagger | german-dewac.tagger | english-bidirectional-distsim.tagger
		
	// Logging
	const ENABLE_ERROR_LOGGING = true;
	const ERROR_LOG = "log/error.log";
	const ENABLE_ACCESS_LOGGING = true;
	const ACCESS_LOG = "log/access.log";
	
	// ------ END GENERAL SETTINGS -----------------------------------------------------------------------
	
	// Some individual Stuff
	const MODEL_FILE_1 = "/home/toth01/refmodmine/input/epml/sim_survey/PMC_UA.epml";
	const MODEL_FILE_2 = "/home/toth01/refmodmine/input/epml/sim_survey/PMC_UA.epml";
	const MODEL_ANALYSIS_FILE = "/home/toth01/refmodmine/input/epml/pmc/Y-CIM_en_original.epml";
	const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/all_examples.epml";
	const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/empiric_values.csv";
	const VARIANCE_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/Analyse_SM.csv";
}
?>
