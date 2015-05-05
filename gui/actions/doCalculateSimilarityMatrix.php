<?php
// asynchronous processing
$workspace = new WorkspaceEPML();
$notification = empty($_SESSION["email"]) ? "no" : $_SESSION["email"];
exec("php CLIModelSimilarity.php measure=".$_REQUEST['measure']." input=".$workspace->file." output=".$workspace->file.".simmatrix.".$_REQUEST['measure']." notification=".$notification." > /dev/null &");

$_POST["msg"] = "<strong>Similarity Calculation started. </strong> Please wait and refresh the site until the result is available. That may take a while.";
$_POST["msgType"] = "info";
 
?>