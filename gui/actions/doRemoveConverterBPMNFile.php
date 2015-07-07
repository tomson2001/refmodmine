<?php
$sessionID = session_id();
$path = Config::FILES_PATH."/".$sessionID;
@unlink($path."/".$_REQUEST['file'].".bpmn");
@unlink($path."/".$_REQUEST['file'].".epml");
?>