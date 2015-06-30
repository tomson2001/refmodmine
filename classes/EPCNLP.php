<?php
/**
 * Some important links:
 * (1) http://www.comp.leeds.ac.uk/amalgam/tagsets/upenn.html
 * 
 * @TODO: Label Transformation!!!! In kontext-korrekt Label Style bringen, wenn also bspw. nur Acceptance, dann sollte eher 
 * accept Application da stehen mit den entsprechenden tags!
 * 
 * @author Tom Thaler
 *
 */
class EPCNLP extends EPC {
	
	// functions and events with its tags
	public $taggedFunctions = array();
	public $taggedEvents = array();
	
	// Tag sets clean
	public $functionTags = array();
	public $eventTags = array();
	
	public $functionHighLevelTags = array();
	public $eventHighLevelTags = array();
	
	public $functionLabelStyles = array();
	public $eventLabelStyles = array();
	
	/**
	 * 
	 */
	public function loadLabelTags($pos_tagger_model = Config::STANFORD_POS_TAGGER_MODEL) {
		if ( !empty($this->functionTags) || !empty($this->eventTags) ) return true;
		if ( !$this->loadLabelTagsFromPersistedFile() ) $this->loadLabelTagsWithStanfordTagger($pos_tagger_model);
		return $this->decodeTaggedLabels();
	}
	
	/**
	 * Checks whether the model is already tagged and persisted. If so, read the file
	 * and annotate the tags to the epc
	 * 
	 * @return boolean
	 */
	private function loadLabelTagsFromPersistedFile() {
		$hash = $this->getHash();
		if ( !file_exists("files/persistedData/NLP-Tags_".$hash.".csv") ) return false;
		$fileHandler = fopen("files/persistedData/NLP-Tags_".$hash.".csv", "r");
		$header = true;
		while ( ($data = fgetcsv($fileHandler, 1000, ";")) !== FALSE ) {
			if ( $header === true ) { $header = false; continue; }
			$label = $data[1];
			$taggedLabel = $data[2];
			$tag = $data[3];
			$ids = $this->getIDsForLabel($label);
			foreach ( $ids["functions"] as $id ) {
				$this->taggedFunctions[$id] = $taggedLabel;
				$this->functionTags[$id] = $tag;
			}
			foreach ( $ids["events"] as $id ) {
				$this->taggedEvents[$id] = $taggedLabel;
				$this->eventTags[$id] = $tag;
			}
		}
		return true;
	}
	
	/**
	 * Tag the labels of all functions and events with the Stanford POS Tagger
	 */
	private function loadLabelTagsWithStanfordTagger($pos_tagger_model = Config::STANFORD_POS_TAGGER_MODEL) {
		$tagger = new StanfordPOSTagger(Config::STANDFORD_POS_TAGGER_PATH);
		$tagger->set_model($pos_tagger_model);
		foreach ( $this->functions as $id => $label ) {
			if ( preg_match("/^t[0-9]*$/", $label) ) continue;
			$taggedLabel = $tagger->array_tag($label);
			$this->functionTags[$id] = $taggedLabel[0]["tag_set_clean"];
			$taggedTokens = array();
			foreach ( $taggedLabel[0]["tagged"] as $token ) {
				array_push($taggedTokens, $token["token"]."@@@".$token["tag"]);
			}
			$this->taggedFunctions[$id] = implode("|||", $taggedTokens);
		}
		foreach ( $this->events as $id => $label ) {
			if ( preg_match("/^t[0-9]*$/", $label) ) continue;
			$taggedLabel = $tagger->array_tag($label);
			$this->eventTags[$id] = $taggedLabel[0]["tag_set_clean"];
			$taggedTokens = array();
			foreach ( $taggedLabel[0]["tagged"] as $token ) {
				array_push($taggedTokens, $token["token"]."@@@".$token["tag"]);
			}
			$this->taggedEvents[$id] = implode("|||", $taggedTokens);
		}
		return $this->persistLabelTags();
	}
	
	/**
	 * Persisting the Label Tags to a csv-file. This is necessary because of the
	 * high calculation time, which is grounded in the php-wrapper
	 */
	private function persistLabelTags() {
		$hash = $this->getHash();
		$fileContent = $hash.";".$this->name;
		foreach ( $this->functionTags as $id => $tag ) {
			$fileContent .= "\nfunction;".$this->functions[$id].";".$this->taggedFunctions[$id].";".$tag;
		}
		foreach ( $this->eventTags as $id => $tag ) {
			$fileContent .= "\nevent;".$this->events[$id].";".$this->taggedEvents[$id].";".$tag;
		}
		
		$fileGenerator = new FileGenerator("NLP-Tags_".$hash.".csv", $fileContent);
		$fileGenerator->setFilename("NLP-Tags_".$hash.".csv");
		$fileGenerator->setContent($fileContent);
		$fileGenerator->setPath("persistedData");
		return $fileGenerator->execute(false);
	}
	
	private function decodeTaggedLabels() {
		foreach ( $this->taggedFunctions as $id => $taggedFunction ) {
			$taggedFunction = explode("|||", $taggedFunction);
			$this->taggedFunctions[$id] = array();
			foreach ( $taggedFunction as $index => $taggedToken ) {
				$taggedToken = explode("@@@", $taggedToken);
				$this->taggedFunctions[$id][$index]["token"] = $taggedToken[0];
				$this->taggedFunctions[$id][$index]["tag"] = $taggedToken[1];
			}
		}
		foreach ( $this->taggedEvents as $id => $taggedEvent ) {
			$taggedEvent = explode("|||", $taggedEvent);
			$this->taggedEvents[$id] = array();
			foreach ( $taggedEvent as $index => $taggedToken ) {
				$taggedToken = explode("@@@", $taggedToken);
				$this->taggedEvents[$id][$index]["token"] = $taggedToken[0];
				$this->taggedEvents[$id][$index]["tag"] = $taggedToken[1];
			}
		}
		return true;
	}
	
	private function encodeTaggedLabel($taggedLabel) {
		$tokens = array();
		foreach ( $taggedLabel as $token ) {
			array_push($tokens, $token["token"]."@@@".$token["tag"]);
		}
		return implode("|||", $tokens);
	}
	
	/**
	 * derives High Level Tags based on the Stanford Tags
	 * 
	 * @return boolean
	 */
	public function generateHighLevelLabelTags($lang="en") {
		switch ($lang) {
			case "de": return $this->generateHighLevelLabelTagsDE();
			default: return $this->generateHighLevelLabelTagsEN();
		}
	}
	
	private function generateHighLevelLabelTagsEN() {
		$NLPHighLevelTagTransformator = new NLPHighLevelTagTransformatorEN();
		foreach ( $this->functionTags as $id => $tag ) {
			$highLevelTag = $NLPHighLevelTagTransformator->transformTagSetString($tag);
			$this->functionHighLevelTags[$id] = $highLevelTag;
		}
		foreach ( $this->eventTags as $id => $tag ) {
			$highLevelTag = $NLPHighLevelTagTransformator->transformTagSetString($tag);
			$this->eventHighLevelTags[$id] = $highLevelTag;
		}
		return true;
	}
	
	private function generateHighLevelLabelTagsDE() {
		$NLPHighLevelTagTransformator = new NLPHighLevelTagTransformatorDE();
		foreach ( $this->functionTags as $id => $tag ) {
			$highLevelTag = $NLPHighLevelTagTransformator->transformTagSetString($tag);
			$this->functionHighLevelTags[$id] = $highLevelTag;
		}
		foreach ( $this->eventTags as $id => $tag ) {
			$highLevelTag = $NLPHighLevelTagTransformator->transformTagSetString($tag);
			$this->eventHighLevelTags[$id] = $highLevelTag;
		}
		return true;
	}
	
	/**
	 * detects the labeling styles formulated in Leopold 2014
	 * 
	 * @return boolean
	 */
	public function detectLableStyles($lang="en") {
		switch ($lang) {
			case "de": return $this->detectLabelStylesDE();
			default: return $this->detectLabelStylesEN();
		}
	}
	
	private function detectLabelStylesEN() {
		$NLPLabelStyleVerifier = new NLPLableStyleVerifierEN();
		foreach ( $this->functionHighLevelTags as $id => $highLevelTag ) {
			$styleKey = $NLPLabelStyleVerifier->getLableStyleKey($highLevelTag);
			$this->functionLabelStyles[$id] = $styleKey;
		}
		foreach ( $this->eventHighLevelTags as $id => $highLevelTag ) {
			$styleKey = $NLPLabelStyleVerifier->getLableStyleKey($highLevelTag);
			$this->eventLabelStyles[$id] = $styleKey;
		}
		return true;
	}
	
	private function detectLabelStylesDE() {
		$NLPLabelStyleVerifier = new NLPLableStyleVerifierDE();
		foreach ( $this->functionHighLevelTags as $id => $highLevelTag ) {
			$styleKey = $NLPLabelStyleVerifier->getLableStyleKey($highLevelTag);
			$this->functionLabelStyles[$id] = $styleKey;
		}
		foreach ( $this->eventHighLevelTags as $id => $highLevelTag ) {
			$styleKey = $NLPLabelStyleVerifier->getLableStyleKey($highLevelTag);
			$this->eventLabelStyles[$id] = $styleKey;
		}
		return true;
	}
	
	/**
	 * exportNLPAnalysisCSV
	 * 
	 * @return string (filename of the csv)
	 */
	public function exportNLPAnalysisCSV() {
		$fileContent = "Model name: ".$this->name;
		$fileContent .= "\nnode-type;label;tagged-label;tag-set;high-level-tag-set;label-style";
		foreach ( $this->functionTags as $id => $tag ) {
			$fileContent .= "\nactivity;".$this->functions[$id].";".$tag.";".$this->functionHighLevelTags[$id].";".$this->functionLabelStyles[$id];
		}
		foreach ( $this->eventTags as $id => $tag ) {
			$fileContent .= "\nevent;".$this->events[$id].";".$tag.";".$this->eventHighLevelTags[$id].";".$this->eventLabelStyles[$id];
		}
		
		$fileGenerator = new FileGenerator("NLP-Analysis_".$this->name.".csv", $fileContent);
		$fileGenerator->setFilename("NLP-Analysis_".$this->name.".csv");
		$fileGenerator->setContent($fileContent);
		return $fileGenerator->execute(false);
	}
	
	public function getEPMLNLPAnalysisCSVPart() {
		$fileContent = "";
		foreach ( $this->functionTags as $id => $tag ) {
			$fileContent .= "\n".$this->name.";
					activity;".$this->functions[$id].";
							".$this->encodeTaggedLabel($this->taggedFunctions[$id]).";
									".$tag.";".$this->functionHighLevelTags[$id].";
											".$this->functionLabelStyles[$id];
		}
		foreach ( $this->eventTags as $id => $tag ) {
			$fileContent .= "\n".$this->name.";event;".$this->events[$id].";".$this->encodeTaggedLabel($this->taggedEvents[$id]).";".$tag.";".$this->eventHighLevelTags[$id].";".$this->eventLabelStyles[$id];
		}
		return $fileContent;
	}
	
	public function getLabelExtractionCSVPart() {
		$fileContent = "";
		foreach ( $this->functions as $id => $label ) {
			$fileContent .= "\n".$this->name.";activity;".$label;
		}
		foreach ( $this->events as $id => $label ) {
			$fileContent .= "\n".$this->name.";event;".$label;
		}
		return $fileContent;
	}
	
	/**
	 * Possible Language combination: de-en, en-de
	 * 
	 * @param unknown $languageCombination
	 */
	public function translate($languageCombination) {
		$newFunctionLabels = LanguageTranslator::translate($languageCombination, $this->functions); 
		$newEventLabels = LanguageTranslator::translate($languageCombination, $this->events);
		if ( !is_null($newFunctionLabels) ) $this->functions = $newFunctionLabels;
		if ( !is_null($newEventLabels) ) $this->events = $newEventLabels; 
		return true;
	}
	
}
?>