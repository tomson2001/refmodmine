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

if ( strtolower(substr($input, -5)) === ".epml" ) {
	$target = substr($input, 0, -5).".epml";
	exec("mv $input $target");
	$input = $target;
	$outputFormat = "PNML"; // in fact, it will be zipped, since pnml does not support multiple models in one file, while epml does
	$output = substr($input, 0, -5)."_pnml.zip";
}

if ( is_null($outputFormat) ) {
	$_POST["msg"] = "<strong>An error occured. </strong> The input filetype could not be detected. Please do only use .pnml or .epml files.";
	$_POST["msgType"] = "danger";
	Logger::log($email, "PNML/EPML Conversion failed.  The input filetype could not be detected. Input: ".$input, "ERROR");
} else {
	$_POST["input"] = $input;
	$_POST["output"] = $output;
	$_POST["notification"] = $email;
	
	$actionHandler = new WorkspaceActionHandler();
	
	if ( $outputFormat == "EPML" ) {
		$actionHandler->run("CONVERT_PNML2EPML");
	} elseif ( $outputFormat == "PNML" ) {
		$actionHandler->run("CONVERT_EPML2PNML");
	}
}
sleep(1);


?>