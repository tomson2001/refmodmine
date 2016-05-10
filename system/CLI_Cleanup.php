<?php
chdir("/var/www");
require 'autoloader.php';

print("\n----------- RMMaaS Cleanup Tool ------------\n\n");

$report = "RMMaaS Cleanup Report\n\n";
$report .= "System: ".Config::WEB_PATH."\n\n";

function delete($path) {
	if (is_dir($path) === true) {
		$files = array_diff(scandir($path), array('.', '..'));
		foreach ($files as $file) delete(realpath($path) . '/' . $file);
		return rmdir($path);
	} elseif (is_file($path) === true) {
		return unlink($path);
	}
	return false;
}


/**
 * CLEANUP WORKSPACES
 * 
 * All workspaces without an activity in the last three month are being deleted
 */

$timeThreshold = time() - (Config::WORKSPACE_REMOVE_DAYS*24*60*60);

print("Remove user files without an activity longer than ".Config::WORKSPACE_REMOVE_DAYS." days ... ");
$report .= "Remove user files without an activity longer than ".Config::WORKSPACE_REMOVE_DAYS." days ... ";
// delete user workspaces which pass the inactivity threshold (see Config.php)

$removedUserFileFoldersOk = array();
$removedUserFileFoldersError = array();
$pathToFiles = Config::ABS_PATH."/".Config::FILES_PATH;
$files = scandir($pathToFiles);
foreach ( $files as $filename ) {
	$path = $pathToFiles.DIRECTORY_SEPARATOR.$filename;
	if ( is_dir($path) && $filename != "." && $filename != ".." && $filename != "persistedData" ) {
		if ( filemtime($path) < $timeThreshold ) {
			print("\n  Remove ".$filename." (last modified: ".date("F d Y H:i:s.", filemtime($path)).") ... ");
			$report .= "\n  Remove ".$filename." (last modified: ".date("F d Y H:i:s.", filemtime($path)).") ... ";
			if ( delete($path) ) {
				array_push($removedUserFileFoldersOk, $filename);
				$report .= "ok";
				print("ok");
			} else {
				array_push($removedUserFileFoldersError, $filename);
				$report .= "failed";
				print("failed");
			}
		}
	}
}
print("\ndone");
$report .= "\ndone";

print("\n\nRemove user workspaces without an activity longer than ".Config::WORKSPACE_REMOVE_DAYS." days ... ");
$report .= "\nRemove user workspaces without an activity longer than ".Config::WORKSPACE_REMOVE_DAYS." days ... ";
$removedUserWorkspaceFoldersOk = array();
$removedUserWorkspaceFoldersError = array();
$pathToWorkspaces = Config::ABS_PATH."/".Config::WORKSPACE_PATH;
$files = scandir($pathToWorkspaces);
foreach ( $files as $filename ) {
	$path = $pathToWorkspaces.DIRECTORY_SEPARATOR.$filename;
	if ( is_dir($path) && $filename != "." && $filename != ".." ) {
		if ( filemtime($path) < $timeThreshold ) {
			print("\n  Remove ".$filename." (last modified: ".date("F d Y H:i:s.", filemtime($path)).") ... ");
			$report .= "\n  Remove ".$filename." (last modified: ".date("F d Y H:i:s.", filemtime($path)).") ... ";
			if ( delete($path) ) {
				array_push($removedUserWorkspaceFoldersOk, $filename);
				$report .= "ok";
				print("ok");
			} else {
				array_push($removedUserWorkspaceFoldersError, $filename);
				$report .= "failed";
				print("failed");
			}
		}
	}
}
print("\ndone");
$report .= "\ndone";

/**
 * ARCHIVE LOG FILES
 */

print("\n\nArchive log files ... ");
$report .= "\n\nArchive log files ... ";

function getLastArchiveLogFileNumber($prefix) {
	$files = scandir("log");
	$maxNo = 0;
	foreach ( $files as $filename ) {
		if ( !is_dir($filename) && $filename != "." && $filename != ".." && Tools::startsWith($filename, $prefix) ) {
			$maxNo = (int) substr($filename, strlen($prefix)+1, strlen($filename)-strrpos($filename, ".log"));
		}
	}
	return $maxNo;
	
}

// ACCESS LOG FILE
$accessFileArchived = false;
$accessFileSize = filesize(Config::ACCESS_LOG);
print("\n\nAccess log size: ".$accessFileSize." bytes");
$report .= "\n\nAccess log size: ".$accessFileSize." bytes";
if ( $accessFileSize > 1024000 ) {
	$archiveNo = getLastArchiveLogFileNumber("access")+1;
	rename(Config::ACCESS_LOG, "log/access_".$archiveNo.".log");
	$fp = fopen(Config::ACCESS_LOG,"wb");
	fwrite($fp,"");
	fclose($fp);
	chmod(Config::ACCESS_LOG, 755);
	print("\nAccess file archived (".($archiveNo).") and created a new one");
	$report .= "\nAccess file archived (".($archiveNo).") and created a new one";
	$accessFileArchived = true;
}

// ERROR LOG FILE
$errorFileArchived = false;
$errorFileSize = filesize(Config::ERROR_LOG);
print("\n\nError log size: ".$errorFileSize." bytes");
$report .= "\n\nError log size: ".$errorFileSize." bytes";
if ( $errorFileSize > 1024000 ) {
	$archiveNo = getLastArchiveLogFileNumber("error")+1;
	rename(Config::ERROR_LOG, "log/error_".$archiveNo.".log");
	$fp = fopen(Config::ERROR_LOG,"w");
	fwrite($fp,"");
	fclose($fp);
	chmod(Config::ERROR_LOG, 755);
	print("\nError file archived (".($archiveNo).") and created a new one");
	$report .= "\nError file archived (".($archiveNo).") and created a new one";
	$errorFileArchived = true;
}

print("\n");
$report .= "\ndone";

EMailNotifyer::sendAdminCleanupReport($report);

?>