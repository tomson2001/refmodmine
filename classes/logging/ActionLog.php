<?php
class ActionLog extends FWTableRow {

	public function __construct($id=null) {
		parent::__construct('action_log');
		if ( !is_null($id) ) $this->load($id);
	}
	
	public function trackAction($action, $command, $session_id, $checksum) {
		if ( !Config::LOG_ACTIONS ) return false;
		$this->id = $checksum;
		$this->start_pot = date(Config::DB_DATETIME_FORMAT);
		$this->ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "0.0.0.0";
		$this->email = (isset($_SESSION['email']) && !empty($_SESSION['email'])) ? $_SESSION['email'] : "n/a";
		$this->action = $action;
		$this->command = $command;
		$this->session_id = !is_null($session_id) ? $session_id : $checksum;
		return $this->save();		
	}
	
	public function setEndPot() {
		$this->end_pot = date(Config::DB_DATETIME_FORMAT);
		$this->duration_sec = strtotime($this->end_pot)-strtotime($this->start_pot);
		return $this->update();
	}

}
?>