<?php
class EMailNotifyer {
		
	public static function sendCLIModelSimilarityNotification($to, $readme) {
		return self::send($to, "Your process model similarity matrix", $readme);
	}
	
	public static function sendCLINSCMNotification($to, $readme) {
		return self::send($to, "Your process matching results (NSCM)", $readme);
	}
	
	public static function sendCLIEPMLSplitMNotification($to, $readme) {
		return self::send($to, "EPML successfully splitted", $readme);
	}
	
	public static function sendCLIModelNLPTaggingNotification($to, $readme) {
		return self::send($to, "NLP-Tagging finished", $readme);
	}
	
	public static function sendCLIModelLabelExtractionNotification($to, $readme) {
		return self::send($to, "Label extraction finished", $readme);
	}
	
	public static function sendCLILogFeatureVectorsNotification($to, $readme) {
		return self::send($to, "Feature Vector extraction (Log) finished", $readme);
	}
	
	public static function sendCLICorrelationCalculatorNotification($to, $readme) {
		return self::send($to, "Correlation calculation finished", $readme);
	}
	
	public static function sendCLIModelEventRemoverNotification($to, $readme) {
		return self::send($to, "Removing trivial events finished", $readme);
	}
	
	public static function sendCLIConvertPNML2EPMLNotification($to, $readme) {
		return self::send($to, "PNML2EPML conversion finished", $readme);
	}
	
	public static function sendCLIConvertEPML2PNMLNotification($to, $readme) {
		return self::send($to, "EPML2PNML conversion finished", $readme);
	}
	
	public static function sendCLIConvertBPMN2EPMLNotification($to, $readme) {
		return self::send($to, "BPMN2EPML conversion finished", $readme);
	}
	
	public static function sendCLIConvertEPML2BPMNNotification($to, $readme) {
		return self::send($to, "EPML2BPMN conversion finished", $readme);
	}
	
	public static function sendCLIModelTranslationNotification($to, $readme) {
		return self::send($to, "Model translation finished", $readme);
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
	
	public static function sendAdminNotificationModelsUploaded($filename) {
		if ( !is_null(Config::DO_NOTIFY_ON_MODEL_UPLOADS) ) {
			return self::send(Config::DO_NOTIFY_ON_MODEL_UPLOADS, "New models uploaded", $filename);
		}
	}
	
	public static function sendAdminNotificationModelsAddedToGlobalRepository($filename) {
		if ( !is_null(Config::DO_NOTIFY_ON_MODEL_ADDED_TO_GLOBAL_RESPOSITORY) ) {
			return self::send(Config::DO_NOTIFY_ON_MODEL_ADDED_TO_GLOBAL_RESPOSITORY, "User added models to global respository", $filename);
		}
	}
	
}
?>