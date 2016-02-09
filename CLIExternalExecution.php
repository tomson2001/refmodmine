<?php
$start = time();
require 'autoloader.php';

print("\n--------------------------------------------------------------------\n RefMod-Miner (PHP) - External Script Execution \n--------------------------------------------------------------------\n");


// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) || !isset($argv[4]) || !isset($argv[5]) ) {
	exit("   Please provide the following parameters:\n
   command=         command to execute with ||| instead of whitespace
   description=     description with ||| instead of whitespace
   sessionid=		
      no
      [browser session id]

   notification=
      no
      [E-Mail adress]
			
   checksum=
      no (no DB logging)
      checksum (for DB Logging in ActionLog)

   please use the correct order!
			
ERROR: Parameters incomplete
");
}

// Checking Parameters
$command = str_replace("[]", " ", substr($argv[1], 8,  strlen($argv[1])));
$command = str_replace("@QUOTE@", "\"", $command);
$description = str_replace("[]", " ", substr($argv[2], 12,  strlen($argv[2])));
$sessionid = substr($argv[3], 10, strlen($argv[3]));
$email   = substr($argv[4], 13, strlen($argv[4]));
$checksum   = substr($argv[5], 9, strlen($argv[5]));

print("
command: ".$command."
description: ".$description."
sessionid: ".$sessionid."
notification: ".$email."
checksum: ".$checksum."

checking notification parameter ...
");

// Check notification
$doNotify = true;
if ( $email == "no" ) {
	$doNotify = false;
	print "  notification ... ok (notification disabled)\n";
} else {
	print "  notification ... ok (mail to ".$email.")\n";
}

print(" done");

print("\n\nchecking notification parameter ...
");

// Check Logging
$doLog = true;
if ( $checksum == "no" ) {
	$doLog = false;
	print "  logging ... disabled\n";
} else {
	print "  logging ... ok (".$checksum.")\n";
}

print(" done");

$result = exec($command);
if ( substr_count(strtolower($result), "error") > 0 || substr_count(strtolower($result), "not found") > 0 ) {
	Logger::log($email, "External call failed: ".$result.", Call was: ".$command, "ERROR");
	Logger::log($email, "External call failed: ".$result.", Call was: ".$command, "ACCESS");
} else {
	Logger::log($email, "External call finished: ".$result.", Call was: ".$command, "ACCESS");
}

// Berechnungdauer
$duration = time() - $start;
$seconds = $duration % 60;
$minutes = floor($duration / 60);

$readme  = "Your action \"".$description."\" finished with the result:\r\n";
$readme .= $result."\r\n\r\n";
$readme .= "Duration: ".$minutes." Min. ".$seconds." Sec.\r\n\r\n";

if ( $sessionid !== "no") $readme .= "Your workspace: ".Config::WEB_PATH."index.php?sid=".$sessionid."&site=workspace";

if ( $doNotify ) {
	print("\n\nSending notification ... ");
	$notificationResult = EMailNotifyer::sendCLIExternalExecutionNotification($email, $readme, $description);
	if ( $notificationResult ) {
		print("ok");
	} else {
		print("error");
	}
}

if ( $doLog ) {
	// DATABASE CONNECTION
	$mdb2_dsn = array(
			'phptype'  => Config::DB_TYPE,
			'username' => Config::DB_USER,
			'password' => Config::DB_PASS,
			'hostspec' => Config::DB_HOST,
			'database' => Config::DB_DATABASE
	);
	
	$mdb2_options = array(
			'debug' => 2
	);
	
	$db = &MDB2::connect($mdb2_dsn, $mdb2_options);
	
	// ERROR HANDLING
	if (PEAR::isError($db)) {
		Logger::log("System", "CLIExternalExecution.php: Error connecting to database: ".$db->getMessage(), "ACCESS");
		Logger::log("System", "CLIExternalExecution.php: Error connecting to database: ".$db->getMessage(), "ERROR");
		die($db->getMessage());
	}
	// DATABASE CONNECTED
	
	$log = new ActionLog($checksum);
	$log->setEndPot();
}

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n");
?>