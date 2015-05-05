<?php
class EMailNotifyer {
		
	public static function sendCLIModelSimilarityNotification($to, $readme) {
		return self::send($to, "Your process model similarity matrix", $readme);
	}
	
	public static function send($to, $subject, $message, $from = "RefMod-Miner as a Service <no-reply@rmm.iwi.uni-sb.de>") {
		$header = 'From: '.$from.'' . "\r\n" .
				'Reply-To: '.$from.'' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
		
		return mail($to, $subject, $message, $header);
	}
	
}
?>