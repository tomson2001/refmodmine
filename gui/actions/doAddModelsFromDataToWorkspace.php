<?php
// load selected epml
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null;
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "add";

$workspace = new WorkspaceEPML();
$workspaceData = $workspace->getAvailableData();
if ( $workspaceData->containsFile($file) ) {
	
	// getting file infos
	$workspaceActionConfig = new WorkspaceActionConfig();
	$fileType = $workspaceData->getFileType($file);
	$fileParams = $workspaceData->getFileParams($file);
	$type = $workspaceActionConfig->getFileTypeName($fileType);
	$description = $workspaceActionConfig->getFileTypeDescriptions($fileType, $fileParams);
	
	// moving file from current place to the user repo
	if ( !file_exists(Config::FILES_PATH."/".session_id()) ) mkdir(Config::FILES_PATH."/".session_id());
	$filenameWithPath = Config::WORKSPACE_PATH."/".session_id()."/workspace.epml.".$file;
	$newFileNameWithPath = Config::FILES_PATH."/".session_id()."/".$file.".epml";
	rename($filenameWithPath, $newFileNameWithPath);
	
	// Load EPML
	$fileSource = "userRepo";
	$epml = new EPML($newFileNameWithPath, $fileSource);
	
	if ( count($epml->epcs) > 0 && $mode == "replace" ) {
		$workspace->clear();
		$workspace = new WorkspaceEPML();
	}

	// adding suffix to the epc names and add to the workspace
	foreach ( $epml->epcs as $index => $epc ) {
		$epml->epcs[$index]->name = $epml->epcs[$index]->name." (".$description.")"; 
		$workspace->addEPC($epc, $epml->filename);
	}
	
	$workspace->updateWorkspaceEPMLFile();
}
?>