<?php
class NLPHighLevelTagTransformatorEN {
	
	private $tagToHighLevelTagAssignment = array(
		"{CD}"	=> "{CD}",
		"{JJ}"	=> "{D}",
		"{JJR}"	=> "{D}",
		"{JJS}"	=> "{D}",
		"{NN}"	=> "{N}",
		"{NNS}"	=> "{N}",
		"{NNP}"	=> "{N}",
		"{NNPS}"=> "{N}",
		"{RB}"	=> "{D}",
		"{RBR}"	=> "{D}",
		"{RBS}"	=> "{D}",
		"{RP}"	=> "{D}",
		"{UH}"	=> "",
		"{VB}"	=> "{A}",
		"{VBD}"	=> "{AP}",
		"{VBG}"	=> "{A}",
		"{VBN}"	=> "{AP}",
		"{VBP}"	=> "{A}",
		"{VBZ}"	=> "{A}",
		"{DT}"	=> "",
		"{POS}" => ""
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