<?php
$startpos = strrpos($_SESSION['uploadedFilePath'], "/") + 1;
$file = substr($_SESSION['uploadedFilePath'], $startpos, -5);
$fileSource = "userRepo";
$epml = new EPML($file, $fileSource);

$workspace = new WorkspaceEPML();
foreach ( $epml->epcs as $epc ) {
	if ( $workspace->addEPC($epc, $epml->filename) ) $_SESSION['numWorkspaceModels']++;
}
$workspace->updateWorkspaceEPMLFile();

$actionLog = new ActionLog();
$checksum = md5("UPLOAD_MODELS ".$file."-".time());
$actionLog->trackAction("UPLOAD_MODELS", $file, session_id(), $checksum);
$actionLog->setEndPot();

EMailNotifyer::sendAdminNotificationModelsUploaded("workspace/".session_id()."/".$file);
?>