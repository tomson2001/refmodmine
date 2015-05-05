<?php
$workspace = new WorkspaceEPML();
$RMM_CLI = new RMM_CLI_CALCULATE_METRICS($workspace->file);

$_POST["msg"] = "<strong>RefMod-Miner (Java CLI). </strong> ".$RMM_CLI->execResult;
$_POST["msgType"] = substr_count($_POST["msg"], "ERROR") == 0 ? "success" : "danger";
 
?>