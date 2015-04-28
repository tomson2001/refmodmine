<?php 
// load repository
$repo = new Repository();
$userRepo = new Repository(true);

// load selected epml 
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null;
$fileSource = isset($_REQUEST['source']) ? $_REQUEST['source'] : null;
$epml = new EPML($file, $fileSource);

// load selected model
$model = isset($_REQUEST['model']) ? $_REQUEST['model'] : null;

// load download path
$downloadPath = Config::REPOSITORY_PATH;
if ( $fileSource == "userRepo" ) {
	$sessionID = session_id();
	$downloadPath = Config::FILES_PATH."/".$sessionID;
}

?>

<div class="row">
    <div class="col-md-3">
        <h2>Files</h2>
	        <div class="list-group">
	        <?php 
	        foreach ( $repo->epmls as $filename => $numModels ) { ?>
	        	<a href="index.php?site=repository&file=<?php echo $filename; ?>&source=repo" class="list-group-item<?php if ( !is_null($file) && $file == $filename ) echo " active"; ?>"><span class="badge"><?php echo $numModels; ?></span><?php echo $filename; ?></a>
	        <?php } ?>
			</div>
			<?php 
	        if ( !$userRepo->isEmpty() ) {
				$userRepoClearLink = "index.php?site=repository&action=doClearUserRepoEPMLFiles";
				if ( $fileSource != "userRepo" ) {
					$userRepoClearLink .= "&file=".$filename."&source=repo";
				}
				
				$userRepoMoveLink = "index.php?site=repository&action=doMoveUserRepoEPMLFilesToGlobalRepo&file=".$filename."&source=repo";
	        ?>
			
			<div class="list-group">
				<li class="list-group-item list-group-item-success">My temporary files 
					<a href="<?php echo $userRepoMoveLink; ?>" title="add my files permanently to global repository" alt="add my files permanently to global repository"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span></a> 
					<a href="<?php echo $userRepoClearLink; ?>" title="clear my files" alt="clear my files"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
				</li>
			<?php 
			  foreach ( $userRepo->epmls as $filename => $numModels ) { ?>
				<a href="index.php?site=repository&file=<?php echo $filename; ?>&source=userRepo" class="list-group-item<?php if ( !is_null($file) && $file == $filename ) echo " active"; ?>"><span class="badge"><?php echo $numModels; ?></span><?php echo $filename; ?></a>
			 <?php } ?>	        
	        </div>
	        <?php } ?> 
			
			<div class="panel panel-default">
			  <div class="panel-heading"><b>Add files</b></div>
			  <div class="panel-body">
  
			<?php
			$reloadURL = "index.php?site=repository";
			if ( !is_null($file) ) $reloadURL .= "&file=".$file;
			if ( !is_null($fileSource) ) $reloadURL .= "&source=".$fileSource;
			include 'gui/uploader.php'; 
			?>
				<ol class="breadcrumb">
				  <li class="active"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span> Drag &amp; drop files here</li>
				</ol>
			  </div>
			</div>
    </div>
    <div class="col-md-6">
        <h2>Content</h2>
        <?php 
        if ( is_null($file) ) {
		?>
        <p>Please select a file.</p>
        <?php 
		} else {
		$size = ( $epml->numModels + $epml->numDirectories < 30 ) ? $epml->numModels+$epml->numDirectories : 28;
		?>
		<form action="index.php?site=repository&file=<?php echo $file; ?>&source=<?php echo $fileSource; ?>" method="post">
		<input type="hidden" name="action" value="doAddSelectedModelsToWorkspace" />
		<select multiple class="form-control" name="modelPaths[]" size="<?php echo $size; ?>">
		<?php  
			$path = "";
        	foreach ( $epml->epcs as $epc ) { 
				$modelPath = str_replace("/".$epc->name, "", $epc->modelPath);
				$modelPath = str_replace("/root/", "", $modelPath);
				$modelPath = str_replace("/Root/", "", $modelPath);
				if ( $path != $modelPath ) {
					$path = $modelPath;
					echo '<optgroup label="'.$path.'">';
				}			
		?>
				<option <?php if ( WorkspaceEPML::inWorkspace($epml->filename, $epc->name) ) echo "selected "; ?>value="<?php echo $epc->modelPath; ?>"><?php echo $epc->name; ?></option>
		<?php 
			} ?>
		</select>
		<br />
		<button type="submit" class="btn btn-success btn-block">add to workspace</button>
		</form>
		<?php 
        } 
        ?>
        
    </div>
    <div class="col-md-3">
        <h2>Details</h2>
        <?php if ( !is_null($file) ) { ?>
        <div class="list-group">
		  <a href="#" class="list-group-item">
		    <p class="list-group-item-text">Filename</p>
		    <h4 class="list-group-item-heading"><?php echo $epml->epmlName; ?>.epml</h4>
		    <p class="list-group-item-text"><?php echo $epml->filepath; ?></p>
		  </a>
		  <a href="#" class="list-group-item">
		  	<p class="list-group-item-text">Number of models</p>
		    <h4 class="list-group-item-heading"><?php echo $epml->numModels?></h4>
		  </a>
		  <a href="#" class="list-group-item">
		  	<p class="list-group-item-text">Number of directories</p>
		    <h4 class="list-group-item-heading"><?php echo $epml->numDirectories?></h4>
		  </a>
		  <?php if ( $fileSource == "userRepo" ) { 
		  	$moveLink = "index.php?site=repository&action=doMoveUserRepoEPMLFileToGlobalRepo&userRepoMoveFile=".$epml->epmlName.'.epml&file='.$file.'&source=repo';
		  ?>
		  <a href="<?php echo $moveLink; ?>" class="list-group-item list-group-item-info">
		    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> add to global repository</h4>
		  </a>
		  <?php } ?>
		  <a href="index.php?site=repository&file=<?php echo $file; ?>&action=doAddAllModelsToWorkspace&source=<?php echo $fileSource;?>" class="list-group-item">
		    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> add all to workspace</h4>
		  </a>
		  <a href="index.php?site=modelBrowser&file=<?php echo $file; ?>&source=<?php echo $fileSource;?>" class="list-group-item">
		    <h4 class="list-group-item-heading"><span class=" glyphicon glyphicon-zoom-in" aria-hidden="true"></span> browse models</h4>
		  </a>
		  <a href="<?php echo $downloadPath.'/'.$epml->epmlName.'.epml'; ?>" download="<?php echo $epml->epmlName; ?>.epml" class="list-group-item">
		    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> download file</h4>
		  </a>
		  <?php if ( $fileSource == "userRepo" ) { 
		  
		  	$delLink = "index.php?site=repository&action=doRemoveUserRepoEPMLFile&userRepoDelFile=".$epml->epmlName.'.epml';;
		  	if ( $fileSource != "userRepo" ) {
		  		$delLink .= "&file=".$file."&source=repo";
		  	}
		  	
		  ?>
		  <a href="<?php echo $delLink; ?>" class="list-group-item list-group-item-danger">
		    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> remove file</h4>
		  </a>
		  <?php } ?>
		</div>
		<?php } ?>
    </div>
</div>