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
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->email = empty($_SESSION['email']) ? null : $_SESSION['email'];
		$this->action = $action;
		$this->command = $command;
		$this->session_id = $session_id;
		return $this->save();		
	}
	
	public function setEndPot() {
		$this->end_pot = date(Config::DB_DATETIME_FORMAT);
		$this->duration_sec = strtotime($this->end_pot)-strtotime($this->start_pot);
		return $this->update();
	}

}
?>