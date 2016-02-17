<?php
$input = isset($_SESSION['uploadedFilePath']) ? str_replace("../../", "", $_SESSION['uploadedFilePath']) : "";

if ( substr_count($input, " ") > 0 ) {
	$target = str_replace(" ", "_", $input);
	$exec = "mv ".str_replace(" ", "\ ", $input)." ".$target;
	$result = exec($exec);
	$input = str_replace(" ", "_", $input);
}

$output = substr($input, 0, -5).".zip";;
$email = empty($_SESSION["email"]) ? "no" : $_SESSION["email"];

$_POST["input"] = $input;
$_POST["output"] = $output;
$_POST["notification"] = $email;

$actionHandler = new WorkspaceActionHandler();
$actionHandler->run("SPLIT_EPML");

?>