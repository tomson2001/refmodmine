<?php
$processingAction = isset($_REQUEST['processingAction']) ? $_REQUEST['processingAction'] : "";
$actionHandler = new WorkspaceActionHandler();
$actionHandler->run($processingAction);
?>