<?php
$type = isset($_POST["type"]) ? $_POST["type"] : null;
$description = isset($_POST["description"]) ? $_POST["description"] : null;
$fileTmp = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : null;
$email = empty($_SESSION["email"]) ? "no" : $_SESSION["email"];

if ( is_null($type) || is_null($description) || is_null($fileTmp) ) {
	$_POST["msg"] = "<strong>Error. </strong> An error occured at data file upload.";
	$_POST["msgType"] = "danger";
	Logger::log($email, "Data file upload to workspace failed because of missing arguments.", "ERROR");
} else {
	$workspaceActionConfig = new WorkspaceActionConfig();
	if ( isset($workspaceActionConfig->fileTypeInfos[$type]) ) {
		$sessionID = session_id();
		$workspacePath = Config::WORKSPACE_PATH;
		
		// Checking Extension matching
		$lastDotPos = strrpos($_FILES['file']['name'], ".");
		$uploadedFileExtension = strtolower(substr($_FILES['file']['name'], $lastDotPos+1));
		$fileWithoutExtension = substr($_FILES['file']['name'], 0, $lastDotPos);
		
		if ( $uploadedFileExtension == $workspaceActionConfig->fileTypeInfos[$type]["FileExtension"] ) {
			if ( empty($description) ) $description = $fileWithoutExtension;
			$description = str_replace(" ", "_", $description);
			$description = str_replace(".", "_", $description);
			$suffix = $type.".custom.".$description;
			$filename = $workspacePath."/".$sessionID."/workspace.epml.".$suffix;
			move_uploaded_file($fileTmp, $filename);
			$_POST["msg"] = "<strong>Done. </strong> File successfully uploaded.";
			$_POST["msgType"] = "success";
			Logger::log($email, "Data file uploaded to workspace: ".$filename, "ACCESS");
			
			if ( !is_null($workspaceActionConfig->fileTypeInfos[$type]["uploadAction"]) ) {
				$_POST["uploadedFilename"] = $filename;
				$_POST["uploadedFiletype"] = $type;
				$_POST["uploadedFileSuffix"] = $suffix;
				callAction($workspaceActionConfig->fileTypeInfos[$type]["uploadAction"]);
			}
			
		} else {
			$_POST["msg"] = "<strong>Error. </strong> Your type selection does not match the uploaded file extension  (selected: ".$type." [".$workspaceActionConfig->fileTypeInfos[$type]["FileExtension"]."], file: ".$_FILES['file']['name'].").";
			$_POST["msgType"] = "danger";
			Logger::log($email, "Data file upload to workspace failed because of filetype mismatch (selected: ".$type.", file: ".$_FILES['file']['name'].").", "ERROR");
		}
	} else {
		$_POST["msg"] = "<strong>Error. </strong> Filetype unknown.";
		$_POST["msgType"] = "danger";
		Logger::log($email, "Data file upload to workspace failed because of unknown filetype (".$type.").", "ERROR");
	}
}


?>