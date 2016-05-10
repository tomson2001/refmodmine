<?php
class ChristmasDays extends FWTableRow {

	public $days = array();
	
	public function __construct($year=null) {
		if ( is_null($year) ) $year = date("Y");
		
		$i = 1;
		while ( $i < 25 ) {
			$day = new ChristmasDay($year, $i);
			array_push($this->days, $day);
			$i++;
		}
	}
	
	public static function assignToday($year=null, $month=null, $day=null) {
		if ( is_null($day) ) $day = date("j");
		if ( is_null($month) ) $month = date("n");
		if ( is_null($year) ) $year = date("Y");
		
		if ( $month == 12 && $day > 0 && $day < 25 && ChristmasDay::exists($year, $day) ) {
			$participants = ChristmasParticipant::getAllUnblocked();
			if ( count($participants) == 0 ) {
				ChristmasParticipant::unblockAll();
				$participants = ChristmasParticipant::getAllUnblocked();
			}
			
			$maxRand = count($participants)-1;
			$randomIndex = rand(0, $maxRand);
			
			$christmasDay = new ChristmasDay($year, $day);
			if ( $christmasDay->isAssigned() ) return false;
			$christmasDay->first_name = $participants[$randomIndex]->first_name;
			$christmasDay->last_name = $participants[$randomIndex]->last_name;
			$christmasDay->email = $participants[$randomIndex]->email;
			$christmasDay->update();
			
			$participants[$randomIndex]->block();
			
			// sending notification mail
			$subject = "Tag ".$day." des IWi-Adventskalenders gehoert dir!";
			$msg = "Hallo ".$christmasDay->first_name.", \n\nGlueckwunsch! Tag ".$day." des IWi-Adventskalenders gehoert dir. Viel Spass beim Leeren der Socke. \n\nFrohe Weihnachten!\nDer Weihnachtsmann";
			$from = "IWi-Adventskalender <weihnachtsmann@iwi.dfki.de>";
			EMailNotifyer::send($christmasDay->email, $subject, $msg, $from);
			EMailNotifyer::send(Config::ADMIN_E_MAIL, $subject, $msg, $from);
			
			return true;
		}
		
		EMailNotifyer::send(Config::ADMIN_E_MAIL, "Adventskalender CronJob", "Keine Zuordnung, Job laeuft aber!");

		return false;
	}
	
	public static function getTodaysAssignment($year=null, $month=null, $day=null) {
		if ( is_null($day) ) $day = date("j");
		if ( is_null($month) ) $month = date("n");
		if ( is_null($year) ) $year = date("Y");
		
		if ( $month == 12 && $day > 0 && $day < 25 && ChristmasDay::exists($year, $day) ) {
			$christmasDay = new ChristmasDay($year, $day);
			if ( !is_null($christmasDay->email) ) return $christmasDay;
		}
		
		return null;
	}
	
}
?>