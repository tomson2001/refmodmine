<?php 
if ( isset($_REQUEST['sid']) ) session_id($_REQUEST['sid']);
session_start();
if ( !isset($_SESSION['email']) ) $_SESSION['email'] = "";
require 'autoloader.php';


// DATABASE CONNECTION
$mdb2_dsn = array(
		'phptype'  => Config::DB_TYPE,
		'username' => Config::DB_USER,
		'password' => Config::DB_PASS,
		'hostspec' => Config::DB_HOST,
		'database' => Config::DB_DATABASE
);

$mdb2_options = array(
		'debug' => 2
);

$db = &MDB2::connect($mdb2_dsn, $mdb2_options);

// ERROR HANDLING
if (PEAR::isError($db)) {
	Logger::log($_SESSION['email'], "Error connecting to database: ".$db->getMessage(), "ACCESS");
	Logger::log($_SESSION['email'], "Error connecting to database: ".$db->getMessage(), "ERROR");
	die($db->getMessage());
}
// DATABASE CONNECTED

// Messages
$msg = null;
$formVal_first_name = "";
$formVal_last_name = "";
$formVal_email = "";

if ( isset($_POST["register_participant"]) ) {
	$first_name = $_POST["first_name"];
	$last_name = $_POST["last_name"];
	$email = $_POST["email"];
	
	$formVal_first_name = "value=\"".$first_name."\" ";
	$formVal_last_name = "value=\"".$last_name."\" ";
	$formVal_email = "value=\"".$email."\" ";
	
	$first_name_check = trim($_POST["first_name"]);
	$last_name_check = trim($_POST["last_name"]);
	$email_check = trim($_POST["email"]);
	
	if ( is_null($first_name) || is_null($last_name) || is_null($email) || empty($first_name_check) || empty($last_name_check) || empty($email_check) ) {
		$msg = "<div class=\"alert alert-danger\" role=\"alert\"><strong>Fehlende Angaben</strong> Es fehlen noch Angaben.</div>";
	} elseif ( !Tools::endsWith(strtolower($email), "@iwi.dfki.de") ) {
		$msg = "<div class=\"alert alert-danger\" role=\"alert\"><strong>E-Mail ungültig!</strong> Es sind nur IWi-E-Mail-Adressen zugelassen (@iwi.dfki.de)</div>";
	} elseif ( ChristmasParticipant::exists($email) ) {
		$msg = "<div class=\"alert alert-info\" role=\"alert\"><strong>Netter Versuch!</strong> Aber du bist schon angemeldet ;-)</div>";
	} elseif ( !ChristmasParticipant::checkNewIWiParticipantPlausibility($first_name, $last_name, $email) ) { 
		$msg = "<div class=\"alert alert-warning\" role=\"alert\"><strong>Angaben nicht plausibel!</strong> Falls du der Meinung bist, dass deine Angaben korrekt sind, wende dich bitte per E-Mail an tom.thaler@dfki.de.</div>";
	} else {
		
		$participant = new ChristmasParticipant();
		$participant->_year = date("Y");
		$participant->first_name = $first_name;
		$participant->last_name = $last_name;
		$participant->email = strtolower($email);
		$participant->blocked = false;
		$participant->register_pot = date(Config::DB_DATETIME_FORMAT);
		$participant->ip = $_SERVER['REMOTE_ADDR'];
		$participant->save();
		$msg = "<div class=\"alert alert-success\" role=\"alert\"><strong>Sehr sch&ouml;n!</strong> Du hast dich erfolgreich angemeldet. Nun hei&szlig;t es Daumen dr&uuml;cken!</div>";
		
		$formVal_first_name = "";
		$formVal_last_name = "";
		$formVal_email = "";
	}
	
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  	<meta charset="UTF-8">
  	<title>RMMaaS - IWi-Adventskalender</title>
  	<META name="description" content="RefMod-Miner as a Service">
	<META name="keywords"  content="RefMod-Miner, rmmaas, refmodminer, Referenzmodellierung, BPM, Business Process Management, NLP, Natural Language Processing, Process Matching, EPC, Process Clustering, Process Model Similarity, Tom Thaler">
	<META name="author" content="Tom Thaler">
   	<link rel="stylesheet" href="gui/lib/bootstrap-3.3.4-dist/css/bootstrap.min.css">
   	<link rel="stylesheet" href="gui/lib/bootstrap-3.3.4-dist/css/bootstrap-theme.min.css">
   	<link rel="stylesheet" href="gui/css/sticky-footer-navbar.css">
	
   	<link href="gui/css/navbar-fixed-top.css" rel="stylesheet">
   	<script type="text/javascript" src="gui/lib/jQuery-2.1.3/jquery-2.1.3.min.js"></script>
   	<script src="gui/lib/bootstrap-3.3.4-dist/js/bootstrap.min.js"></script>
   	<link rel="stylesheet" href="gui/lib/jQuery-File-Upload-9.9.3/css/jquery.fileupload.css">
   	<link href="gui/lib/bootstrap-slider/css/bootstrap-slider.css" rel="stylesheet">
   	<script type='text/javascript' src="gui/lib/bootstrap-slider/js/bootstrap-slider.js"></script>
   	<link href="gui/lib/bootstrap-toggle/css/bootstrap-toggle.min.css" rel="stylesheet">
	<script src="gui/lib/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>

  	<script type="text/javascript">
		$(document).ready(function(){
		    $('[data-toggle="popover"]').popover();   
		});
	</script>
	
	<script type="text/javascript">
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();   
		});
	</script>
  	
</head>
<body style="background: url(gui/img/christmas_background.jpg) no-repeat; background-size:100% auto;">
<div class="container">

<div class="row">

	<div class="col-md-1">
		<img src="gui/img/iwi-weihnachtslogo.png" alt="IWi-Adventskalender">
	</div>
	<div class="col-md-1">&nbsp;</div>
	<div class="col-md-9">
	
	<?php 
	
	$start = GregorianToJD("12","01",date("Y"));
	$current = GregorianToJD(date("m"),date("d"),date("Y"));
	
	$dayDiff = $start - $current;
	
	if ( $dayDiff <= -24 ) {
		echo "<h1 style=\"color: white; font-size: 70px\">FROHE WEIHNACHTEN!</h1>";
	} elseif ( $dayDiff <= 0 && $dayDiff > -24 ) { 
		$today = ChristmasDays::getTodaysAssignment();
		if ( !is_null($today) ) {
			echo "<h1 style=\"color: white; font-size: 70px\"><small style=\"color: white\">".$today->_day.". Socke: </small>".$today->first_name." ".$today->last_name."</h1>";
		} else {
			echo "<h1 style=\"color: white; font-size: 70px\"><small style=\"color: white\">Die heutige Socke wird bald zugeordnet!</small></h1>";
		}
	} else {
		$dayStr = $dayDiff == 1 ? "Tag" : "Tage";
		echo "<h1 style=\"color: white; font-size: 70px\"><small style=\"color: white\">nur noch</small> <strong>".$dayDiff."</strong> <small style=\"color: white\">".$dayStr." bis zum Kalenderstart!</small></h1>";
	}
	
	?>
	
	</div>
	
    
</div>

<div class="row">
	<div class="col-md-7">&nbsp;</div>
	<div class="col-md-1">&nbsp;</div>
    <div class="col-md-3">&nbsp;</div>
</div>

<div class="row">

	<div class="col-md-7" style="background-color: white; overflow: auto; height: 600px;">

      <div class="caption">
        <h3>Zuordnung der Socken</h3>
        <table class="table table-striped table-hover" style="table-layout: auto;">
        <?php 
        $i = 0;
        $christmasDays = new ChristmasDays();
        foreach ( $christmasDays->days as $day ) { ?>
			<tr>
				<td><?php echo $day->_day;?></td>
				<td><?php 
				if ( is_null($day->email) ) {
					echo "-";
				} else {
					echo "<strong>".$day->first_name." ".$day->last_name."</strong>";
				}
				?></td>
			</tr>
		<?php
			$i++;
		}
        ?>
        </table>
      </div>

  </div>
  
  <div class="col-md-1">&nbsp;</div>

  <div class="col-md-3" style="background-color: white; ">

      <div class="caption" style="opacity: 1;">
      
        <h3>Ich will mich anmelden</h3>
        
        <?php echo $msg; ?>
        
         <form method="post">
      	<div class="form-group">
		    <label for="first_name">Vorname</label>
		    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Vorname" <?php echo $formVal_first_name;?>>
		  </div>
		  
		  <div class="form-group">
		    <label for="last_name">Nachname</label>
		    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Nachname" <?php echo $formVal_last_name;?>>
		  </div>
        
		  <div class="form-group">
		    <label for="email">Deine IWi-E-Mail-Adresse</label>
		    <input type="email" class="form-control" id="email" name="email" placeholder="vorname.nachname@iwi.dfki.de" <?php echo $formVal_email;?>>
		  </div>

		  <button type="submit" name="register_participant" id="register_participant" class="btn btn-default">Anmelden</button>
		  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#participants">Teilnehmerliste (<?php echo ChristmasParticipant::getNumParticipants(); ?>)</button>
		</form>
          
          <p></p>
          <p></p>
          
          <!-- Button trigger modal -->
			
			<!-- Modal -->
			<div class="modal fade" id="participants" tabindex="-1" role="dialog" aria-labelledby="Teilnehmerliste">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="myModalLabel">Teilnehmerliste <?php echo date("Y");?></h4>
			      </div>
			      <div class="modal-body">
			        <ul>
			        	<?php 
			        	$participants = ChristmasParticipant::getAll();
			        	foreach ( $participants as $participant ) {
							echo "<li>".$participant->first_name." ".$participant->last_name."</li>";
						}
			        	?>
			        </ul>
			      </div>
			    </div>
			  </div>
			</div>
			
			<p></p>

      </div>
     

  </div>
  
</div>
</div>

<footer class="footer">
      <div class="container">
        <p class="text-muted">Jetzt <a href="http://rmm.dfki.de" title="RefMod-Miner as a Service (RMMaaS)" alt="RefMod-Miner as a Service (RMMaaS)" target="_blank">RefMod-Minen im Browser</a>! ;-)</p>
      </div>
    </footer>

</body>
</html>
