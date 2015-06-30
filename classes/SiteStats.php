<?php
class SiteStats extends FWTableRow {

	public function __construct($_date=null,$ip=null) {
		parent::__construct('site_stats');
	}
	
	public function trackSiteVisit() {
		if ( !Config::TRACK_SITE_VISITS ) return false;
		$this->_date = date(Config::DB_DATE_FORMAT);
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->site = isset($_REQUEST['site']) ? $_REQUEST['site'] : "home";
		$this->load($this->_date, $this->ip, $this->site);
		if ( $this->isPositioned ) {
			if ( is_null($this->email) && !empty($_SESSION['email']) ) {
				$this->updateEmailForDayIP($this->_date, $this->ip, $_SESSION['email']);
			}
			$this->clicks++;
		} else {
			$this->clicks = 1;
		}
		$this->email = empty($_SESSION['email']) ? null : $_SESSION['email'];
		return $this->save();
		
	}
	
	private function updateEmailForDayIP($day, $ip, $email) {
		$sql = "UPDATE stats SET email='".$email."' WHERE _date='".$day."' AND ip='".$ip."'";
		$affected =& FWDatabase::exec($sql);
		if ( PEAR::isError($affected) ) {
			Logger::log($_SESSION['email'], 'Stats->updateEmailForDayIP failed: '.$sql." - Message: ".$affected->getMessage(), "ERROR");
			return false;
		}
		return true;
	}

}
?>