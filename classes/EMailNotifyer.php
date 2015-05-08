<?php
class EMailNotifyer {
		
	public static function sendCLIModelSimilarityNotification($to, $readme) {
		return self::send($to, "Your process model similarity matrix", $readme);
	}
	
	public static function sendCLIModelNLPTaggingNotification($to, $readme) {
		return self::send($to, "NLP-Tagging finished", $readme);
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
	
	
	
}
?>