<?php
abstract class ANLPLabelStyleVerifier {
	
	/**
	 * getLableStyle
	 *
	 * return the labeling style based on a tag set.
	 *
	 * @param  string $tagSetString high-level-tag-set-string
	 * @return string Label Style Key
	 */
	public function getLableStyleKey($tagSetString) {
		return isset($this->lableStyles[$tagSetString]) ? $this->lableStyles[$tagSetString]["style"] : null;
	}
	
	/**
	 * 
	 * @param unknown $part					"subject" | "verb" | "object"
	 * @param unknown $extendedTaggedLabel
	 * @param unknown $tagSetString
	 * @return NULL|string
	 */
	public function getLabelPart($part, $extendedTaggedLabel, $tagSetString) {
		$labelStyle = $this->getLableStyleKey($tagSetString);
		if ( is_null($labelStyle) ) return null;
		// get subject indexes of the tokens in the label
		$subjectIndexes = $this->lableStyles[$tagSetString][$part];
		if ( empty($subjectIndexes) ) return null;
		
		// construct the tokens of the label with regard to the high level tags
		$tokens = array();
		foreach ( $extendedTaggedLabel as $currToken ) {
			if ( !empty($currToken["high_level_tag"]) ) array_push($tokens, $currToken["token"]);
		}
		
		// retrieve subject
		$subject = "";
		//var_dump($subjectIndexes);
		foreach ( $subjectIndexes as $subjectIndex ) {
			$subject .= " ".$tokens[$subjectIndex];
		}
		
		return ltrim($subject);
	}
	
}
?>
