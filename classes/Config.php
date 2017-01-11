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
	const STANDFORD_POS_TAGGER_PATH = "";
	const WORDNET_EXE = "wordnet";
	const REFMOD_MINER_JAVA_PATH_WITH_FILENAME = "";
	const REFMOD_MINER_JAVA_PATH_WITH_FILENAME_LAYOUT = "";
	const TEXT2MODEL_JAVA_PATH_WITH_FILENAME = "";
	const YANDEX_API_KEY = "";
	const OPEN_CALAIS_API_KEY = "";
	
	const WORKSPACE_REMOVE_DAYS = 90; // days with no acitivity on a workspace, before it be deleted (cleanup)
	
	// DB Settings
	const DB_TYPE = 'mysql';
	const DB_HOST = 'localhost';
	const DB_USER = '';
	const DB_PASS = '';
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
}
?>
