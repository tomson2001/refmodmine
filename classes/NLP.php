<?php
class NLP {
	
	public static function tag($text, $pos_tagger_model = Config::STANFORD_POS_TAGGER_MODEL) {
		$tagger = new StanfordPOSTagger(Config::STANDFORD_POS_TAGGER_PATH);
		$tagger->set_model($pos_tagger_model);
		$taggedText = $tagger->array_tag(str_replace(" ", ", ", $text));
		$taggedTokens = array();
		
		$forbiddenTags = array(",", "POS", "-LRB-", "-RRB-");
		
		// remove unused tokens
		foreach ( $taggedText[0]["tagged"] as $token ) {
			if ( !in_array($token["tag"], $forbiddenTags) ) array_push($taggedTokens, array("token" => $token["token"], "tag" => $token["tag"]));
		}
		
		// if there are two token, whereby the second is a Noun (N, NN)
		if ( count($taggedTokens) == 2 && in_array($taggedTokens[1]["tag"], array("N", "NN")) ) {
			if ( WordNet::isVerb($taggedTokens[0]["token"]) ) $taggedTokens[0]["tag"] = "VB";
		}
		
		// if there is no A in the text, check whether one of them could be a VB
		$hasVerb = false;
		foreach ( $taggedTokens as $taggedToken ) {
			if ( in_array($taggedToken["tag"], array("VB", "VBD", "VBG", "VBN", "VBP", "VBZ")) ) $hasVerb = true;
		}
		if ( !$hasVerb ) {
			foreach ( $taggedTokens as $index => $taggedToken ) {
				if ( WordNet::isVerb($taggedToken["token"]) ) {
					$taggedTokens[$index]["tag"] = "VB";
					//print ("\n".$taggedToken["token"]." is verb \n");
					break;
				}
				
				$extToken = new WordNet($taggedToken["token"]);
				$extToken = $extToken->getVerbConversionsOfNoun();
				if ( !empty($extToken[0]) ) {
					//print ("\n".$taggedToken["token"]." (noun) changed to ".$extToken[0]." (verb)\n");
					$taggedTokens[$index]["token"] = ltrim(rtrim($extToken[0]));
					$taggedTokens[$index]["tag"] = "VB";
					break;
				}
			}
		}
		
		
		
		// if there is only one token which is not A
		if ( count($taggedTokens) == 1 && !in_array($taggedTokens[0]["tag"], array("VB", "VBD", "VBG", "VBN", "VBP", "VBZ")) ) {
			if ( WordNet::isVerb($taggedTokens[0]["token"]) ) {
				$taggedTokens[0]["tag"] = "VB";
			} else {
				$extToken = new WordNet($taggedTokens[0]["token"]);
				$extToken = $extToken->getVerbConversionsOfNoun();
				if ( !empty($extToken[0]) ) {
					$taggedTokens[0]["token"] = ltrim(rtrim($extToken[0]));
					$taggedTokens[0]["tag"] = "VB";
				}
			}
		}
		
		// if there are two VB, the second might be a noun
		$onlyVerbs = true;
		foreach ( $taggedTokens as $taggedToken ) {
			if ( !in_array($taggedToken["tag"], array("VB", "VBD", "VBG", "VBN", "VBP", "VBZ")) ) $onlyVerbs = false;
		}
		if ( $onlyVerbs && count($taggedTokens) == 2 ) $taggedTokens[1]["tag"] = "NN";
		
		// if there is no NN in the text, check whether one of them could be a VB
		$hasNoun = false;
		foreach ( $taggedTokens as $taggedToken ) {
			if ( in_array($taggedToken["tag"], array("NN", "NNS", "NNP", "NNPS")) ) $hasNoun = true;
		}
		if ( !$hasNoun ) {
			foreach ( $taggedTokens as $index => $taggedToken ) {
				if ( WordNet::isNoun($taggedToken["token"]) ) {
					$taggedTokens[$index]["tag"] = "NN";
					break;
				}
			}
		}
		
		
		return $taggedTokens;
	}
	
	/**
	 * @param array $taggedTokens 	the output of the function NLP::tag
	 * @param string $language		EN | DE
	 */
	public static function extendTaggedTextWithHighLevelTags($taggedTokens, $language = "EN") {
		$tagTransformator = null;
		switch ( $language ) {
			case "DE":  $tagTransformator = new NLPHighLevelTagTransformatorDE(); break;
			default: 	$tagTransformator = new NLPHighLevelTagTransformatorEN();
		}
		$newTaggedTokens = array();
		foreach ( $taggedTokens as $token ) {
			$highLevelTag = $tagTransformator->transformTagToHighLevelTag($token["tag"]);
			array_push($newTaggedTokens, array("token" => $token["token"], "tag" => $token["tag"], "high_level_tag" => $highLevelTag));
		}
		return $newTaggedTokens;
	}
	
	/**
	 * tries to correct the tags of function labels in order to be able to detect a label style
	 * @param unknown $extendedTaggedTokens
	 */
	public static function tryToCorrectHighLevelTagsForFunctionLabels($extendedTaggedTokens) {
		
		// If there is only one token which is a Noun (N) , check, whether it could be an Action Noun
		if ( count($extendedTaggedTokens) == 1 && $extendedTaggedTokens["tag"] = "N" ) {
			
		}
		
	}
	
	public static function hasVerbs($taggedTokens) {
		foreach ( $taggedTokens as $token ) {
			if ( WordNet::isVerb($token["token"]) ) return true; 
		}
		return false;
	}
	
	/**
	 * tried to detect the label style based on the extended tagged text (result of extendTaggedTextWithHighLevelTags)
	 * 
	 * @param unknown $extendedTaggedTokens
	 * @param string $language
	 */
	public static function getLabelStyle($extendedTaggedTokens, $language = "EN") {
		$styleVerifier = null;
		switch ( $language ) {
			case "DE":  $styleVerifier = new NLPLableStyleVerifierDE(); break;
			default: 	$styleVerifier = new NLPLableStyleVerifierEN();
		}
		$highLevelTagString = self::getHighLevelTagSetString($extendedTaggedTokens);
		return $styleVerifier->getLableStyleKey($highLevelTagString);
	}
	
	private static function getHighLevelTagSetString($extendedTaggedTokens) {
		$highLevelTagString = "";
		foreach ( $extendedTaggedTokens as $token ) {
			if ( !empty($token["high_level_tag"]) ) $highLevelTagString .= "{".$token["high_level_tag"]."}";
		}
		return $highLevelTagString;
	}
	
	/**
	 *
	 * @param unknown $extendedTaggedLabel the result of NLP::extendTaggedTextWithHighLevelTags
	 * @param unknown $tagSetString
	 */
	public static function getLabelSubject($extendedTaggedLabel, $language = "EN") {
		$verifier = self::getStyleVerifier($language);
		$tagSetString = self::getHighLevelTagSetString($extendedTaggedLabel);
		return $verifier->getLabelPart("subject", $extendedTaggedLabel, $tagSetString);
	}
	
	private static function getLabelVerbLive($extendedTaggedLabel, $language = "EN") {
		$verifier = self::getStyleVerifier($language);
		$tagSetString = self::getHighLevelTagSetString($extendedTaggedLabel);
		return $verifier->getLabelPart("verb", $extendedTaggedLabel, $tagSetString);
	}
	
	private static function getLabelObjectLive($extendedTaggedLabel, $language = "EN") {
		$verifier = self::getStyleVerifier($language);
		$tagSetString = self::getHighLevelTagSetString($extendedTaggedLabel);
		return $verifier->getLabelPart("object", $extendedTaggedLabel, $tagSetString);
	}
	
	private static function getLabelToNounLive($extendedTaggedLabel, $language = "EN") {
		$verifier = self::getStyleVerifier($language);
		$tagSetString = self::getHighLevelTagSetString($extendedTaggedLabel);
		return $verifier->getLabelPart("toNoun", $extendedTaggedLabel, $tagSetString);
	}
	
	public static function getLabelVerb($label, $persistedLabelElements = null) {
		$labelElements = is_null($persistedLabelElements) ? self::loadLabelElementsFromPersistedFile() : $persistedLabelElements;
		if ( isset($labelElements[$label]) ) return $labelElements[$label]["verb"];
		
		$tagged = self::tag($label);
		$tagged = self::extendTaggedTextWithHighLevelTags($tagged);
		$verb = self::getLabelVerbLive($tagged);
		$object = self::getLabelObjectLive($tagged);
		self::persistLabelElements($label, $verb, $object);
		return $verb;
	}
	
	public static function getLabelObject($label, $persistedLabelElements = null) {
		$labelElements = is_null($persistedLabelElements) ? self::loadLabelElementsFromPersistedFile() : $persistedLabelElements;
		if ( isset($labelElements[$label]) ) return $labelElements[$label]["object"];
	
		$tagged = self::tag($label);
		$tagged = self::extendTaggedTextWithHighLevelTags($tagged);
		$verb = self::getLabelVerbLive($tagged);
		$object = self::getLabelObjectLive($tagged);
		self::persistLabelElements($label, $verb, $object);
		return $object;
	}
	
	public static function getLabelToNoun($label, $persistedLabelElements = null) {
		$labelElements = is_null($persistedLabelElements) ? self::loadLabelElementsFromPersistedFile() : $persistedLabelElements;
		if ( isset($labelElements[$label]) ) return $labelElements[$label]["toNoun"];
	
		$tagged = self::tag($label);
		$tagged = self::extendTaggedTextWithHighLevelTags($tagged);
		$verb = self::getLabelVerbLive($tagged);
		$object = self::getLabelObjectLive($tagged);
		$toNoun = self::getLabelToNounLive($tagged);
		self::persistLabelElements($label, $verb, $object, $toNoun);
		return $object;
	}
	
	private static function getStyleVerifier($language) {
		switch ( $language ) {
			case "DE":  return new NLPLableStyleVerifierDE();
			default: 	return new NLPLableStyleVerifierEN();
		}
	}
	
	/**
	 * checks whether there is a correspondency between two labels based on their verbs and objects
	 * 
	 * @param string $label1
	 * @param string $label2
	 * 
	 * @return boolean
	 */
	public static function checkVerbObjectCorrespondencyForTwoLabels($label1, $label2, $correspondencyCache=null, $nonCorrespondencyCache=null, $matchingCorrespondencyCache=null, $matchingNonCorrespondencyCache=null, $persistedLabelElements=null) {
		
		$correspondencyCache = is_null($correspondencyCache) ? self::loadCorrespondentLabelsFromPersistedFile() : $correspondencyCache;
		foreach ( $correspondencyCache as $pair ) {
			if ( $pair["label1"] == $label1 && $pair["label2"] == $label2 ) return true;
			if ( $pair["label1"] == $label2 && $pair["label2"] == $label1 ) return true;
		}
		
		$nonCorrespondencyCache = is_null($nonCorrespondencyCache) ? self::loadNonCorrespondentLabelsFromPersistedFile() : $nonCorrespondencyCache;
		foreach ( $nonCorrespondencyCache as $pair ) {
			if ( $pair["label1"] == $label1 && $pair["label2"] == $label2 ) return false;
			if ( $pair["label1"] == $label2 && $pair["label2"] == $label1 ) return false;
		}
		
 		//$tags1 = NLP::tag($label1);
 		//$extTags1 = NLP::extendTaggedTextWithHighLevelTags($tags1);
 		$verb1 = NLP::getLabelVerb($label1);
 		$object1 = NLP::getLabelObject($label1);
			
 		//$tags2 = NLP::tag($label2);
 		//$extTags2 = NLP::extendTaggedTextWithHighLevelTags($tags2);
 		$verb2 = NLP::getLabelVerb($label2);
 		$object2 = NLP::getLabelObject($label2);
 		
 		//print($verb1." - ".$object1.", ".$verb2." - ".$object2."\n");
 		
 		if ( !is_null($verb1) && !is_null($verb2) ) {
 			if ( (is_null($object1) && !is_null($object2)) || (!is_null($object1) && is_null($object2)) ) {
 				self::persistNonCorrespondentLabelPair($label1, $label2);
 				return false;
 			}
 		}
			
 		if ( !is_null($verb1) && !is_null($verb2) && !is_null($object1) && !is_null($object2) ) {
			
 			$verbSyns = WordNet::checkIfVerbsAreSynonym($verb1, $verb2);
			
 			$objectSyns = NLP::areSynonymNouns($object1, $object2);
 			if ( !$objectSyns ) {
 				$lObject1 = strtolower($object1);
 				$lObject2 = strtolower($object2);
 				if ( substr_count($lObject1, $lObject2) > 0 || substr_count($lObject2, $lObject1) > 0 ) $objectSyns = true;
 			}
			
 			$doCorrespond = $verbSyns || $objectSyns;
			
 			if ( $doCorrespond ) {
 				$toNoun1 = NLP::getLabelToNoun($label1);
 				$toNoun2 = NLP::getLabelToNoun($label2);
 				if ( !is_null($toNoun1) && !is_null($toNoun2) ) {
 					if ( !NLP::areSynonymNouns($toNoun1, $toNoun2) ) {
 						self::persistNonCorrespondentLabelPair($label1, $label2);
 						return false;
 					}
 				}
				
 			} else {
 				self::persistNonCorrespondentLabelPair($label1, $label2);
 				return false;
 			}
 		}
		
 		self::persistCorrespondentLabelPair($label1, $label2);
 		return true;
		
		
// 		$doCorrespond = self::checkVerbObjectCorrespondencyForTwoLabelsForMatching($label1, $label2);
// 		if ( $doCorrespond ) {
// 			$toNoun1 = NLP::getLabelToNoun($label1);
// 			$toNoun2 = NLP::getLabelToNoun($label2);
// 			if ( !is_null($toNoun1) && !is_null($toNoun2) ) {
// 				if ( NLP::areSynonymNouns($toNoun1, $toNoun2) ) {
// 					self::persistCorrespondentLabelPair($label1, $label2);
// 					return true;
// 				} else {
// 					self::persistNonCorrespondentLabelPair($label1, $label2);
// 					return false;
// 				}
// 			} else {
// 				self::persistCorrespondentLabelPair($label1, $label2);
// 				return true;
// 			}
// 		} else {
// 			self::persistNonCorrespondentLabelPair($label1, $label2);
// 			return false;
// 		}
	}
	
	public static function checkVerbObjectCorrespondencyForTwoLabelsForMatching($label1, $label2, &$persistedLabelElements=null) {
// 		$correspondencyCache = is_null($matchingCorrespondencyCache) ? self::loadMatchingCorrespondentLabelsFromPersistedFile() : $matchingCorrespondencyCache;
// 		foreach ( $correspondencyCache as $pair ) {
// 			if ( $pair["label1"] == $label1 && $pair["label2"] == $label2 ) return true; 
// 			if ( $pair["label1"] == $label2 && $pair["label2"] == $label1 ) return true;

// 		}
		
// 		$nonCorrespondencyCache = is_null($matchingNonCorrespondencyCache) ? self::loadMatchingNonCorrespondentLabelsFromPersistedFile() : $matchingNonCorrespondencyCache;
// 		foreach ( $nonCorrespondencyCache as $pair ) {
// 			if ( $pair["label1"] == $label1 && $pair["label2"] == $label2 ) return false;
// 			if ( $pair["label1"] == $label2 && $pair["label2"] == $label1 ) return false;
// 		}
		
		$verb1 = NLP::getLabelVerb($label1, $persistedLabelElements);
		$persistedLabelElements = self::loadLabelElementsFromPersistedFile();
		$object1 = NLP::getLabelObject($label1, $persistedLabelElements);
		
		$verb2 = NLP::getLabelVerb($label2, $persistedLabelElements);
		$persistedLabelElements = self::loadLabelElementsFromPersistedFile();
		$object2 = NLP::getLabelObject($label2, $persistedLabelElements);

		if ( (is_null($verb1) && !is_null($verb2)) 
			|| (!is_null($verb1) && is_null($verb2))
			|| (is_null($object1) && !is_null($object2))
			|| (!is_null($object1) && is_null($object2)) 
			|| (is_null($verb1) && is_null($verb2) && is_null($object1) && is_null($object2)) ) 
		{
// 			self::persistMatchingNonCorrespondentLabelPair($label1, $label2);
			return false;
		}
		
		// Check for synonym verb and object
		
		// First case: only verbs are available
		if ( !is_null($verb1) && !is_null($verb2) && is_null($object1) && is_null($object2) ) {
// 			$verbSyn = NLP::areSynonymVerbs($verb1, $verb2);
// 			if ( !$verbSyn ) {
//  				self::persistMatchingNonCorrespondentLabelPair($label1, $label2);
// 				return false;
// 			}
// 			self::persistMatchingNonCorrespondentLabelPair($label1, $label2);
			return false;
		}
		
		// Second case: only object are available
		if ( is_null($verb1) && is_null($verb2) && !is_null($object1) && !is_null($object2) ) {
// 			$objectSyn = NLP::areSynonymNouns($object1, $object2);
// 			if ( !$objectSyn ) {
// 				self::persistMatchingNonCorrespondentLabelPair($label1, $label2);
// 				return false;
// 			}
// 			self::persistMatchingNonCorrespondentLabelPair($label1, $label2);
			return false;
		}
		
		// Third case: verbs and object are available
		if ( !is_null($verb1) && !is_null($verb2) && !is_null($object1) && !is_null($object2) ) {
			$verbSyn = NLP::areSynonymVerbs ( $verb1, $verb2 );
			$objectSyn = NLP::areSynonymNouns ( $object1, $object2 );
			if ( !$verbSyn || !$objectSyn ) {
// 				self::persistMatchingNonCorrespondentLabelPair($label1, $label2);
				return false;
			}
		}
		
		//print("\n   V1: ".$verb1.", O1: ".$object1.", V2: ".$verb2.", O2: ".$object2."\n");
		
 		self::persistMatchingCorrespondentLabelPair($label1, $label2);
		return true;
	}
	
	public static function loadCorrespondentLabelsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Correspondent_Labels.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Correspondent_Labels.csv", "r");
		$correspondentLablePairs = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label1 = $data[0];
			$label2 = $data[1];
			array_push($correspondentLablePairs, array("label1" => $label1, "label2" => $label2));
		}
		fclose($fileHandler);
		return $correspondentLablePairs;
	}
	
	public static function loadNonCorrespondentLabelsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Non_Correspondent_Labels.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Non_Correspondent_Labels.csv", "r");
		$nonCorrespondentLablePairs = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label1 = $data[0];
			$label2 = $data[1];
			array_push($nonCorrespondentLablePairs, array("label1" => $label1, "label2" => $label2));
		}
		fclose($fileHandler);
		return $nonCorrespondentLablePairs;
	}
	

	private static function persistCorrespondentLabelPair($label1, $label2) {
		$txt = $label1."|".$label2."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Correspondent_Labels.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
	private static function persistNonCorrespondentLabelPair($label1, $label2) {
		$txt = $label1."|".$label2."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Non_Correspondent_Labels.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
	public static function loadMatchingCorrespondentLabelsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Matching_Correspondent_Labels.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Matching_Correspondent_Labels.csv", "r");
		$correspondentLablePairs = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label1 = $data[0];
			$label2 = $data[1];
			array_push($correspondentLablePairs, array("label1" => $label1, "label2" => $label2));
		}
		fclose($fileHandler);
		return $correspondentLablePairs;
	}
	
	public static function loadMatchingNonCorrespondentLabelsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Matching_Non_Correspondent_Labels.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Matching_Non_Correspondent_Labels.csv", "r");
		$nonCorrespondentLablePairs = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label1 = $data[0];
			$label2 = $data[1];
			array_push($nonCorrespondentLablePairs, array("label1" => $label1, "label2" => $label2));
		}
		fclose($fileHandler);
		return $nonCorrespondentLablePairs;
	}
	
	
	private static function persistMatchingCorrespondentLabelPair($label1, $label2) {
		$txt = $label1."|".$label2."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Matching_Correspondent_Labels.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
	private static function persistMatchingNonCorrespondentLabelPair($label1, $label2) {
		$txt = $label1."|".$label2."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Matching_Non_Correspondent_Labels.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
	public static function loadLabelElementsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Label_Elements.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Label_Elements.csv", "r");
		$labels = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label = $data[0];
			$verb = $data[1] == "null" ? null : $data[1];
			$object = $data[2] == "null" ? null : $data[2];
			$toNoun = $data[3] == "null" ? null : $data[3];
			$labels[$label] = array("verb" => $verb, "object" => $object, "toNoun" => $toNoun);
		}
		fclose($fileHandler);
		return $labels;
	}
	
	
	private static function persistLabelElements($label, $verb, $object, $toNoun=null) {
		$verb = is_null($verb) ? "null" : $verb;
		$object = is_null($object) ? "null" : $object;
		$toNoun = is_null($toNoun) ? "null" : $toNoun;
		$txt = $label."|".$verb."|".$object."|".$toNoun."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Label_Elements.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
	public static function getUnprocessibleLabels($doPrintToConsole=false) {
		if ( $doPrintToConsole) print("\nGetting unprocessible label elements...");
		$labelElements = self::loadLabelElementsFromPersistedFile();
		foreach ( $labelElements as $label => $elements ) {
			if ( !(is_null($elements["verb"]) && is_null($elements["object"])) ) {
				unset($labelElements[$label]);
			}
		}
		if ( $doPrintToConsole) {
			print("done\n");
			foreach ( $labelElements as $label => $elements ) {
				print("\n   ".$label);
			}
			print("\n\n");
		}
		return $labelElements;
	}
	
	public static function getAnnotatedUnprocessibleLabels() {
		print("\nGetting and annotating unprocessible label elements...");
		$labelElements = self::getUnprocessibleLabels();
		foreach ( $labelElements as $label => $elements ) {
			$tagged = self::tag($label);
			$tagged = self::extendTaggedTextWithHighLevelTags($tagged);
			$tags = "";
			$highLevelTags = "";
			$labelStyle = self::getLabelStyle($tagged);
			foreach ( $tagged as $token ) {
				$tags .= "{".$token["tag"]."}";
				$highLevelTags .= "{".$token["high_level_tag"]."}";
			}
			print $output = "\n  ".$label." - Tag-Set: ".$tags." - High-Level-Tag-Set: ".$highLevelTags." - Label style: ".$labelStyle;
		}
		print("\ndone\n");
	}
	
	public static function removeDuplicatedFromPersistedLabelElements() {
		$labelElements = self::loadLabelElementsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Label_Elements.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Label_Elements.csv");
		foreach ( $labelElements as $label => $elements ) {
			self::persistLabelElements($label, $elements["verb"], $elements["object"], $elements["toNoun"]);
		}
		print("\ndone\n");
	}
	
	/**
	 * Removes all undefined labels from the persisted file
	 */
	public static function cleanUpPersistedFiles() {
		$labelElements = self::loadLabelElementsFromPersistedFile();
		$affectedLabels = array();
		print("\n\nClean up undefined persisted label elements...");
		foreach ( $labelElements as $label => $elements ) {
			if ( is_null($elements["verb"]) && is_null($elements["object"]) ) {
				unset($labelElements[$label]);
				array_push($affectedLabels, $label);
				print("\n   \"".$label."\" removed");
			}
		}
		print("\ndone");
		
		print("\n\nUpdating files...");
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Label_Elements.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Label_Elements.csv");
		foreach ( $labelElements as $label => $elements ) {
			self::persistLabelElements($label, $elements["verb"], $elements["object"], $elements["toNoun"]);
		}
		
		$pairs = self::loadAntonymLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Antonym_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Antonym_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) ) 
				self::persistAntonymLabelPair($pair["label1"], $pair["label2"]);
		}
		
		$pairs = self::loadNonAntonymLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Non_Antonym_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Non_Antonym_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistNonAntonymLabelPair($pair["label1"], $pair["label2"]);
		}
				
		$pairs = self::loadCorrespondentLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Correspondent_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Correspondent_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistCorrespondentLabelPair($pair["label1"], $pair["label2"]);
		}
		
		$pairs = self::loadNonCorrespondentLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Non_Correspondent_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Non_Correspondent_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistNonCorrespondentLabelPair($pair["label1"], $pair["label2"]);
		}
		
		$pairs = self::loadMatchingCorrespondentLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Matching_Correspondent_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Matching_Correspondent_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistMatchingCorrespondentLabelPair($pair["label1"], $pair["label2"]);
		}
		
		$pairs = self::loadMatchingNonCorrespondentLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Matching_Non_Correspondent_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Matching_Non_Correspondent_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistMatchingNonCorrespondentLabelPair($pair["label1"], $pair["label2"]);
		}
		print("done\n");

	}
	
	/**
	 * Removes all undefined labels from the persisted file
	 */
	public static function cleanUpPersistedFilesByLabel($delLabel) {
		$labelElements = self::loadLabelElementsFromPersistedFile();
		$affectedLabels = array($delLabel);
		if ( isset($labelElements[$delLabel]) ) unset($labelElements[$delLabel]);
		print("\n\nClean up undefined persisted label elements...");
	
		print("\n\nUpdating files...");
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Label_Elements.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Label_Elements.csv");
		foreach ( $labelElements as $label => $elements ) {
			self::persistLabelElements($label, $elements["verb"], $elements["object"], $elements["toNoun"]);
		}
	
		$pairs = self::loadAntonymLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Antonym_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Antonym_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistAntonymLabelPair($pair["label1"], $pair["label2"]);
		}
	
		$pairs = self::loadNonAntonymLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Non_Antonym_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Non_Antonym_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistNonAntonymLabelPair($pair["label1"], $pair["label2"]);
		}
	
		$pairs = self::loadCorrespondentLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Correspondent_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Correspondent_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistCorrespondentLabelPair($pair["label1"], $pair["label2"]);
		}
	
		$pairs = self::loadNonCorrespondentLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Non_Correspondent_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Non_Correspondent_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistNonCorrespondentLabelPair($pair["label1"], $pair["label2"]);
		}
	
		$pairs = self::loadMatchingCorrespondentLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Matching_Correspondent_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Matching_Correspondent_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistMatchingCorrespondentLabelPair($pair["label1"], $pair["label2"]);
		}
	
		$pairs = self::loadMatchingNonCorrespondentLabelsFromPersistedFile();
		if ( file_exists(Config::PERSISTENT_PATH."/NLP_Matching_Non_Correspondent_Labels.csv") ) unlink(Config::PERSISTENT_PATH."/NLP_Matching_Non_Correspondent_Labels.csv");
		foreach ( $pairs as $index => $pair ) {
			if ( !(in_array($pair["label1"], $affectedLabels) || in_array($pair["label2"], $affectedLabels)) )
				self::persistMatchingNonCorrespondentLabelPair($pair["label1"], $pair["label2"]);
		}
		print("done\n");
	
	}
	
	public static function removeStopWordsInArray($array) {
		// got from http://xpo6.com/list-of-english-stop-words/
		$stopwords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", 
				"along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any",
				"anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", 
				"becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", 
				"bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", 
				"done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", 
				"ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", 
				"for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", 
				"have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", 
				"however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", 
				"latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", 
				"mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", 
				"none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", 
				"others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", 
				"same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", 
				"sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", 
				"than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", 
				"thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", 
				"to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", 
				"via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", 
				"wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", 
				"with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
		
		foreach ( $array as $index => $word ) {
			if ( in_array($word, $stopwords) ) unset($array[$index]);
		}
		
		return $array;
	}
	
	public static function areSynonymVerbs($word1, $word2, $synonymCache = null, $nonSynonymCache=null) {
		
		//print("  check syn ".$word1." => ".$word2." ");
		
		$word1 = strtolower($word1);
		$word2 = strtolower($word2);
		
		if ( $word1 == $word2 ) return true;
		if ( PorterStemmer::Stem($word1) == PorterStemmer::Stem($word2) ) return true;
		
		$noSyns = array(
// 				array("word1" => "apply", "word2" => "upload"), // TODO
// 				array("word1" => "apply", "word2" => "fill"),
 				array("word1" => "assessment", "word2" => "accept"),
 				array("word1" => "assessment", "word2" => "reject"),
// 				array("word1" => "check", "word2" => "document"),
// 				array("word1" => "compare", "word2" => "fill"),
// 				array("word1" => "wait", "word2" => "act"),
// 				array("word1" => "wait", "word2" => "move"),
// 				array("word1" => "fill", "word2" => "receive"),
// 				array("word1" => "fill", "word2" => "send"),
// 				array("word1" => "receive", "word2" => "accept"),
// 				array("word1" => "receive", "word2" => "make"),
// 				array("word1" => "receive", "word2" => "start"),
// 				array("word1" => "receive", "word2" => "cancel"),
// 				array("word1" => "receive", "word2" => "precheck"),
// 				array("word1" => "receive", "word2" => "check"),
// 				array("word1" => "pay", "word2" => "talk"),
// 				array("word1" => "take", "word2" => "create"),
// 				array("word1" => "take", "word2" => "fill"),
// 				array("word1" => "receive", "word2" => "make"),
// 				array("word1" => "send", "word2" => "create"),
// 				array("word1" => "send", "word2" => "check"),
// 				array("word1" => "send", "word2" => "compare"),
// 				array("word1" => "send", "word2" => "precheck"),
// 				array("word1" => "send", "word2" => "print"),
// 				array("word1" => "send", "word2" => "take"),
// 				array("word1" => "print", "word2" => "fill"),
// 				array("word1" => "return", "word2" => "create"),
// 				array("word1" => "finalize", "word2" => "decide"),
// 				array("word1" => "send", "word2" => "go"),
// 				array("word1" => "reject", "word2" => "accept"),
// 				array("word1" => "complete", "word2" => "fill"),
// 				array("word1" => "appoint", "word2" => "create"),
// 				array("word1" => "complete", "word2" => "precheck"),
// 				array("word1" => "send", "word2" => "precheck")
		);
		
		$wordStem1 = PorterStemmer::Stem($word1);
		$wordStem2 = PorterStemmer::Stem($word2);
		foreach ( $noSyns as $pair ) {
			$pairStem1 = PorterStemmer::Stem($pair["word1"]);
			$pairStem2 = PorterStemmer::Stem($pair["word2"]);
			if ( $wordStem1 == $pairStem1 && $wordStem2 == $wordStem2 ) return false;
			if ( $wordStem1 == $wordStem2 && $wordStem2 == $pairStem1 ) return false;
		}
		
		$synonymCache = is_null($synonymCache) ? self::loadSynonymVerbsFromPersistedFile() : $synonymCache;
		foreach ( $synonymCache as $pair ) {
			if ( $pair["verb1"] == $word1 && $pair["verb2"] == $word2 ) return true;
			if ( $pair["verb1"] == $word2 && $pair["verb2"] == $word1 ) return true;
		
		}
		
		$nonSynonymCache = is_null($nonSynonymCache) ? self::loadNonSynonymVerbsFromPersistedFile() : $nonSynonymCache;
		foreach ( $nonSynonymCache as $pair ) {
			if ( $pair["verb1"] == $word1 && $pair["verb2"] == $word2 ) return false;
			if ( $pair["verb1"] == $word2 && $pair["verb2"] == $word1 ) return false;
		}
		
		$wiktionaryResult = self::areSynonymsWiktionary($word1, $word2);
		if ( $wiktionaryResult ) {
			self::persistSynonymVerbPair($word1, $word2);
			return true;
		}

		if (  substr_count($word1, " ") > 0 || substr_count($word2, " ") > 0 ) {
			self::persistNonSynonymVerbPair($word1, $word2);
			return false;
		}
		
		$wordnetResult = WordNet::checkIfVerbsAreSynonym($word1, $word2);
		if ( $wordnetResult ) {
			self::persistSynonymVerbPair($word1, $word2);
			return true;
		}
		
		self::persistNonSynonymVerbPair($word1, $word2);
		return false;
	}
	
	public static function areSynonymNouns($word1, $word2, $synonymCache = null, $nonSynonymCache=null) {
		$word1 = strtolower($word1);
		$word2 = strtolower($word2);
		
		if ( $word1 == $word2 ) return true;
		if ( PorterStemmer::Stem($word1) == PorterStemmer::Stem($word2) ) return true;
		
		$noSyns = array(
 				//array("word1" => "application", "word2" => "documents")

		);
		
		$wordStem1 = PorterStemmer::Stem($word1);
		$wordStem2 = PorterStemmer::Stem($word2);
		foreach ( $noSyns as $pair ) {
			$pairStem1 = PorterStemmer::Stem($pair["word1"]);
			$pairStem2 = PorterStemmer::Stem($pair["word2"]);
			if ( $wordStem1 == $pairStem1 && $wordStem2 == $wordStem2 ) return false;
			if ( $wordStem1 == $wordStem2 && $wordStem2 == $pairStem1 ) return false;
		}
		
		$synonymCache = is_null($synonymCache) ? self::loadSynonymNounsFromPersistedFile() : $synonymCache;
		foreach ( $synonymCache as $pair ) {
			if ( $pair["noun1"] == $word1 && $pair["noun2"] == $word2 ) return true;
			if ( $pair["noun1"] == $word2 && $pair["noun2"] == $word1 ) return true;
		
		}
		
		$nonSynonymCache = is_null($nonSynonymCache) ? self::loadNonSynonymNounsFromPersistedFile() : $nonSynonymCache;
		foreach ( $nonSynonymCache as $pair ) {
			if ( $pair["noun1"] == $word1 && $pair["noun2"] == $word2 ) return false;
			if ( $pair["noun1"] == $word2 && $pair["noun2"] == $word1 ) return false;
		}
		
		$wiktionaryResult = self::areSynonymsWiktionary($word1, $word2);
		if ( $wiktionaryResult ) {
			self::persistSynonymNounPair($word1, $word2);
			return true;
		}
		if ( substr_count($word1, " ") > 0 || substr_count($word2, " ") > 0 ) {
			$word1 = strtolower($word1);
			$word2 = strtolower($word2);
			if ( substr_count($word1, $word2) > 0 || substr_count($word2, $word1) > 0 ) {
				self::persistSynonymNounPair($word1, $word2);
				return true;
			} else {
				self::persistNonSynonymNounPair($word1, $word2);
				return false;
			}
		} else {
			$wordnetResult = WordNet::checkIfNounsAreSynonym($word1, $word2);
			if ( $wordnetResult ) {
				self::persistSynonymVerbPair($word1, $word2);
				return true;
			} else {
				self::persistNonSynonymNounPair($word1, $word2);
				return false;
			}
		}
	}
	
	public static function areAntonymVerbs($word1, $word2) {
		$wiktionaryResult = self::areAntonymsWiktionary($word1, $word2);
		if ( $wiktionaryResult ) return $wiktionaryResult;
		if (  substr_count($word1, " ") > 0 || substr_count($word2, " ") > 0 ) return false;
		return WordNet::checkIfVerbsAreAntonyms($word1, $word2);
	}
	
	public static function areAntonymNouns($word1, $word2) {
		$wiktionaryResult = self::areAntonymsWiktionary($word1, $word2);
		return $wiktionaryResult;
// 		if (  substr_count($word1, " ") > 0 || substr_count($word2, " ") > 0 ) return false;
// 		return WordNet::checkIfVerbsAreAntonyms($word1, $word2);
	}
	
	public static function areSynonymsWiktionary($word1, $word2) {
		$wiktionaryInfoWord1 = new Wiktionary($word1);
		$wiktionaryInfoWord2 = new Wiktionary($word2);
		$synIntersection = array_intersect($wiktionaryInfoWord1->synonyms, $wiktionaryInfoWord2->synonyms);
		return count($synIntersection) > 0;
	}
	
	public static function areAntonymsWiktionary($word1, $word2) {
		$wiktionaryInfoWord1 = new Wiktionary($word1);
		$wiktionaryInfoWord2 = new Wiktionary($word2);
		
		// check for ants 1
		$syns1 = $wiktionaryInfoWord1->synonyms;
		$ants2 = $wiktionaryInfoWord2->antonyms;
		$intersection1 = array_intersect($syns1, $ants2);
		if ( count($intersection1) > 0 ) return true;
		
		// check for ants 2
		$syns2 = $wiktionaryInfoWord2->synonyms;
		$ants1 = $wiktionaryInfoWord1->antonyms;
		$intersection2 = array_intersect($syns2, $ants1);
		if ( count($intersection2) > 0 ) return true;
		
		return false;
	}
	
	public static function checkIfFunctionLabelsAreAntonyms($label1, $label2, $antonymCache=null, $nonAntonymCache=null) {
		// check in the also available antonym cache
		$antonymCache = is_null($antonymCache) ? self::loadAntonymLabelsFromPersistedFile() : $antonymCache;
		foreach ( $antonymCache as $pair ) {
			if ( $pair["label1"] == $label1 && $pair["label2"] == $label2 ) return true;
			if ( $pair["label1"] == $label2 && $pair["label2"] == $label1 ) return true;
		}
	
		// check in the also available non antonym cache
		$nonAntonymCache = is_null($nonAntonymCache) ? self::loadNonAntonymLabelsFromPersistedFile() : $nonAntonymCache;
		foreach ( $nonAntonymCache as $pair ) {
			if ( $pair["label1"] == $label1 && $pair["label2"] == $label2 ) return false;
			if ( $pair["label1"] == $label2 && $pair["label2"] == $label1 ) return false;
		}
	
		//$taggedLabel1 = NLP::tag($label1);
		//$taggedLabel1 = NLP::extendTaggedTextWithHighLevelTags($taggedLabel1);
		//$labelStyle1 = NLP::getLabelStyle($taggedLabel1);
	
		//$taggedLabel2 = NLP::tag($label2);
		//$taggedLabel2 = NLP::extendTaggedTextWithHighLevelTags($taggedLabel2);
		//$labelStyle2 = NLP::getLabelStyle($taggedLabel2);
	
		//if ( !is_null($labelStyle1) && !is_null($labelStyle2) ) {
		$verb1 = NLP::getLabelVerb($label1);
		$verb2 = NLP::getLabelVerb($label2);
		$object1 = NLP::getLabelObject($label1);
		$object2 = NLP::getLabelObject($label2);
		
		$areVerbsAntonym = false;
		$areObjectsAntonym = false;
		if ( !is_null($verb1) && !is_null($verb2) ) {
			$areVerbsAntonym = WordNet::checkIfVerbsAreAntonyms($verb1, $verb2);
			if ( $areVerbsAntonym && ( is_null($object1) || is_null($object2) ) ) {
				self::persistAntonymLabelPair($label1, $label2);
				return true;
			}
			$areObjectsAntonym = self::areAntonymNouns($object1, $object2);
			
			if ( $areVerbsAntonym && $areObjectsAntonym ) {
				self::persistNonAntonymLabelPair($label1, $label2);
				return false;
			}
			
			if ( $areVerbsAntonym || $areObjectsAntonym ) {
				self::persistAntonymLabelPair($label1, $label2);
				return true;
			}
		}
		//}
	
		self::persistNonAntonymLabelPair($label1, $label2);
		return false;
	}
	
	public static function loadAntonymLabelsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Antonym_Labels.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Antonym_Labels.csv", "r");
		$antonymLablePairs = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label1 = $data[0];
			$label2 = $data[1];
			array_push($antonymLablePairs, array("label1" => $label1, "label2" => $label2));
		}
		fclose($fileHandler);
		return $antonymLablePairs;
	}
	
	public static function loadNonAntonymLabelsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Non_Antonym_Labels.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Non_Antonym_Labels.csv", "r");
		$nonAntonymLablePairs = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label1 = $data[0];
			$label2 = $data[1];
			array_push($nonAntonymLablePairs, array("label1" => $label1, "label2" => $label2));
		}
		fclose($fileHandler);
		return $nonAntonymLablePairs;
	}
	
	private static function persistAntonymLabelPair($label1, $label2) {
		$txt = $label1."|".$label2."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Antonym_Labels.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
	private static function persistNonAntonymLabelPair($label1, $label2) {
		$txt = $label1."|".$label2."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Non_Antonym_Labels.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
	public static function loadSynonymVerbsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Synonym_Verbs.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Synonym_Verbs.csv", "r");
		$antonymLablePairs = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label1 = $data[0];
			$label2 = $data[1];
			array_push($antonymLablePairs, array("verb1" => $label1, "verb2" => $label2));
		}
		fclose($fileHandler);
		return $antonymLablePairs;
	}
	
	public static function loadNonSynonymVerbsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Non_Synonym_Verbs.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Non_Synonym_Verbs.csv", "r");
		$nonAntonymLablePairs = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label1 = $data[0];
			$label2 = $data[1];
			array_push($nonAntonymLablePairs, array("verb1" => $label1, "verb2" => $label2));
		}
		fclose($fileHandler);
		return $nonAntonymLablePairs;
	}
	
	private static function persistSynonymVerbPair($verb1, $verb2) {
		$txt = $verb1."|".$verb2."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Synonym_Verbs.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
	private static function persistNonSynonymVerbPair($verb1, $verb2) {
		$txt = $verb1."|".$verb2."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Non_Synonym_Verbs.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
	public static function loadSynonymNounsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Synonym_Nouns.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Synonym_Nouns.csv", "r");
		$antonymLablePairs = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label1 = $data[0];
			$label2 = $data[1];
			array_push($antonymLablePairs, array("noun1" => $label1, "noun2" => $label2));
		}
		fclose($fileHandler);
		return $antonymLablePairs;
	}
	
	public static function loadNonSynonymNounsFromPersistedFile() {
		if ( !file_exists(Config::PERSISTENT_PATH."/NLP_Non_Synonym_Nouns.csv") ) return array();
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Non_Synonym_Nouns.csv", "r");
		$nonAntonymLablePairs = array();
		while ( ($data = fgetcsv($fileHandler, 1000, "|")) !== FALSE ) {
			$label1 = $data[0];
			$label2 = $data[1];
			array_push($nonAntonymLablePairs, array("noun1" => $label1, "noun2" => $label2));
		}
		fclose($fileHandler);
		return $nonAntonymLablePairs;
	}
	
	private static function persistSynonymNounPair($noun1, $noun2) {
		$txt = $noun1."|".$noun2."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Synonym_Nouns.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
	private static function persistNonSynonymNounPair($noun1, $noun2) {
		$txt = $noun1."|".$noun2."\n";
		$fileHandler = fopen(Config::PERSISTENT_PATH."/NLP_Non_Synonym_Nouns.csv", "a");
		fwrite($fileHandler, $txt);
		fclose($fileHandler);
	}
	
}
?>
