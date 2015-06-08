<?php
// load selected epml
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null;
$fileSource = isset($_REQUEST['source']) ? $_REQUEST['source'] : null;
$epml = new EPML($file, $fileSource);

$modelPath = isset($_REQUEST['modelPath']) ? $_REQUEST['modelPath'] : null;
$epc = $epml->getEPC($modelPath);

$workspace = new WorkspaceEPML();
$result = $workspace->addEPC($epc, $epml->filename);
//if ( $result ) { echo "OK"; } else { echo "NICHT OK"; }
$workspace->updateWorkspaceEPMLFile();
?>