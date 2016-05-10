<?php
class Wiktionary {
	
	public $word;
	
	public $synonyms = array();
	public $antonyms = array();
	
	public function __construct($word) {
		$this->word = $word;
		$this->loadInformationOnline();
	}
	
	public function loadInformationOnline() {
		$queryWord = str_replace(" ", "%20", $this->word);
		$uri = "https://en.wiktionary.org/w/api.php?format=xml&action=query&titles=".$queryWord."&rvprop=content&prop=revisions";
		$serviceReturnXML = self::curl_get_contents($uri);
		try {
			$xml = new SimpleXMLElement($serviceReturnXML);
		} catch (Exception $e) {
			print("\nCatched XML-Read Error caused by uri: ".$uri."\n\n");
			return null;
		}
		
		@ $content = (string) $xml->query->pages->page->revisions->rev;
		array_push($this->synonyms, $this->word);
		$this->extractSynonyms($content);
		$this->extractVerb($content);
		$this->extractRelatedTerms($content);
		$this->extractNoun($content);		
		//var_dump($content);
	}
	
	public static function curl_get_contents($url)	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		$data = curl_exec($curl);
		curl_close($curl);
		return $data;
	}
	
	private function extractSynonyms($content) {
		$separator = "=Synonyms=";
		$start = strpos($content, $separator);
		//var_dump($start);
		if ( $start === false ) return null;
		$content = substr($content, $start + strlen($separator));
		while ( strpos($content, "=") === 0 ) {
			$content = substr($content, 1);
		}
		$end = $this->getEndPartPosition($content);
		$content = $end === false ? $content : substr($content, 0, $end);
		$syns = trim($content);
		$startSynsPos = strpos($syns, "[[");
		if ( $startSynsPos === false ) return null;
		$syns = substr($syns, $startSynsPos);
		$syns = $this->removeUnusedChard($syns);
		if ( !empty($syns) ) {
			$synsArr = explode(", ", $syns);
			foreach ( $synsArr as $syn ) array_push($this->synonyms, $syn);
		}
	}
	
	/**
	 * Adding basic verb to syns
	 * @param unknown $content
	 */
	private function extractVerb($content) {
		$separator = "=Verb=";
		$start = strpos($content, $separator);
		if ( $start === false ) return null;
		$content = substr($content, $start + strlen($separator));
		$startVerbPos = strpos($content, "[[");
		if ( $startVerbPos === false ) return null;
		$verb = substr($content, $startVerbPos);
		$endVerbPos = strpos($verb, "]]");
		if ( $endVerbPos === false ) return null;
		$verb = substr($verb, 0, $endVerbPos);
		$verb = $this->removeUnusedChard($verb);
		if ( !empty($verb) && !substr_count($verb, ":") > 0 ) array_push($this->synonyms, $verb);
	}
	
	/**
	 * Adding basic noun(s) to syns
	 * @param unknown $content
	 */
	private function extractNoun($content) {
		$separator = "=Noun=";
		$start = strpos($content, $separator);
		if ( $start === false ) return null;
		$content = substr($content, $start + strlen($separator));
		$startNounPos = strpos($content, "[[");
		if ( $startNounPos === false ) return null;
		$nouns = substr($content, $startNounPos);
		$endNounPos = $this->getEndPartPosition($nouns);
		$nouns = substr($nouns, 0, $endNounPos);
		$nouns = eregi_replace("\],.*\[", "], [", $nouns);
		$nouns = $this->removeUnusedChard($nouns);
		
		if ( !empty($nouns) ) {
			$nounsArr = explode(", ", $nouns);
			foreach ( $nounsArr as $noun ) array_push($this->synonyms, $noun);
		}
	}
	
	/**
	 * Adding related terms to synonyms and antonyms
	 * @param unknown $content
	 */
	private function extractRelatedTerms($content) {
		
		// Antonyms
		$separator = "* antonyms: ";
		$start = strpos($content, $separator);
		if ( $start === false ) return null;
		$ants = substr($content, $start + strlen($separator));
		$startAntsPos = strpos($ants, "[[");
		if ( $startAntsPos === false ) return null;
		$ants = substr($ants, $startAntsPos);
		$endAntsPos = $this->getEndPartPosition($ants);
		if ( $endAntsPos === false ) return null;
		$ants = substr($ants, 0, $endAntsPos);
		$ants = $this->removeUnusedChard($ants);
		if ( !empty($ants) ) {
			$antsArr = explode(" or ", $ants);
			foreach ( $antsArr as $ant ) array_push($this->antonyms, $ant);
		}
		
		// Synonyms
		$separator = "* synonyms: ";
		$start = strpos($content, $separator);
		if ( $start === false ) return null;
		$syns = substr($content, $start + strlen($separator));
		$startSynsPos = strpos($syns, "[[");
		if ( $startSynsPos === false ) return null;
		$syns = substr($syns, $startSynsPos);
		$endSynsPos = $this->getEndPartPosition($syns);
		if ( $endSynsPos === false ) return null;
		$syns = substr($syns, 0, $endSynsPos);
		$syns = $this->removeUnusedChard($syns);
		if ( !empty($syns) ) {
			$synsArr = explode(" or ", $syns);
			foreach ( $synsArr as $syn ) array_push($this->synonyms, $syn);
		}
	}
	
	private function getEndPartPosition($contentPart) {
		$values = array();
		$end1 = strpos($contentPart, "{{");
		if ( $end1 !== false ) array_push($values, $end1);
		$end2 = strpos($contentPart, "=");
		if ( $end2 !== false ) array_push($values, $end2);
		$end3 = strpos($contentPart, "* ");
		if ( $end3 !== false ) array_push($values, $end3);
		$end4 = strlen($contentPart)-1;
		if ( $end4 !== false ) array_push($values, $end4);
		return (int) min(array_values($values));
	}
	
	private function removeUnusedChard($string) {
		$string = str_replace("[", "", $string);
		$string = str_replace("]", "", $string);
		$string = str_replace("\n", "", $string);
		$string = str_replace("\r", "", $string);
		return $string;
	}
	
}
?>