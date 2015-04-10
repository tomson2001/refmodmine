<?php
// load selected epml
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null;
$fileSource = isset($_REQUEST['source']) ? $_REQUEST['source'] : null;
$epml = new EPML($file, $fileSource);

$modelPath = isset($_REQUEST['modelPath']) ? $_REQUEST['modelPath'] : null;
$epc = $epml->getEPC($modelPath);

$workspace = new WorkspaceEPML();
if ( $workspace->addEPC($epc, $epml->filename) ) $_SESSION['numWorkspaceModels']++;
$workspace->updateWorkspaceEPMLFile();
?>