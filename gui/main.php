<?php
include_once 'google_analytics_tracking.php';
$site = isset($_REQUEST['site']) ? $_REQUEST['site'] : null;
$stats = new SiteStats();
$stats->trackSiteVisit();

include 'nav.php';
?>



<?php
if ( !is_null($site) ) {
    if ($site == "workspaceMatchingEditor"){
        include $site.'.php';
    } else {
        ?>
    <div class="container"><?php
	include $site.'.php';?>
    </div> <?php
    }
		
} else {
    ?>
    <div class="container"><?php
	include 'home.php';?>
    </div> <?php
}
?>

