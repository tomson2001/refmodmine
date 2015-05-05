<?php
if ( isset($_REQUEST['file']) ) {
	$workspace = new WorkspaceEPML();
	$workspaceData = $workspace->getAvailableData();
	$workspaceData->deleteFile($_REQUEST['file']);
}
?>