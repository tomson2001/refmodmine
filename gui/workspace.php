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
<div class="row">
    <div class="col-md-3">

    	<h2>Models</h2>
    		<ul class="list-group">
			  <li class="list-group-item list-group-item-warning">
			    <h4 class="list-group-item-heading">Workspace</h4>
			    <p class="list-group-item-text">
			    	<b><?php echo $workspace->numModels; ?> models</b> from <b><?php echo $workspace->numSources." ".$sourcesString; ?></b><br /><br />
			    	<a href="<?php echo $downloadPath; ?>" download="workspace.epml" class="list-group-item-warning"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> download as EPML</a>
			    </p>
			  </li>
			</ul>
	        <div class="list-group">
	        <?php 
	        foreach ( $workspace->epcs as $currEpcID => $currEpc ) { ?>
	        	<a href="<?php echo $reloadLink."&modelID=".$currEpcID; ?>" class="list-group-item"><small><?php echo $currEpc->name; ?></small></a>
	        <?php } ?>
			</div>
			    </div>
			    <div class="col-md-6">
			        <h2>Preview</h2>
					  
					<?php 
			        if ( is_null($epc) ) {
					?>
			        <p>Please select a model.</p>
			        <?php 
					} else {
						echo $jsCode;
						echo "<div id='EPC'></div>";
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
        			<div class="list-group">
					  <a href="#" class="list-group-item">
					    <p class="list-group-item-text">Model</p>
					    <h4 class="list-group-item-heading"><?php echo $epc->name; ?></h4>
					    <p class="list-group-item-text"><?php echo $modelPath; ?></p>
					  </a>
					  <a href="index.php?site=modelBrowser&file=<?php echo $file; ?>&source=<?php echo $sourceRepo;?>" class="list-group-item">
					    <h4 class="list-group-item-heading"><span class=" glyphicon glyphicon-zoom-in" aria-hidden="true"></span> browse source file</h4>
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
					    <div class="panel-heading" role="tab" id="headingThree">
					      <h4 class="panel-title">
					        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
					          Source file options
					        </a>
					      </h4>
					    </div>
					    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
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
		
		<?php } ?>
    </div>
</div>