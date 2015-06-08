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
			role="button">download CSV</a></p>
	
	<table class="table table-striped">
	
	<?php 
	$f = fopen($workspaceData->getDownloadLink($file), "r");
	$row = 0;
	$col = 0;
	while ( ($line = fgetcsv($f, 0, ";")) !== false ) {
		$row++;
		echo "<tr>";
		foreach ( $line as $cell ) {
			$col++;
			if ( $row == 1 ) {
				echo "<th>" . htmlspecialchars($cell) . "</th>";
			} else {
				echo "<td>" . htmlspecialchars($cell) . "</td>";
			}			
		}
		$col = 0;
		echo "</tr>\n";
	}
	fclose($f);
	?>
	
	</table>

	
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