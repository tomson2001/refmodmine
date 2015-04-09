<?php
$sessionID = session_id();
$path = Config::FILES_PATH."/".$sessionID;
if ( is_dir($path) ) $files = scandir($path);

// remove all epmls
foreach ( $files as $file ) {
	if ( substr($file, -5) == ".epml" ) unlink($path."/".$file);
}
?>