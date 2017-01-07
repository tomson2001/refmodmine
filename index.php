<?php 
if ( isset($_REQUEST['sid']) ) session_id($_REQUEST['sid']);
session_start();
if ( !isset($_SESSION['numWorkspaceModels']) ) $_SESSION['numWorkspaceModels'] = 0;  
if ( !isset($_SESSION['modelsInWorkspace']) ) $_SESSION['modelsInWorkspace'] = array();
if ( !isset($_SESSION['email']) ) $_SESSION['email'] = "";
if ( !isset($_SESSION['workspaceOpened']) ) $_SESSION['workspaceOpened'] = false;
require 'autoloader.php';

// DATABASE CONNECTION
$mdb2_dsn = array(
		'phptype'  => Config::DB_TYPE,
		'username' => Config::DB_USER,
		'password' => Config::DB_PASS,
		'hostspec' => Config::DB_HOST,
		'database' => Config::DB_DATABASE
);

$mdb2_options = array(
		'debug' => 2
);


$db = &MDB2::connect($mdb2_dsn, $mdb2_options);

// ERROR HANDLING
if (PEAR::isError($db)) {
	Logger::log($_SESSION['email'], "Error connecting to database: ".$db->getMessage(), "ACCESS");
	Logger::log($_SESSION['email'], "Error connecting to database: ".$db->getMessage(), "ERROR");
	die($db->getMessage());
}
// DATABASE CONNECTED

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$onload = "";

function callAction($action) {
	include 'gui/actions/'.$action.'.php';
}

// Proceed Actions
if ( !is_null($action) ) callAction($action);

// Special Calls
if ( $action == "modelBrowser" ) {
	callAction("doPrepareModelVisualization");
}

// Messages
$msg = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  	<meta charset="UTF-8">
  	<title>RefMod-Miner as a Service</title>
  	<META name="description" content="RefMod-Miner as a Service">
	<META name="keywords"  content="RefMod-Miner, rmmaas, refmodminer, Referenzmodellierung, BPM, Business Process Management, NLP, Natural Language Processing, Process Matching, EPC, Process Clustering, Process Model Similarity, Tom Thaler">
	<META name="author" content="Tom Thaler">
   	<link rel="stylesheet" href="gui/lib/bootstrap-3.3.4-dist/css/bootstrap.min.css">
   	<link rel="stylesheet" href="gui/lib/bootstrap-3.3.4-dist/css/bootstrap-theme.min.css">
	
   	<link href="gui/css/navbar-fixed-top.css" rel="stylesheet">
   	<script type="text/javascript" src="gui/lib/jQuery-2.1.3/jquery-2.1.3.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
   	<script src="gui/lib/bootstrap-3.3.4-dist/js/bootstrap.min.js"></script>
   	<link rel="stylesheet" href="gui/lib/jQuery-File-Upload-9.9.3/css/jquery.fileupload.css">
        <link href="gui/lib/jQuery-contextMenu-2.2.4/dist/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="gui/lib/jQuery-contextMenu-2.2.4/dist/jquery.ui.position.js"></script>
        <script type="text/javascript" src="gui/lib/jQuery-contextMenu-2.2.4/dist/jquery.contextMenu.js"></script>
   	<link href="gui/lib/bootstrap-slider/css/bootstrap-slider.css" rel="stylesheet">
   	<script type='text/javascript' src="gui/lib/bootstrap-slider/js/bootstrap-slider.js"></script>
   	<link href="gui/lib/bootstrap-toggle/css/bootstrap-toggle.min.css" rel="stylesheet">
	<script src="gui/lib/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>
        <script src="gui/lib/color-picker-master/color-picker.min.js"></script>
        <link href="gui/lib/color-picker-master/color-picker.min.css" rel="stylesheet">
        
        <link href="gui/lib/jQuery-tabdrop/css/tabdrop.css" rel="stylesheet">
        <script type="text/javascript" src="gui/lib/jQuery-tabdrop/js/bootstrap-tabdrop.js"></script>

   	<!-- Graph Bib -->
   	<script type="text/javascript" src="gui\lib\jasmine_tests\src\EPCViz_NEW.js"></script>
    <script type="text/javascript" src="gui\lib\jasmine_tests\src\MatchVizMultiple.js"></script>
    <script type="text/javascript" src="gui\lib\svg-pan-zoom-3.2.10\dist\svg-pan-zoom.js"></script>
  	<link href="gui/lib/visjs/dist/vis.css" rel="stylesheet" type="text/css" />
        
        <script type="text/javascript" src="gui\lib\hyphenator\Hyphenator.js"></script>
        <script type="text/javascript" src="gui\lib\hyphenator\patterns\en-gb.js"></script>
<script type="text/javascript" src="gui\lib\hyphenator\patterns\de.js"></script>

  	<script type="text/javascript">
		$(document).ready(function(){
		    $('[data-toggle="popover"]').popover();   
		});
	</script>
	
	<script type="text/javascript">
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();   
		});
	</script>
  	
</head>
<body <?php
// Special handling for model visualizuation 
if ( (isset($_REQUEST['modelPath']) || isset($_REQUEST['modelID'])) && isset($_REQUEST['site']) && ($_REQUEST['site'] == "modelBrowser" || $_REQUEST['site'] == "workspace") )  echo ' onload="drawEPC()"';
?>>
<?php 
if ($action == "getMatching"){
    
} else {
include 'gui/main.php'; 
}
?>
</body>
</html>
