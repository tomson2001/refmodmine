<?php
class NLPHighLevelTagTransformatorDE {
	
	private $tagToHighLevelTagAssignment = array(

	);
	
	public function __construct() {
		
	}
	
	/**
	 * Takes the tag_set_clean string from StanfordPOSTageer replaces
	 * the tags by high level tags
	 *  
	 * @param string $tagSetString tag_set_clean tags
	 * @return string
	 */
	public function transformTagSetString($tagSetString) {
		return str_replace(array_keys($this->tagToHighLevelTagAssignment), array_values($this->tagToHighLevelTagAssignment), $tagSetString);
	}
	
}
?>