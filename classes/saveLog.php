<?php

require './process-model/EPC.php';
require './workspace/WorkspaceEPML.php';
require './process-model-mapping/GenericMapping.php';
require './process-model-mapping/MappingFile.php';
require './tools/FileGenerator.php';
session_start();

// if isset is missing
$log = $_POST["log"];
$workspace = $_SESSION['workspace'];
$workspaceFilePath = $workspace->filepath;

$suc = chdir(Config::ABS_PATH);

$content = $log;
$fp = fopen($workspaceFilePath . "/log.txt", "a");
fwrite($fp, $content);
fclose($fp);
?>