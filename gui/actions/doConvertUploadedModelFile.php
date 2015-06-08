<?php
$input = isset($_SESSION['uploadedFilePath']) ? str_replace("../../", "", $_SESSION['uploadedFilePath']) : "";

if ( substr_count($input, " ") > 0 ) {
	$target = str_replace(" ", "_", $input);
	$exec = "mv ".str_replace(" ", "\ ", $input)." ".$target;
	$result = exec($exec);
	$input = str_replace(" ", "_", $input);
}

$outputFormat = null;
$output = $input;

if ( strtolower(substr($input, -5)) === ".epml" ) {
	$target = substr($input, 0, -5).".epml";
	exec("mv $input $target");
	$input = $target;
	$outputFormat = "AML";
	$output = substr($input, 0, -5).".xml";
} elseif ( strtolower(substr($input, -4)) === ".xml" || strtolower(substr($input, -4)) === ".aml" ) {
	$target = substr($input, 0, -4).".xml";
	exec("mv $input $target");
	$input = $target;
	$outputFormat = "EPML";
	$output = substr($input, 0, -4).".epml";
}

if ( is_null($outputFormat) ) {
	$_POST["msg"] = "<strong>An error occured. </strong> The input filetype could not be detected. Please do only use .epml, .xml or .aml files.";
	$_POST["msgType"] = "danger";
	$email = empty($_SESSION["email"]) ? "no" : $_SESSION["email"];
	Logger::log($email, "EPML/AML Conversion failed.  The input filetype could not be detected. Input: ".$input, "ERROR");
} else {
	$_POST["INPUT_DATA"] = $input;
	$_POST["OUTPUT_DATA"] = $output;
	$_POST["outputFormat"] = $outputFormat;
	$actionHandler = new WorkspaceActionHandler();
	$actionHandler->run("CONVERTER");
}
?>