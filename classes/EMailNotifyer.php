<?php
class EMailNotifyer {
		
	public static function sendCLIModelSimilarityNotification($to, $readme) {
		return self::send($to, "Your process model similarity matrix", $readme);
	}
	
	public static function sendCLINSCMNotification($to, $readme) {
		return self::send($to, "Your process matching results (NSCM)", $readme);
	}
	
	public static function sendCLIModelNLPTaggingNotification($to, $readme) {
		return self::send($to, "NLP-Tagging finished", $readme);
	}
	
	public static function sendCLIModelLabelExtractionNotification($to, $readme) {
		return self::send($to, "Label extraction finished", $readme);
	}
	
	public static function send($to, $subject, $message, $from = null) {
		if ( is_null($from) ) $from = "RefMod-Miner as a Service <".Config::NO_REPLY_MAIL.">";
		$header = 'From: '.$from.'' . "\r\n" .
				'Reply-To: '.$from.'' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
		
		return mail($to, $subject, $message, $header);
	}
	
	public static function sendCLIExternalExecutionNotification($to, $readme, $description) {
		return self::send($to, "Action finished: ".$description, $readme);
	}
	
	public static function sendWorkspaceShareLink($to, $msg) {
		$sid = session_id();
		$shareLink = Config::WEB_PATH."index.php?sid=".$sid."&site=workspace";
		$msg = "Someone shared his RMMaaS-Workspace with you: \n\n".$shareLink."\n\n".$msg;
		return self::send($to, "RMMaaS-Workspace shared", $msg); 
	}
	
	
}
?>