<?php 
// load selected epml
$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null;
$fileSource = isset($_REQUEST['source']) ? $_REQUEST['source'] : null;
$epml = new EPML($file, $fileSource);

$modelPath = isset($_REQUEST['modelPath']) ? $_REQUEST['modelPath'] : null;
$epc = $epml->getEPC($modelPath);

$visualizer = is_null($modelPath) ? null : new EPCVisualizer($epc);
$jsCode = is_null($modelPath) ? null : $visualizer->generateVisJSCode();

$directoriesString = $epml->numDirectories < 2 ? "directory" : "directories";

// load download path
$downloadPath = Config::REPOSITORY_PATH;
if ( $fileSource == "userRepo" ) {
	$sessionID = session_id();
	$downloadPath = Config::FILES_PATH."/".$sessionID;
}

$reloadLink = "index.php?site=modelBrowser&file=".$file."&source=".$fileSource;

?>
<div class="row">
    <div class="col-md-3">

    	<h2>Models</h2>
    	
	        <div class="list-group">
	        <?php 
	        foreach ( $epml->epcs as $currEpc ) { ?>
	        	<a href="<?php echo $reloadLink."&modelPath=".$currEpc->modelPath; ?>" class="list-group-item"><small><?php echo $currEpc->name; ?></small></a>
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
        <ul class="list-group">
		  <li class="list-group-item list-group-item-warning">
		    <h4 class="list-group-item-heading"><?php echo $epml->epmlName; ?>.epml</h4>
		    <p class="list-group-item-text">
		    	<b><?php echo $epml->numModels; ?> models</b> in <b><?php echo $epml->numDirectories." ".$directoriesString; ?></b><br /><br />
		    	<a href="<?php echo $downloadPath.'/'.$epml->epmlName.'.epml'; ?>" download="<?php echo $epml->epmlName; ?>.epml" class="list-group-item-warning"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> download</a> &nbsp;
		    	<a href="index.php?site=modelBrowser&file=<?php echo $file; ?>&addFileToWorkspace=<?php echo $file; ?>&source=<?php echo $fileSource;?>" class="list-group-item-warning"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> add to workspace</a>
		    </p>
		  </li>
		</ul>
        <?php if ( !is_null($epc) ) { 

        	// load data to show
        	$modelPath = $epc->modelPathOnly;
        	$modelPath = str_replace("/root/", "", $modelPath);
        	$modelPath = str_replace("/Root/", "", $modelPath);
        	
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
		    <h4 class="list-group-item-heading"><?php echo $epc->name; ?>.epml</h4>
		    <p class="list-group-item-text"><?php echo $modelPath; ?></p>
		  </a>
		  <a href="index.php?site=repository&file=<?php echo $file; ?>&addFileToWorkspace=<?php echo $file; ?>&source=<?php echo $fileSource;?>" class="list-group-item">
		    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> add model to workspace</h4>
		  </a>		  
		</div>
		
		<div class="panel panel-default">
		  <!-- Default panel contents -->
		  <div class="panel-heading"><b>Some metrics</b></div>
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
		<?php } ?>
    </div>
</div>