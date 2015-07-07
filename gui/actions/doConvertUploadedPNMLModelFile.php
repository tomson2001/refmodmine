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
$email = empty($_SESSION["email"]) ? "no" : $_SESSION["email"];

if ( strtolower(substr($input, -5)) === ".pnml" ) {
	$target = substr($input, 0, -5).".pnml";
	exec("mv $input $target");
	$input = $target;
	$outputFormat = "EPML";
	$output = substr($input, 0, -5).".epml";
}

if ( is_null($outputFormat) ) {
	$_POST["msg"] = "<strong>An error occured. </strong> The input filetype could not be detected. Please do only use .pnml files.";
	$_POST["msgType"] = "danger";
	Logger::log($email, "PNML to EPML Conversion failed.  The input filetype could not be detected. Input: ".$input, "ERROR");
} else {
	$_POST["input"] = $input;
	$_POST["output"] = $output;
	$_POST["notification"] = $email;
	$actionHandler = new WorkspaceActionHandler();
	$actionHandler->run("CONVERT_PNML2EPML");
}
sleep(1);
?>