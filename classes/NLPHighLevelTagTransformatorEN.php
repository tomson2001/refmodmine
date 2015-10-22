<?php
class NLPHighLevelTagTransformatorEN extends ANLPHighLevelTagTransformator {
	
	protected $tagToHighLevelTagAssignment = array(
		"{CD}"	=> "{CD}",
		"{IN}"	=> "{IN}",
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
		"{DT}"	=> "{DT}",
		"{POS}" => "",
		"{TO}"	=> "{TO}",
		"{PRP}" => "{N}",
			
		"{extAN}" => "{extAN}"	// Action Noun, e.g. Rejection
	);	
	
}
?>