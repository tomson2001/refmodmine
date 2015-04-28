<?php
$startpos = strrpos($_SESSION['uploadedFilePath'], "/") + 1;
$filename = substr($_SESSION['uploadedFilePath'], $startpos, -5);
$_REQUEST['file'] = $filename;
$_REQUEST['source'] = "userRepo";
?>