<?php
// load selected epml
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null;
$fileSource = isset($_REQUEST['source']) ? $_REQUEST['source'] : null;
$epml = new EPML($file, $fileSource);

$epcID = isset($_REQUEST['epcID']) ? $_REQUEST['epcID'] : null;

$workspace = new WorkspaceEPML();
$workspace->removeEPC($epcID);
$workspace->updateWorkspaceEPMLFile();
?>