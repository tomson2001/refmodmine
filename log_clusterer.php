<?php
$start = time();
require 'autoloader.php';
print("\n-------------------------------------------------\n RefMod-Miner (PHP) - Log Clusterer \n-------------------------------------------------\n\n");

if ( in_array("--help", $argv) || in_array("-help", $argv) || in_array("-?", $argv) ) {
	exit("   Options:\n
   [--aggregate] aggregate log
   [--export]    Export resuling log file(s)
   [--help]      Hilfe\n\n");
}

$mxml_content = file_get_contents(Config::MXML_FILE);
$xml = new SimpleXMLElement($mxml_content);

$containedProcessInstances = count($xml->xpath("//ProcessInstance"));

print("Logfile:\n");
print("  ".Config::MXML_FILE." \n\n");
print("Num Traces: ".$containedProcessInstances."\n\n");

print("loading logfile ... ");
$startLoadingTime = time();
$loader = new MXMLLoader(Config::MXML_FILE, $xml, in_array("--aggregate", $argv));
$processLog = $loader->load();

$loadingDuration = time() - $startLoadingTime;
$seconds = $loadingDuration % 60;
$minutes = floor($loadingDuration / 60);

print("done ".count($processLog->traces)." (".$minutes." Min. ".$seconds." Sek.)\n");

if ( in_array("--export", $argv) ) {
	print("save log as mxml ... ");
	$startSavingTime = time();
	$processLog->exportMXML();

	$savingDuration = time() - $startSavingTime;
	$seconds = $savingDuration % 60;
	$minutes = floor($savingDuration / 60);

	print("done (".$minutes." Min. ".$seconds." Sek.)\n");
}
?>