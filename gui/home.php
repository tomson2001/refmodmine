

	<div class="jumbotron">
		<div class="container">
			<div class="col-md-7">
		    	<h1>RefMod-Miner as a Service</h1>
		        <p align="justify">This is a web-based interface for the research prototype <a href="http://refmod-miner.dfki.de" target="_blank" title="RefMod-Miner" alt="RefMod-Miner">RefMod-Miner</a> developed by the Institute for Information Systems (<a href="http://www.dfki.de/web/forschung/iwi" titel="IWi" alt="IWi" target="_blank">IWi</a>) at the German Research Center for Artificial Intelligence (<a href="http://www.dfki.de" title="DFKI" alt="DFKI" target="_blank">DFKI</a>) and Saarland University (<a href="http://iwi.uni-saarland.de" target="_blank" title="UdS - IWi" alt="UdS - IWi">UdS</a>).</p>
		        <p align="justify">The RefMod-Miner aims at supporting the analysis of process models with innovative approaches, technologies and ideas from research as well as at the automated reference process model mining.</p>
		        <p>
		          <a class="btn btn-lg btn-primary" href="index.php?site=repository" role="button">Let's get started &raquo;</a>
		        </p>
	        </div>
	        <div class="col-md-1">
	        </div>
	        <div class="col-md-4">
	        	<h1>Latest Features</h1>
	        	<?php 
	        	$actionHandler = new WorkspaceActionHandler();
	        	echo $actionHandler->getLatestFeaturesMenu();
	        	?>
	        </div>
        </div>
	</div>

