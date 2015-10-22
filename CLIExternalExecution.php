<?php
$start = time();
require 'autoloader.php';

print("\n--------------------------------------------------------------------\n RefMod-Miner (PHP) - External Script Execution \n--------------------------------------------------------------------\n");


// Hilfeanzeige auf Kommandozeile
if ( !isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) || !isset($argv[4]) ) {
	exit("   Please provide the following parameters:\n
   command=         command to execute with ||| instead of whitespace
   description=     description with ||| instead of whitespace
   sessionid=		
      no
      [browser session id]

   notification=
      no
      [E-Mail adress]

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

print("
command: ".$command."
description: ".$description."
sessionid: ".$sessionid."
notification: ".$email."

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

// Ausgabe der Dateiinformationen auf der Kommandozeile
print("\n\nDuration: ".$minutes." Min. ".$seconds." Sec.\n");
?>