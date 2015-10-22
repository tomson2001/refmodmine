<?php
// load selected epml
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null;
$description = isset($_REQUEST['description']) ? $_REQUEST['description'] : "-";
$description = str_replace(" ", "-", $description);

$workspace = new WorkspaceEPML();
$workspaceData = $workspace->getAvailableData();
if ( $workspaceData->containsFile($file) ) {
	
	// getting file infos
	$workspaceActionConfig = new WorkspaceActionConfig();
	$fileType = $workspaceData->getFileType($file);
	
	// moving file from current place to the user repo
	$filenameWithPath = Config::WORKSPACE_PATH."/".session_id()."/workspace.epml.".$file;
	$newFileNameWithPath = Config::WORKSPACE_PATH."/".session_id()."/workspace.epml.".$fileType.".custom.".$description;
	rename($filenameWithPath, $newFileNameWithPath);
	
}
?>