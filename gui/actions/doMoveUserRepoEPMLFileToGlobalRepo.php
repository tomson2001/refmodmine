<?php
$sessionID = session_id();
$path = Config::FILES_PATH."/".$sessionID;
rename($path."/".$_REQUEST['userRepoMoveFile'], Config::REPOSITORY_PATH.'/'.$_REQUEST['userRepoMoveFile']);

EMailNotifyer::sendAdminNotificationModelsAddedToGlobalRepository(Config::REPOSITORY_PATH.'/'.$_REQUEST['userRepoMoveFile']);
?>