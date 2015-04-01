<?php
class EPCNLP extends EPC {
	
	public $functionTags = array();
	public $eventTags = array();
	
	public $functionHighLevelTags = array();
	public $eventHighLevelTags = array();
	
	public $functionLabelStyles = array();
	public $eventLabelStyles = array();
	
	/**
	 * 
	 */
	public function loadLabelTags() {
		if ( !empty($this->functionTags) || !empty($this->eventTags) ) return true;
		if ( $this->loadLabelTagsFromPersistedFile() ) return true;
		return $this->loadLabelTagsWithStanfordTagger();
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
			$tag = $data[2];
			$ids = $this->getIDsForLabel($label);
			foreach ( $ids["functions"] as $id ) {
				$this->functionTags[$id] = $tag;
			}
			foreach ( $ids["events"] as $id ) {
				$this->eventTags[$id] = $tag;
			}
		}
		return true;
	}
	
	/**
	 * Tag the labels of all functions and events with the Stanford POS Tagger
	 */
	private function loadLabelTagsWithStanfordTagger() {
		$tagger = new StanfordPOSTagger(Config::STANDFORD_POS_TAGGER_PATH);
		foreach ( $this->functions as $id => $label ) {
			if ( preg_match("/^t[0-9]*$/", $label) ) continue;
			$taggedLabel = $tagger->array_tag($label);
			$this->functionTags[$id] = $taggedLabel[0]["tag_set_clean"];
		}
		foreach ( $this->events as $id => $label ) {
			if ( preg_match("/^t[0-9]*$/", $label) ) continue;
			$taggedLabel = $tagger->array_tag($label);
			$this->eventTags[$id] = $taggedLabel[0]["tag_set_clean"];
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
			$fileContent .= "\nfunction;".$this->functions[$id].";".$tag;
		}
		foreach ( $this->eventTags as $id => $tag ) {
			$fileContent .= "\nevent;".$this->events[$id].";".$tag;
		}
		
		$fileGenerator = new FileGenerator("NLP-Tags_".$hash.".csv", $fileContent);
		$fileGenerator->setFilename("NLP-Tags_".$hash.".csv");
		$fileGenerator->setContent($fileContent);
		$fileGenerator->setPath("persistedData");
		return $fileGenerator->execute(false);
	}
	
	/**
	 * derives High Level Tags based on the Stanford Tags
	 * 
	 * @return boolean
	 */
	public function generateHighLevelLabelTags() {
		$NLPHighLevelTagTransformator = new NLPHighLevelTagTransformator();
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
	public function detectLableStyles() {
		$NLPLabelStyleVerifier = new NLPLableStyleVerifier();
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
		$fileContent .= "\nnode-type;label;tag-set;high-level-tag-set;label-style";
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
	
}
?>