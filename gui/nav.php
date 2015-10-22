    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">RefMod-Miner</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li <?php if (is_null($site) || $site == "home" ) { ?>class="active" <?php } ?>><a href="index.php">Home</a></li>
            <li <?php if ( $site == "repository" ) { ?>class="active" <?php } ?>><a href="index.php?site=repository">Model Repository</a></li>
            <li <?php if ( $site == "workspace" || $site == "workspaceCSVViewer" ) { ?>class="active" <?php } ?>><a href="index.php?site=workspace">Workspace <span class="badge"><?php echo $_SESSION['numWorkspaceModels']; ?></span></a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Tools &amp; Infos<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li class="dropdown-header">Converter</li>
                <li><a href="index.php?site=converter">AML / EPML</a></li>
                <li><a href="index.php?site=converter_PNML-EPC">PNML / EPML</a></li>
                <li><a href="index.php?site=converter_BPMN-EPC">BPMN / EPML</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Tool Information</li>
                <li><a href="index.php?site=fileTypeSpecifications">File Type Specifications</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">General Information</li>
                <li><a href="http://refmod-miner.dfki.de/cms/anwendungsfaelle/" target="_blank">Use Cases</a></li>
                <li><a href="http://refmod-miner.dfki.de/cms/files/manual/refmod_funktionsbeschreibung.pdf" target="_blank">RefMod-Miner Manual</a></li>
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li <?php if ( $site == "about" ) { ?>class="active" <?php } ?>><a href="http://bpm.dfki.de/" target="_blank">About</a></li>
            <li <?php if ( $site == "contact" ) { ?>class="active" <?php } ?>><a href="index.php?site=contact">Contact</a></li>
          </ul>
        </div>
      </div>
    </nav>
   