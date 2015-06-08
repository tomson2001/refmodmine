<?php 
$file = $_REQUEST["file"];
$content = "";
$workspace = new WorkspaceEPML();
$workspaceData = $workspace->getAvailableData();
if ( $workspaceData->containsFile($file) ) {
	
	$workspaceActionConfig = new WorkspaceActionConfig();
	$fileType = $workspaceData->getFileType($file);
	$fileParams = $workspaceData->getFileParams($file);
	$type = $workspaceActionConfig->getFileTypeName($fileType);
	$description = $workspaceActionConfig->getFileTypeDescriptions($fileType, $fileParams);
	
?>
	

	<p><?php echo "<big><b>".$type."</b> (".$description.")</big>";?>&nbsp; &nbsp;&nbsp;&nbsp;<a class="btn btn-lg btn-primary" href="index.php?site=workspace"
			role="button">&laquo; back to workspace</a>
		<a class="btn btn-lg btn-success" href="<?php echo $workspaceData->getDownloadLink($file); ?>" download="<?php echo $workspaceData->getDownloadFilename($file); ?>"
			role="button">download SVG</a></p>
	
	<object data="<?php echo $workspaceData->getDownloadLink($file); ?>" type="image/svg+xml"></object>
	
<?php 
} else {
?>

<div class="jumbotron">
	<div class="alert alert-danger alert-dismissible" role="alert">
		  <strong>Error. </strong> File not found.
		</div>
</div>

<?php 
}
?>