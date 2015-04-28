<?php
// load selected epml
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null;
$fileSource = isset($_REQUEST['source']) ? $_REQUEST['source'] : null;
$epml = new EPML($file, $fileSource);

$modelPaths = isset($_REQUEST['modelPaths']) ? $_REQUEST['modelPaths'] : array();

$workspace = new WorkspaceEPML();
$workspace->removeAllEPCsOfSource($epml->filename);
foreach ( $modelPaths as $modelPath ) {
	$epc = $epml->getEPC($modelPath);
	$workspace->addEPC($epc, $epml->filename);
}
$workspace->updateWorkspaceEPMLFile();
?>