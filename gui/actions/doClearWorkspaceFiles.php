<?php
$workspace = new WorkspaceEPML();
$workspaceData = $workspace->getAvailableData();
$workspaceData->deleteAllFiles();
?>