<?php

$email = isset($_POST['email']) ? $_POST['email'] : "";

if ( empty($email) ) {
	$_SESSION['email'] = $email;
	$_POST["msg"] = "<strong>E-Mail address successfully removed. </strong> You will not be informed on your calculations by e-mail!";
	$_POST["msgType"] = "warning";
} elseif (filter_var($email, FILTER_VALIDATE_EMAIL) ) {
	$_SESSION['email'] = $email;
	$_POST["msg"] = "<strong>E-Mail address successfully saved. </strong> You will now get informed on your calculations by e-mail.";
	$_POST["msgType"] = "success";
} else {
	$_POST["msg"] = "<strong>E-Mail address invalid. </strong> Please check your e-mail address and try again.";
	$_POST["msgType"] = "danger";
}


?>