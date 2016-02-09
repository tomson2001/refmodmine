<?php
class ChristmasDay extends FWTableRow {

	public function __construct($year, $day) {
		parent::__construct('christmas_days');
		$this->load($year, $day);
	}
	
	public function setParticipant($first_name, $last_name, $email) {
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->email = $email;
		return $this->update();		
	}
	
	public function isAssigned() {
		if ( is_null($this->email) ) return false;
		return true;
	}
	
	public static function exists($year, $day) {
		$sql = "SELECT count(*) FROM christmas_days WHERE _year='".$year."' AND _day='".$day."';";
		$res = FWDatabase::query($sql);
		$row = $res->fetchRow();
		if ( $row[0] == 1 ) return true;
		return false;
	}

}
?>