<?php
/**
 * Logger
 * 
 * Possible Log Levels:
 * 	ERROR
 *  ACCESS
 *  
 *  See definitions in Config.php
 * 
 * @author thaler
 *
 */ 
class Logger {

	public static function log($user, $msg, $loglevel) {
		$logFile = "";
		$doLog = false;
		switch ( $loglevel ) {
			case "ERROR":  $logFile = Config::ERROR_LOG;  $doLog = Config::ENABLE_ERROR_LOGGING;  continue;
			case "ACCESS": $logFile = Config::ACCESS_LOG; $doLog = Config::ENABLE_ACCESS_LOGGING; continue;
			default: exit("ERROR: Log level ".$loglevel." unknown.");
		}
		
		if ( !$doLog ) return null;

		$data = date("Y-m-d H:i:s").";".$loglevel.";".$user.";".$msg."\n";
		
		$handle = fopen($logFile, "a");
		fwrite($handle, $data);
		fclose($handle);
	}
	
}
?>