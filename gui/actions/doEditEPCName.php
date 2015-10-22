<?php
// load selected epml
$epcID = isset($_REQUEST['epcID']) ? $_REQUEST['epcID'] : null;
$epcName = isset($_REQUEST['epcName']) ? $_REQUEST['epcName'] : null;

$workspace = new WorkspaceEPML();

if ( !is_null($epcID) && isset($workspace->epcs[$epcID]) && !is_null($epcName) && !empty($epcName) ) {
	$workspace->epcs[$epcID]->name = $epcName;
	$workspace->updateWorkspaceEPMLFile();
}
?>