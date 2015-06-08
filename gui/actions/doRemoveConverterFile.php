<?php
$sessionID = session_id();
$path = Config::FILES_PATH."/".$sessionID;
@unlink($path."/".$_REQUEST['file'].".xml");
@unlink($path."/".$_REQUEST['file'].".epml");
?>