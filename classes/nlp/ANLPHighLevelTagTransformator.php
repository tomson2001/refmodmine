<?php
abstract class ANLPHighLevelTagTransformator {
	
	public function transformTagSetString($tagSetString) {
		return str_replace(array_keys($this->tagToHighLevelTagAssignment), array_values($this->tagToHighLevelTagAssignment), $tagSetString);
	}
	
	public function transformTagToHighLevelTag($tag) {
		return isset($this->tagToHighLevelTagAssignment["{".$tag."}"]) ? substr($this->tagToHighLevelTagAssignment["{".$tag."}"], 1, -1) : null;
	}
	
}
?>
