<?php
include_once 'google_analytics_tracking.php';
$site = isset($_REQUEST['site']) ? $_REQUEST['site'] : null;
$stats = new SiteStats();
$stats->trackSiteVisit();

include 'nav.php';
?>

<div class="container">

<?php
if ( !is_null($site) ) {
	include $site.'.php';	
} else {
	include 'home.php';
}
?>

</div>