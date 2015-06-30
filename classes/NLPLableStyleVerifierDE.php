<?php
/**
 * Checks labels for its labeling style based on Leopold, S. 53ff, but German!
 * 
 * @author thaler
 *
 */
class NLPLableStyleVerifierDE {
	
	// Pattern of HighLevelTags => Label Style Key (see NLPHighLevelTransformator)
	private $lableStyles = array(

	);
	
	public function __construct() {
		
	}
	
	/**
	 * getLableStyle
	 * 
	 * return the labeling style based on a tag set.
	 * 
	 * @param  string $tagSetString tag-set-string
	 * @return string Label Style Key
	 */
	public function getLableStyleKey($tagSetString) {
		foreach ( $this->lableStyles as $pattern => $styleKey ) {
			if ( $pattern == $tagSetString ) return $styleKey;
		}
		return false;
	}
	
}
?>