<?php
$filename = $_POST["uploadedFilename"];
$pos = strrpos($filename, "/");
$tmpFilename = substr($filename, 0, $pos)."matching.rdf";
exec("mv ".$filename." ".$tmpFilename);
$type = $_POST["uploadedFiletype"];
$suffix = $_POST["uploadedFileSuffix"];

$workspace = new WorkspaceEPML();

$_POST["matchings"] = $tmpFilename;
$_POST["model_set"] = $workspace->file;
$_POST["output_file"] = str_replace($suffix, str_replace($type, "xmlmatching", $suffix), $filename);
$actionHandler = new WorkspaceActionHandler();
$actionHandler->run("CONVERT_MATCHING");

sleep(2);
unlink($tmpFilename);
?>