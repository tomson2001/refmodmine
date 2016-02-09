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
	const PERSISTENT_PATH = "files/persistedData";
	const ADMIN_E_MAIL = "tom.thaler@dfki.de";
	const STANDFORD_POS_TAGGER_PATH = "/var/www/lib/stanford-postagger-full-2015-01-30/";
	const WORDNET_EXE = "wordnet";
	//const WORDNET_EXE = "\"c:\\Program Files (x86)\\WordNet\\2.1\\bin\\wn.exe\"";
	const REFMOD_MINER_JAVA_PATH_WITH_FILENAME = "/var/www/lib/refmod-miner/master.jar";
	const TEXT2MODEL_JAVA_PATH_WITH_FILENAME = "/var/www/lib/text2model/text2model.jar";
	const YANDEX_API_KEY = "trnsl.1.1.20150615T152332Z.8cd0307f7f72dc78.2c9576dbbbf37b07fed503a528b33b9e897059aa";
	const OPEN_CALAIS_API_KEY = "MNffdJ8winJ0ows1sZ9Do6s2jAgsmK6U";
	
	// DB Settings
	const DB_TYPE = 'mysql';
	const DB_HOST = 'localhost';
	const DB_USER = 'root';
	const DB_PASS = 'rmmaasdbroot';
	const DB_DATABASE = 'rmmaas';
	const DB_DATE_FORMAT = 'Y-m-d';
	const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';
	const MAX_INSERTS_PER_QUERY = 500;
	
	// Some Settings
	const NUM_CORES_TO_WORK_ON = 1;
	const WORDNET_SYNONYM_LIMIT = 4; // Anzahl der zu beruecksichtigen Bedeutungen. Ersten Tests zufolge mindestens 7, MUSS < 10 sein!!!!
	const MAX_TIME_PER_TRACE_EXTRAKTION = 360; // in Sekunden, 0=keine Beschraenkung
	const FIX_POINT_ARITHMETIC = false; // Einstellen einer Fixpunktarithmetik: false => Ausgeschaltet, [0-x] => Fixpunkt
	const STANFORD_POS_TAGGER_MODEL = 'english-left3words-distsim.tagger'; // english-left3words-distsim.tagger || german-hgc.tagger | german-fast.tagger | german-dewac.tagger | english-bidirectional-distsim.tagger
		
	// Logging
	const ENABLE_ERROR_LOGGING = true;
	const ERROR_LOG = "log/error.log";
	const ENABLE_ACCESS_LOGGING = true;
	const ACCESS_LOG = "log/access.log";
	const ENABLE_DB_QUERY_LOGGING = false;  // database queries and execs are being logged to ACCESS_LOG
	
	// Tracking and Logging
	const TRACK_SITE_VISITS = true;
	const TRACK_ACTIONS = true;
	const LOG_ACTIONS = true;
	
	// Notification
	const DO_NOTIFY_ON_MODEL_UPLOADS = "tom.thaler@dfki.de"; // disable: null, otherwise the e-mail adress
	const DO_NOTIFY_ON_MODEL_ADDED_TO_GLOBAL_RESPOSITORY = "tom.thaler@dfki.de"; // disable: null, otherwise the e-mail adress
	
	// ------ END GENERAL SETTINGS -----------------------------------------------------------------------
	
	// Some individual Stuff
	
	const MODEL_FILE_1 = "input/Kurbel_english.epml";
	const MODEL_FILE_2 = "input/pmc.epml";
	//const MODEL_FILE_2 = "input/SAP_ALL_EPCs_named.epml";
	
	const MODEL_ANALYSIS_FILE = "/home/toth01/refmodmine/input/epml/pmc/Y-CIM_en_original.epml";
	const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/all_examples.epml";
	const CORR_INPUT_FILE = "input/sim_vals.csv";
	const VARIANCE_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/Analyse_SM.csv";
}
?>
