<?php
// load selected epml
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null;
$fileSource = isset($_REQUEST['source']) ? $_REQUEST['source'] : null;
$epml = new EPML($file, $fileSource);

$workspace = new WorkspaceEPML();
foreach ( $epml->epcs as $epc ) {
	$workspace->addEPC($epc, $epml->filename);
}
$workspace->updateWorkspaceEPMLFile();
?>