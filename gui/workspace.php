<?php 
// load workspace
$workspace = new WorkspaceEPML();

$modelID = isset($_REQUEST['modelID']) ? $_REQUEST['modelID'] : null;
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

    	<h2>Models</h2>
    		<ul class="list-group">
			  <li class="list-group-item list-group-item-warning">
			    <h4 class="list-group-item-heading">Workspace</h4>
			    <p class="list-group-item-text">
			    	<b><?php echo $workspace->numModels; ?> models</b> from <b><?php echo $workspace->numSources." ".$sourcesString; ?></b><br /><br />
			    	<a href="<?php echo $downloadPath; ?>" download="workspace.epml" class="list-group-item-warning"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> download as EPML</a>
			    	&nbsp;<a href="<?php echo $reloadLink; ?>&action=doClearWorkspace" class="list-group-item-warning"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> clear</a>
			    </p>
			  </li>
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
			</ul>
	        <div class="list-group">
	        <?php 
	        foreach ( $workspace->epcs as $currEpcID => $currEpc ) { ?>
	        	<a href="<?php echo $reloadLink."&modelID=".$currEpcID; ?>" class="list-group-item"><small><?php echo $currEpc->name; ?></small></a>
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
					?>
						<h2>Available Data</h2>
						
						<?php
						$workspaceData = $workspace->getAvailableData();
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
							foreach ( $workspaceData->files as $wFile => $wFilename ) {  ?>
							 <tr>
							 	<td><span class="<?php echo $workspaceData->getFileIcon($wFile); ?>" aria-hidden="true"></span></td>
							 	<td><?php echo $workspaceData->getFileType($wFile); ?></td>
							 	<td><?php echo $workspaceData->getFileDescription($wFile); ?></td>
							 	<td>
							 		<?php if ( !is_null($workspaceData->getOpeningMethod($wFile)) ) { ?><a href="index.php?site=<?php echo $workspaceData->getOpeningMethod($wFile); ?>&file=<?php echo $wFile; ?>" title="show file" alt="show file"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a><?php } ?>
							 		<a href="<?php echo $workspaceData->getDownloadLink($wFile); ?>" download="<?php echo $workspaceData->getDownloadFilename($wFile); ?>" title="download CSV" alt="download CSV"><span class="glyphicon glyphicon-save" aria-hidden="true"></span></a>
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
			        <h2>Details</h2>
			        
			<?php if ( !is_null($epc) ) { 

        	// load data to show
        	$modelPath = $epc->modelPathOnly;
        	$modelPath = substr($modelPath, 1);
        	$pos = strrpos($modelPath, "/")+1;
        	$sourceFilename = substr($modelPath, $pos);
        	$file = str_replace(".epml", "", $sourceFilename);
        	
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
					  <a href="#" class="list-group-item">
					    <p class="list-group-item-text">Model</p>
					    <h4 class="list-group-item-heading"><?php echo $epc->name; ?></h4>
					    <p class="list-group-item-text"><?php echo $modelPath; ?></p>
					  </a>
					  <a href="index.php?site=workspace&epcID=<?php echo $epc->id; ?>&action=doRemoveModelFromWorkspace" class="list-group-item list-group-item-danger">
					    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span> remove from workspace</h4>
					  </a>
					  <a href="index.php?site=modelBrowser&file=<?php echo $file; ?>&source=<?php echo $sourceRepo;?>" class="list-group-item">
					    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span> browse source file</h4>
					  </a>
					  <a href="<?php echo $modelPath; ?>" download="<?php echo $sourceFilename; ?>" class="list-group-item">
					    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> download source file</h4>
					  </a>	  
					</div>
		
					<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingTwo">
					      <h4 class="panel-title">
					        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
					          Tools
					        </a>
					      </h4>
					    </div>
					    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
					      <div class="panel-body">
					        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
					      </div>
					    </div>
					  </div>
					  					  
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
		
		<?php } else {?>
		
			<!-- no model selected ==> whole workspace -->
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingOne">
					      <h4 class="panel-title">
					        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					          Tools
					        </a>
					      </h4>
					    </div>
					    <div id="collapseOne" class="panel-collapse collapse in list-group" role="tabpanel" aria-labelledby="headingOne">
					        <a href="<?php echo $reloadLink; ?>&action=doRMM_CLI_Metrics" class="list-group-item">Calculate Metrics</a>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingTwo">
					      <h4 class="panel-title">
					        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
					          Process Model Similarity 
					        </a>
					      </h4>
					    </div>
					    <div id="collapseTwo" class="panel-collapse collapse list-group" role="tabpanel" aria-labelledby="headingTwo">
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=ssbocan" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo SimilarityScoreBasedOnCommonActivityNames::$literatureSource; ?>" data-placement="bottom">Common Activity Names</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=lms" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo LabelMatchingSimilarity::$literatureSource; ?>" data-placement="bottom">Label Matching Similarity</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=fbse" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo FeatureBasedSimilarityEstimation::$literatureSource; ?>" data-placement="bottom">Feature Based Similarity Estimation</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=pocnae" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo PercentageOfCommonNodesAndEdges::$literatureSource; ?>" data-placement="bottom">Common Nodes And Edges</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=geds" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo GraphEditDistanceSimilarity::$literatureSource; ?>" data-placement="bottom">Graph Edit Distance Similarity</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=amaged" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo ActivityMatchingAndGraphEditDistance::$literatureSource; ?>" data-placement="bottom">Activity Matching And Graph Edit Distance</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=cf" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo CausalFootprints::$literatureSource; ?>" data-placement="bottom">Causal Footprints</a>
					    </div>
					  </div>
					  <div class="panel panel-default">
					    <div class="panel-heading" role="tab" id="headingThree">
					      <h4 class="panel-title">
					        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
					          Process Matching
					        </a>
					      </h4>
					    </div>
					    <div id="collapseThree" class="panel-collapse collapse list-group" role="tabpanel" aria-labelledby="headingTwo">
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=ssbocan" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo SimilarityScoreBasedOnCommonActivityNames::$literatureSource; ?>" data-placement="bottom">Common Activity Names</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=lms" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo LabelMatchingSimilarity::$literatureSource; ?>" data-placement="bottom">Label Matching Similarity</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=fbse" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo FeatureBasedSimilarityEstimation::$literatureSource; ?>" data-placement="bottom">Feature Based Similarity Estimation</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=pocnae" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo PercentageOfCommonNodesAndEdges::$literatureSource; ?>" data-placement="bottom">Common Nodes And Edges</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=geds" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo GraphEditDistanceSimilarity::$literatureSource; ?>" data-placement="bottom">Graph Edit Distance Similarity</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=amaged" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo ActivityMatchingAndGraphEditDistance::$literatureSource; ?>" data-placement="bottom">Activity Matching And Graph Edit Distance</a>
					        <a href="<?php echo $reloadLink; ?>&action=doCalculateSimilarityMatrix&measure=cf" class="list-group-item" data-toggle="tooltip" title="Source: <?php echo CausalFootprints::$literatureSource; ?>" data-placement="bottom">Causal Footprints</a>
					    </div>
					  </div>
					  
					  
					</div>
		<?php } ?>
    </div>
</div>