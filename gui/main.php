<?php
$site = isset($_REQUEST['site']) ? $_REQUEST['site'] : null;

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