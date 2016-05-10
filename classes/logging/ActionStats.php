<?php
class ActionStats extends FWTableRow {

	public function __construct($_date=null,$ip=null) {
		parent::__construct('action_stats');
	}
	
	public function trackAction($action, $session_id) {
		if ( !Config::TRACK_ACTIONS ) return false;
		$this->_date = date(Config::DB_DATE_FORMAT);
		$this->pot = date(Config::DB_DATETIME_FORMAT);
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->email = empty($_SESSION['email']) ? null : $_SESSION['email'];
		$this->action = $action;
		$this->session_id = $session_id;
		return $this->save();		
	}

}
?>