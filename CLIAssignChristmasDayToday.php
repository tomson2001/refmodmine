<?php
chdir("/var/www");

require 'autoloader.php';

// DATABASE CONNECTION
$mdb2_dsn = array (
		'phptype' => Config::DB_TYPE,
		'username' => Config::DB_USER,
		'password' => Config::DB_PASS,
		'hostspec' => Config::DB_HOST,
		'database' => Config::DB_DATABASE 
);

$mdb2_options = array (
		'debug' => 2 
);

$db = &MDB2::connect ( $mdb2_dsn, $mdb2_options );

// ERROR HANDLING
if (PEAR::isError ( $db )) {
	Logger::log ( "System", "CLIAssignChristmasDayToday.php: Error connecting to database: " . $db->getMessage (), "ACCESS" );
	Logger::log ( "System", "CLIAssignChristmasDayToday.php: Error connecting to database: " . $db->getMessage (), "ERROR" );
	die ( $db->getMessage () );
}
// DATABASE CONNECTED

$result = ChristmasDays::assignToday();

// Ausgabe der Dateiinformationen auf der Kommandozeile
if ( $result ) {
	print("\nChristmas day successfully assigned\n");
} else {
	print("\nChristmas day not assigned\n");
}
?>