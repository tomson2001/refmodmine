<?php 
$modelID = isset($_REQUEST['modelID']) ? $_REQUEST['modelID'] : null;

// load workspace
$workspace = is_null($modelID) ? new WorkspaceEPML(false) : new WorkspaceEPML(true);

$epc = $workspace->getEPC($modelID);

$visualizer = is_null($modelID) ? null : new EPCVisualizer($epc);
$jsCode = is_null($modelID) ? null : $visualizer->generateVisJSCode();

$sourcesString = $workspace->numSources < 2 ? "source" : "sources";

// load download path
$sessionID = session_id();
$downloadPath = Config::WORKSPACE_PATH."/".$sessionID."/workspace.epml";

$reloadLink = "index.php?site=workspace";
?>

	<?php 
	if ( isset($_POST["msg"]) ) {

		?>
		<div class="alert alert-<?php echo $_POST["msgType"]; ?> alert-dismissible" role="alert">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <?php echo $_POST["msg"]; ?>
		</div>
		<?php 
	}
	?>
	
	<?php 
	if ( !$_SESSION['workspaceOpened'] ) {
		$_SESSION['workspaceOpened'] = true;
		?>

		<div class="alert alert-info alert-dismissible" role="alert">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <strong>Please enter you e-mail.</strong> Some calculations may take minutes or hours. If you enter your e-mail adress, you will get an e-mail, when your calculation(s) are finished.<br /><br />
		  <form class="form-inline" method="post">
		  	<input type="hidden" name="action" value="doSetWorkspaceEmail" />
			  <div class="form-group">
			    <label for="email">Your E-Mail</label>
			    <input type="email" class="form-control" name="email" id="email" placeholder="jane.doe@example.com">
			  </div>
			  <button type="submit" class="btn btn-default">Save</button>
			</form>
		</div>
		<?php 
	}
	?>
	
    <div class="col-md-3">

    	<h2>Selected Models</h2>
    		<ul class="list-group">
			  <li class="list-group-item list-group-item-warning">
			    <h4 class="list-group-item-heading">Workspace &nbsp;<a href="#modal_share_workspace" data-toggle="modal" title="share workspace"><span class="glyphicon glyphicon-share" aria-hidden="true"></span></a></h4>
			    
			    <!-- SHARE WORKSPACE MODAL -->
			    <div class="modal fade" id="modal_share_workspace" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
						<form method="post">
							<input type="hidden" name="action" value="doShareWorkspace" />
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title" id="myModalLabel">Share Workspace</h4>
							</div>
							<div class="modal-body">
							Share your workspace with others by sending a link.<br /><br />
							<input type="email" class="form-control" name="email" id="email" placeholder="your.buddy@example.com"><br />
							Your message
							<textarea name="text" id="text" class="form-control" rows="3"></textarea>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
								<button type="submit" class="btn btn-primary">Share</button>
							</div>
						</form>
						</div>
					</div>
				</div>
			    <!-- END SHARE WORKSPACE MODAL -->
			    
			    <p class="list-group-item-text">
			    	<b><?php echo $workspace->numModels; ?> models</b> from <b><?php echo $workspace->numSources." ".$sourcesString; ?></b><br /><br />
			    	<a href="<?php echo $downloadPath; ?>" download="workspace.epml" class="list-group-item-warning"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> download as EPML</a>
			    	&nbsp;<a href="<?php echo $reloadLink; ?>&action=doClearWorkspace" class="list-group-item-warning"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> clear</a>
			    </p>
			  </li>
			  
			  <!-- EMAIL NOTIFICATION -->
			  <li class="list-group-item list-group-item-warning">
			    <p class="list-group-item-text">
			    	<?php 
			    	if ( empty($_SESSION["email"]) ) { ?>

						<a class="list-group-item-warning" href="#modal_set_notification_email" role="button" title="Set notification e-mail" data-toggle="modal">Set notification E-Mail</a>
						<a class="list-group-item-warning" data-toggle="tooltip" title="Some calculations may take minutes or hours. If you enter your e-mail adress, you will get an e-mail, when your calculation(s) are finished." data-placement="bottom"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span></a>

					<?php
					} else {
			    	?>
			    	<b>Notification E-Mail</b><br />
			    	<?php 
			    	echo $_SESSION["email"]; 
			    	?>
			    	
			    	<a class="list-group-item-warning" href="#modal_set_notification_email" role="button" title="change notification e-mail" data-toggle="modal"> <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
			    	
			    	<?php }
			    	?>
			    	
			    	<div class="modal fade" id="modal_set_notification_email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
							<form method="post">
								<input type="hidden" name="action" value="doSetWorkspaceEmail" />
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="myModalLabel">Set notification e-mail</h4>
								</div>
								<div class="modal-body">
								Some calculations may take minutes or hours. If you enter your e-mail adress, you will get an e-mail, when your calculation(s) are finished.<br /><br />
								<input type="email" class="form-control" name="email" id="email" placeholder="<?php if ( empty($_SESSION["email"]) ) { echo "jane.doe@example.com"; } else { echo $_SESSION["email"]; } ?>" value="<?php if ( !empty($_SESSION["email"]) ) { echo $_SESSION["email"]; } ?>">
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<button type="submit" class="btn btn-primary">Save</button>
								</div>
							</form>
							</div>
						</div>
					</div>
					
			    </p>
			  </li>
			  <!-- END EMAIL NOTIFICATION -->
			  
			</ul>
	        <div class="list-group">
	        <?php 
	        foreach ( $workspace->modelList as $epcID => $epcName ) { ?>
	        	<a href="<?php echo $reloadLink."&modelID=".$epcID; ?>" class="list-group-item"><small><?php echo $epcName; ?></small></a>
	        <?php } ?>
			</div>
			<?php 
			$reloadURL = is_null($modelID) ? $reloadLink : "&".$reloadLink."modelID=".$modelID;
			$reloadURL .= "&action=doLoadNewFileToWorkspace";
			include 'gui/uploader.php'; 
			?>
			<ol class="breadcrumb">
				<li class="active"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span> Drag &amp; drop files here</li>
			</ol>
			    </div>
			    <div class="col-md-6">

					<?php 
			        if ( is_null($epc) ) {
						$workspaceData = $workspace->getAvailableData();
						$workspaceActionConfig = new WorkspaceActionConfig();
						
						$clearFiles = count($workspaceData->files) == 0 ? "" : " <small><a href=\"".$reloadLink."&action=doClearWorkspaceFiles\" title=\"clear\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></a></small>";
					?>
						<h2>Available Data
						<?php echo $clearFiles; ?>
						
						<!-- UPLOADER -->
						<small><a href="#modal_data_file_upload" role="button" title="upload file" data-toggle="modal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></small>
						</h2>
						
						<div class="modal fade" id="modal_data_file_upload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="myModalLabel">Upload data file</h4>
									</div>
									<form method="post" enctype="multipart/form-data" class="form-horizontal">
										<input type="hidden" name="action" value="doUploadDataFile" />
										
										<div class="modal-body">
											<div class="form-group">
											  <label for="type" class="col-sm-2 control-label">Type <a href="index.php?site=fileTypeSpecifications" target="_blank"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a></label>
											  <div class="col-sm-10">
											    <select name="type" id="type" class="form-control">
										  
									<?php 
									$workspaceActionConfig = new WorkspaceActionConfig();
									foreach ( $workspaceActionConfig->fileTypeInfos as $type => $infos ) {
										if ( $infos["isUploadable"] )
										echo "<option value=\"".$type."\">".$infos["Name"]." (".$infos["FileExtension"].")</option>";
									}
									?>
												</select>
											</div>
										</div>
									
										<div class="form-group">
										    <label for="description" class="col-sm-2 control-label">Description</label>
										    <div class="col-sm-10">
										      <input type="text" class="form-control" id="description" name="description" placeholder="enter description, otherwise filename is choosen">
										    </div>
										</div>
										
										<div class="form-group">
									      <label for="file" class="col-sm-2 control-label">File</label>
									      <div class="col-sm-10">
									      	<input type="file" id="file" name="file">
									      </div>
									    </div>
									
									  </div>
									  <div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
										<button type="submit" class="btn btn-primary">Upload</button>
									  </div>
								</form>
								</div>
							</div>
						</div>
						<!-- END UPLOADER -->
						
						<?php
						
						
						if ( count($workspaceData->files) == 0 ) { ?>
						
						<p>There are no data available at the moment. Please proceed some calculations.</p>
						
						<?php 
						} else { ?>
						
						<table class="table table-hover">
							<tr>
								<th></th> <!-- Icon -->
								<th>Type</th>
								<th>Description</th>
								<th>Options</th>
							</tr>
							<?php 
							foreach ( $workspaceData->files as $wFile => $wFilename ) {  
								$fileType = $workspaceData->getFileType($wFile);
								$fileParams = $workspaceData->getFileParams($wFile); 
								?>
							 <tr>
							 	<td><span class="<?php echo $workspaceActionConfig->getFileTypeIcon($fileType); ?>" aria-hidden="true"></span></td>
							 	<td><?php echo $workspaceActionConfig->getFileTypeName($fileType); ?></td>
							 	<td><?php echo $workspaceActionConfig->getFileTypeDescriptions($fileType, $fileParams); ?>
							 		<a href="#modal_editFileDescription_<?php echo md5($wFile); ?>" role="button" title="edit" alt="edit" data-toggle="modal"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
							 		<?php echo $workspaceData->getEditFileDescriptionModalCode($wFile, $reloadLink); ?>
							 	</td>
							 	<td>
							 		<?php if ( !is_null($workspaceActionConfig->getFileTypeOpenMethod($fileType)) ) { ?><a href="index.php?site=<?php echo $workspaceActionConfig->getFileTypeOpenMethod($fileType); ?>&file=<?php echo $wFile; ?>" title="show file" alt="show file"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a><?php } ?>
							 		<?php if ( in_array($fileType, array("models", "model")) ) { ?>
							 			<a href="#modal_addModelsFromDataToWorkspace_<?php echo md5($wFile); ?>" role="button" title="add to workspace" alt="add to workspace" data-toggle="modal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
							 			<?php echo $workspaceData->getAddModelsFromDataToWorkspaceModalCode($wFile, $reloadLink); ?>
							 		<?php } ?>
							 		<a href="<?php echo $workspaceData->getDownloadLink($wFile); ?>" download="<?php echo $workspaceData->getDownloadFilename($wFile); ?>" title="download <?php echo $workspaceActionConfig->getFileTypeExtension($fileType); ?>"><span class="glyphicon glyphicon-save" aria-hidden="true"></span></a>
							 		<a href="#modal_delete_<?php echo md5($wFile); ?>" role="button" title="delete" alt="delete" data-toggle="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
							 		<?php echo $workspaceData->getDeleteModalCode($wFile, $reloadLink); ?>
							 	</td>
							 </tr>
							<?php }
							?>
						</table>
				        <?php 
				        }
					} else {
						echo $jsCode; ?>
						
						<h2>Preview</h2>
						<div id='EPC'></div>
						
					<?php 
			        } 
			        ?>  
					      
			    </div>
			    <div class="col-md-3">
			        
			<?php if ( !is_null($epc) ) { ?>

				<h2>Model Details</h2>

			<?php 
        	// load data to show
        	$modelPath = $epc->modelPathOnly;
        	$modelPath = substr($modelPath, 6);
        	$pos = strrpos($modelPath, "/")+1;
        	$sourceFilename = substr($modelPath, $pos);
        	$file = str_replace(".epml", "", $sourceFilename);
        	
        	$epcHash = $epc->getHash();
        	$editEPCNameModalCode = $workspace->getEditEPCNameModalCode($modelID);
        	
        	$sourceRepo = substr($modelPath, 0, $pos-1);
        	$sourceRepo = ( $sourceRepo == Config::REPOSITORY_PATH ) ? "repo" : "userRepo";
        	
        	$numActivities = count($epc->functions);
        	$numEvents = count($epc->events);
        	
        	$numXOR = count($epc->xor);
        	$numOR = count($epc->or);
        	$numAND = count($epc->and);
        	$numConnectors = $numXOR + $numOR + $numAND;
        	
        	$numNodes = $numConnectors + $numActivities + $numEvents;
        	
        	$numEdges = count($epc->edges);
        	
        ?>
        		<!-- A model was selected -->
        			<div class="list-group">
					  <a href="#modal_editEPCName_<?php echo $epcHash; ?>" data-toggle="modal" class="list-group-item">
					    <p class="list-group-item-text">Model</p>
					    <h4 class="list-group-item-heading"><?php echo $epc->name; ?></h4>
					    <?php if ( file_exists($modelPath) ) { ?><p class="list-group-item-text"><?php echo $modelPath; ?></p><?php } ?>
					  </a>
					  <?php echo $editEPCNameModalCode; ?>
					  
					  <a href="index.php?site=workspace" class="list-group-item list-group-item-info">
					    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> show operations</h4>
					  </a>
					  
					  <a href="index.php?site=workspace&epcID=<?php echo $epc->id; ?>&action=doRemoveModelFromWorkspace" class="list-group-item list-group-item-danger">
					    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> remove from workspace</h4>
					  </a>
					  
					  <?php if ( file_exists($modelPath) ) { ?>
					  <a href="index.php?site=modelBrowser&file=<?php echo $file; ?>&source=<?php echo $sourceRepo;?>" class="list-group-item">
					    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span> browse source file</h4>
					  </a>
					  <a href="<?php echo $modelPath; ?>" download="<?php echo $sourceFilename; ?>" class="list-group-item">
					    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> download source file</h4>
					  </a>	  
					  <?php } ?>
					</div>
		
					<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			  					  
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingOne">
					      <h4 class="panel-title">
					        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					          Some metrics
					        </a>
					      </h4>
					    </div>
					    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
					      
					        <table class="table table-hover">
							    <tr>
							    	<td>#nodes</td>
							    	<td align="right"><b><?php echo $numNodes; ?></b></td>
							    </tr>
							    <tr>
							    	<td>#edges</td>
							    	<td align="right"><b><?php echo $numEdges; ?></b></td>
							    </tr>
							    <tr class="active">
							    	<td></td>
							    	<td></td>
							    </tr>
							    <tr>
							    	<td>#functions</td>
							    	<td align="right"><b><?php echo $numActivities; ?></b></td>
							    </tr>
							    <tr>
							    	<td>#events</td>
							    	<td align="right"><b><?php echo $numEvents; ?></b></td>
							    </tr>
							    <tr>
							    	<td>#connectors</td>
							    	<td align="right"><b><?php echo $numConnectors; ?></b></td>
							    </tr>
							    <tr class="active">
							    	<td></td>
							    	<td></td>
							    </tr>
							    <tr>
							    	<td>#XOR</td>
							    	<td align="right"><b><?php echo $numXOR; ?></b></td>
							    </tr>
							    <tr>
							    	<td>#OR</td>
							    	<td align="right"><b><?php echo $numOR; ?></b></td>
							    </tr>
							    <tr>
							    	<td>#AND</td>
							    	<td align="right"><b><?php echo $numAND; ?></b></td>
							    </tr>
							  </table>
					      
					    </div>
					  </div>
					</div>
		
		<?php 
			} else { ?>
			
			<h2>Operations</h2>
			
			<?php 
						
				$workspaceActionHandler = new WorkspaceActionHandler();
				$workspaceActionMenu = $workspaceActionHandler->getActionMenu();
				echo $workspaceActionMenu;
			
			}
			
			
			?>
    </div>
</div>