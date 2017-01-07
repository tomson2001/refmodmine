<?php

require './process-model/EPC.php';
require './workspace/WorkspaceEPML.php';
require './process-model-mapping/GenericMapping.php';
require './process-model-mapping/MappingFile.php';
require './tools/FileGenerator.php';
require 'Config.php';
session_start();

// if isset is missing
$action = $_POST["action"];
$matchID = (is_numeric($_POST['matchID']) ? (int) $_POST['matchID'] : -1);
$matchingID = (is_numeric($_POST['matchingID']) ? (int) $_POST['matchingID'] : -1);
$nodeID = (is_numeric($_POST['nodeID']) ? (int) $_POST['nodeID'] : -1);
$file = "workspace.epml." . $_POST["fileType"] . "matching." . $_POST["fileName"];

$workspace = $_SESSION['workspace'];
//$workspace = new WorkspaceEPML();
//$workspaceData = $workspace->getAvailableData();

$mappingFile = null; //$workspace->getMatchingFile($file);

if ($action == "update") {
    $updatedMapping = json_decode($_POST['matching'], true);
    if ($mappingFile == null) {
        $workspace->createNewMatchingFile($file, $_POST["fileType"]);
        $mappingFile = $workspace->getMatchingFileInWorkspace($file);
    }
    $mappingFile->matchings = $updatedMapping;
}

if ($action == "delete") {
    if ($matchID !== null && $nodeID === -1) {
        // delete a match
        $mapping->removeMatch($matchID);
    }
    if ($matchID !== null && $nodeID !== null) {
        // delete a match node
        $mapping->removeNodeFromMap($nodeID);
    }
}

$mappingFile->save($workspace);
//$mapping->exportRDF_BPMContest2015(false, $mapping->filename); //exportRDF_BPMContest2015_Dataset3(false, $mapping->filename);
//$json = $workspace->mappingList[0]->getJSON();
echo "$json";
?>