<?php
$startpos = strrpos($_SESSION['uploadedFilePath'], "/") + 1;
$filename = substr($_SESSION['uploadedFilePath'], $startpos, -5);
$_REQUEST['file'] = $filename;
$_REQUEST['source'] = "userRepo";

$actionLog = new ActionLog();
$checksum = md5("UPLOAD_MODELS ".$filename."-".time());
$actionLog->trackAction("UPLOAD_MODELS", $filename, session_id(), $checksum);
$actionLog->setEndPot();

EMailNotifyer::sendAdminNotificationModelsUploaded("files/".session_id()."/".$filename);
?>