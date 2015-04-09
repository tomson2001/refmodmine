<?php 
session_start();
require 'autoloader.php';
$action = isset($_REQUEST['action']) ? isset($_REQUEST['action']) : null;
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  	<meta charset="utf-8">
  	<title>RefModMining</title>
   	<link rel="stylesheet" href="gui/lib/bootstrap-3.3.4-dist/css/bootstrap.min.css">
   	<link rel="stylesheet" href="gui/lib/bootstrap-3.3.4-dist/css/bootstrap-theme.min.css">
   	<link href="gui/css/navbar-fixed-top.css" rel="stylesheet">
   	<script type="text/javascript" src="gui/lib/jQuery-2.1.3/jquery-2.1.3.min.js"></script>
   	<script src="gui/lib/bootstrap-3.3.4-dist/js/bootstrap.min.js"></script>
   	<link rel="stylesheet" href="gui/lib/jQuery-File-Upload-9.9.3/css/jquery.fileupload.css">

   	<!-- Graph Bib -->
   	<script type="text/javascript" src="gui/lib/visjs/dist/vis.js"></script>
  	<link href="gui/lib/visjs/dist/vis.css" rel="stylesheet" type="text/css" />
 	
</head>
<body <?php
// Special handling for model visualizuation 
if ( isset($_REQUEST['modelPath']) && isset($_REQUEST['site']) && $_REQUEST['site'] == "modelBrowser" )  echo ' onload="drawEPC()"';
?>>
<?php include 'gui/main.php'; ?>
</body>
</html>