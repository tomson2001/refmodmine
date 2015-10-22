<?php 
$config = new WorkspaceActionConfig();
foreach ( $config->fileTypeInfos as $fileTypeInfos ) {
	if ( $fileTypeInfos["isUploadable"] ) {
?>

	<div class="jumbotron">
    	<h3><?php echo $fileTypeInfos["Name"]; ?> (<?php echo $fileTypeInfos["FileExtension"]; ?>)</h3>
        <?php echo $fileTypeInfos["Specification"]; ?>
	</div>
	
<?php
	}
}
?>