<?php
$sessionID = session_id();
$path = Config::FILES_PATH."/".$sessionID;
@unlink($path."/".$_REQUEST['file'].".zip");
@unlink($path."/".$_REQUEST['file'].".epml");
?>