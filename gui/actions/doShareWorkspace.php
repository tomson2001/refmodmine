<?php

$email = isset($_POST['email']) ? $_POST['email'] : "";
$text = isset($_POST['text']) ? $_POST['text'] : "";

if ( $result = EMailNotifyer::sendWorkspaceShareLink($email, $text) ) {
	$_POST["msg"] = "<strong>Workspace successfully shared. </strong> The workspace link was sent to ".$email.".";
	$_POST["msgType"] = "success";
} else {
	$_POST["msg"] = "<strong>Workspace sharing failed. </strong> Please try againg or contact the admin.";
	$_POST["msgType"] = "danger";
}

$actionLog = new ActionLog();
$checksum = md5("SHARE WORKSPACE WITH ".$email.": ".$text."-".time());
$actionLog->trackAction("SHARE_WORKSPACE", "SHARE WITH ".$email.": ".$text, session_id(), $checksum);
$actionLog->setEndPot();

?>