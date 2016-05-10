<?php
class ChristmasParticipant extends FWTableRow {

	public function __construct($id=null) {
		parent::__construct('christmas_participants');
		if ( !is_null($id) ) $this->load($id);
	}
	
	public function block() {
		$this->blocked = true;
		$this->update();
	}
	
	public static function exists($email, $year=null) {
		if ( is_null($year) ) $year = date("Y");
		$checkmails = array();
		$email = strtolower($email);
		array_push($checkmails, $email);
		$prefix = substr($email, 0, (strlen($email)-12));
		$nameParts = explode(".", $prefix);
		if ( count($nameParts) > 1 ) {
			array_push($checkmails, $nameParts[1].".".$nameParts[0]."@iwi.dfki.de");
			array_push($checkmails, $nameParts[0]."@iwi.dfki.de");
			array_push($checkmails, $nameParts[1]."@iwi.dfki.de");
			$inPart = implode("','", $checkmails);
			$inPart = "'".$inPart."'";
			
			$sql = "SELECT count(*) FROM christmas_participants WHERE _year='".$year."' AND email IN (".$inPart.");";
			$res = FWDatabase::query($sql);
			$row = $res->fetchRow();
			if ( $row[0] > 0 ) return true;
		} else {
			
			$sql = "SELECT count(*) FROM christmas_participants WHERE _year='".$year."' AND (email LIKE '%.".$prefix."@iwi.dfki.de' OR email LIKE '".$prefix.".%@iwi.dfki.de' OR email='".$email."');";
			$res = FWDatabase::query($sql);
			$row = $res->fetchRow();
			if ( $row[0] > 0 ) return true;
		}
				
		return false;
	}
	
	public static function checkNewIWiParticipantPlausibility($first_name, $last_name, $email) {
		$first_name = trim(strtolower($first_name));
		$last_name = trim(strtolower($last_name));
		$email = trim(strtolower($email));
	
		$prefix = substr($email, 0, (strlen($email)-12));
		$nameParts = explode(".", $prefix);
		
		if ( count($nameParts) > 1 ) {
			if ( substr_count($nameParts[0], $first_name) > 0 ) return true;
			if ( substr_count($nameParts[0], $last_name) > 0 ) return true;
			if ( substr_count($nameParts[1], $first_name) > 0 ) return true;
			if ( substr_count($nameParts[1], $last_name) > 0 ) return true;
		} else {
			if ( substr_count($nameParts[0], $first_name) > 0 ) return true;
			if ( substr_count($nameParts[0], $last_name) > 0 ) return true;
		}
		
		return false;
	}
	
	public static function getAll($year=null) {
		if ( is_null($year) ) $year = date("Y");
		$sql = "SELECT id FROM christmas_participants WHERE _year='".$year."' ORDER BY first_name, last_name;";
		$res = FWDatabase::query($sql);
		$participants = array();
		while ( $row = $res->fetchRow() ) {
			array_push($participants, new ChristmasParticipant($row[0]));
		}
		return $participants;
	}
	
	public static function getAllUnblocked($year=null) {
		if ( is_null($year) ) $year = date("Y");
		$sql = "SELECT id FROM christmas_participants WHERE blocked=false AND _year='".$year."';";
		$res = FWDatabase::query($sql);
		$participants = array();
		while ( $row = $res->fetchRow() ) {
			array_push($participants, new ChristmasParticipant($row[0]));
		}
		return $participants;
	}
	
	public static function unblockAll($year=null) {
		if ( is_null($year) ) $year = date("Y");
		$sql = "UPDATE christmas_participants SET blocked=false WHERE _year='".$year."';";
		return FWDatabase::query($sql);
	}
	
	public static function getNumParticipants($year=null) {
		if ( is_null($year) ) $year = date("Y");
		$sql = "SELECT count(*) FROM christmas_participants WHERE _year='".$year."';";
		$res = FWDatabase::query($sql);
		$row = $res->fetchRow();
		return $row[0];
	}

}
?>