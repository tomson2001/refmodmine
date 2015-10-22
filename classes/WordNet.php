<?php
/**
 * Entwickelt und getestet fuer die Windows-Version 2.1 von WordNet
 * Beschreibung des CLI von WordNet: http://wordnet.princeton.edu/wordnet/man/wn.1WN.html
 * 
 * Installation unter Linux: 
 * apt-get install libc6 \
 *   libx11-6 \
 *   tcl8.4 \
 *   tk8.4 \
 *   wordnet-base
 * 
 * @author t.thaler
 *
 */
class WordNet {
	
	private $word;
	
	private $splits = array(
			1 => "Synonyms/Hypernyms (Ordered by Estimated Frequency) of noun",
			2 => "Synonyms/Hypernyms (Ordered by Estimated Frequency) of verb",
			3 => "Derived Forms of noun",
			4 => "Derived Forms of verb",
			5 => "Antonyms of noun",
			6 => "Antonyms of verb"
	);
	
	public $syn_noun_cli = null;
	public $syn_verb_cli = null;
	public $verb_nouns_cli = null;
	public $noun_verbs_cli = null;
	public $ants_noun_cli = null;
	public $ants_verb_cli = null;
	
	public $syn_noun = null;
	public $syn_verb = null;
	public $verb_nouns = null;
	public $noun_verbs = null;
	public $ants_noun = null;
	public $ants_verb = null;
	
	private $antonymFunctionLabels = null;
	private $nonAntonymFunctionLabels = null;

	private $ext_deri = array(
		"assess" => "assessment"
	);
	
	public function __construct($word) {
		$this->word = $word;
		$this->getSynonymsAndMorphologyLinks($word);
		//$this->getAll();
	}
	
	public function getAll() {
		$this->getNounSyns();
		$this->getVerbSyns();
		$this->getNounConversionsOfVerb();
		$this->getVerbConversionsOfNoun();
		$this->getNounAnts();
		$this->getVerbAnts();
	}
	
	public function getNounSyns() {
		$this->syn_noun = self::extractSynonyms($this->syn_noun_cli);
		return $this->syn_noun;
	}
	
	public function getVerbSyns() {
		$this->syn_verb = self::extractSynonyms($this->syn_verb_cli);
		return $this->syn_verb;
	}
	
	public function getNounConversionsOfVerb() {
		$this->noun_verbs = self::extractMorphologyLinks($this->noun_verbs_cli, "noun");
		return $this->noun_verbs;
	}
	
	public function getVerbConversionsOfNoun() {
		$this->verb_nouns = self::extractMorphologyLinks($this->verb_nouns_cli, "verb");
		return $this->verb_nouns;
	}
	
	public function getNounAnts() {
		$this->ants_noun = self::extractAntonyms($this->ants_noun_cli);
		return $this->ants_noun;
	}
	
	public function getVerbAnts() {
		$this->ants_verb = self::extractAntonyms($this->ants_verb_cli);
		return $this->ants_verb;
	}
	
	public function getAllAntonyms() {
		$ants_noun = $this->getNounAnts();
		$ants_verb = $this->getVerbAnts();
		$ants = $ants_noun;
		foreach ( $ants_verb as $ant ) {
			if ( !in_array($ant, $ants) ) array_push($ants, $ant);
		}
		return $ants;
	}
	
	/**
	 * Ermittelt die Stammform eines Worted
	 * 
	 * @param unknown $word
	 */
	public static function getBaseFormOfVerb($word) {
		$cli_output = shell_exec(Config::WORDNET_EXE." ".$word." -synsv");
		$baseForm = self::extractBaseForm($cli_output);
		return $baseForm;
	}
	
	public static function getBaseFormOfNoun($word) {
		$cli_output = shell_exec(Config::WORDNET_EXE." ".$word." -synsn");
		$baseForm = self::extractBaseForm($cli_output);
		return $baseForm;
	}

	public static function getSynonyms($word) {
		$nounSyns = self::getNounSynonyms($word);
		$verbSyns = self::getVerbSynonyms($word);
		$synonyms = $nounSyns;
		foreach ( $verbSyns as $syn ) {
			if ( !in_array($syn, $synonyms) ) array_push($synonyms, $syn);
		}
		return $synonyms;
	}

	public static function getNounSynonyms($word) {
		$cli_output = shell_exec(Config::WORDNET_EXE." ".$word." -synsn");
		$synonyms = self::extractSynonyms($cli_output);
		return $synonyms;
	}

	public static function getVerbSynonyms($word) {
		$cli_output = shell_exec(Config::WORDNET_EXE." ".$word." -synsv");
		$synonyms = self::extractSynonyms($cli_output);
		return $synonyms;
	}

	/**
	 * Display derivational morphology links between noun and verb forms.
	 */
	public static function getNounsOfVerb($word) {
		$cli_output = shell_exec(Config::WORDNET_EXE." ".$word." -deriv");
		$nouns = self::extractMorphologyLinks($cli_output, "noun");
		return $nouns;
	}

	public static function getVerbsOfNoun($word) {
		$cli_output = shell_exec(Config::WORDNET_EXE." ".$word." -derin");
		$verbs = self::extractMorphologyLinks($cli_output, "verb");
		return $verbs;
	}

	public static function isNoun($word) {
		$cli_output = shell_exec(Config::WORDNET_EXE." ".$word." -synsn");
		return self::checkIfWordIsOfCorrectType($cli_output);
	}

	public static function isVerb($word) {
		$cli_output = shell_exec(Config::WORDNET_EXE." ".$word." -synsv");
		return self::checkIfWordIsOfCorrectType($cli_output);
	}

	/**
	 * Extrahierte bestimmte Type von Ausgaben
	 * @param string $cli_output
	 * @param string $type       noun, verb
	 * @return multitype:
	 */
	public static function extractMorphologyLinks($cli_output, $type) {
		$type = "(".$type.")";

		// Array erstellen mit einzelnen Bedeutungen
		$arr1 = explode("Sense ", $cli_output);
		if ( empty($arr1[0]) ) return array();

		// Alles bis einschliesslich RELATED_TO-> entfernen
		$arr2 = array();
		$i = 1;
		while ( $i <= Config::WORDNET_SYNONYM_LIMIT && isset($arr1[$i]) ) {
			$string = $arr1[$i];
			$strpos = strpos($string, "RELATED TO->");
			$cutout = substr($string, $strpos+12);
			$arr2[$i] = $cutout;
			$i++;
		}

		// jetzt alle related to's extrahieren
		$arr3 = array();
		foreach ( $arr2 as $string ) {
			$subarr = explode("RELATED TO->", $string);
			foreach ( $subarr as $element ) {
				$strpos = strpos($element, "=>");
				$cutout = substr($element, 0, $strpos);
				if ( substr($cutout, 0, strlen($type)) == $type ) {
					$cutout = substr($cutout, strlen($type));
					$cutout = substr_count($cutout, "#") == 1 ? $cutout = substr($cutout, 0, strpos($cutout, "#")) : $cutout;
					if ( !in_array($cutout, $arr3) ) array_push($arr3, $cutout);
				}
			}
		}

		return $arr3;
	}
	
	/**
	 * Extrahiert die Grundform eines Wortes aus dem Ergebnis des Kommandozeilenaufrufs
	 *
	 * @param string $cli_output
	 * @param bool $extractSyns liest die Stammform aus
	 * @return array
	 */
	private static function extractBaseForm($cli_output) {
		// Array erstellen mit einzelnen Bedeutungen
		$arr1 = explode("Sense ", $cli_output);
	
		if ( empty($arr1[0]) ) return null;
		// Extraktion des Stammwortes
		$strrpos = strrpos($arr1[0], " of ");
		$word = trim(preg_replace("/\r|\n/s", "", substr($arr1[0], $strrpos+4)));
		return $word;
	}

	/**
	 * Extrahiert die Synonyme aus dem Ergebnis des Kommandozeilenaufrufs
	 *
	 * @param string $cli_output
	 * @param bool $extractSyns Werden Synonyme extrahiert? Falls nein antonyme
	 * @return array
	 */
	private static function extractSynonyms($cli_output) {
		// Array erstellen mit einzelnen Bedeutungen
		$arr1 = explode("Sense ", $cli_output);

		if ( empty($arr1[0]) ) return array();
		// Extraktion des Stammwortes
		$strrpos = strrpos($arr1[0], " of ");
		$word = trim(preg_replace("/\r|\n/s", "", substr($arr1[0], $strrpos+4)));

		// jetzt alles bis einschliesslich => entfernen
		$arr2 = array();
		$i = 1;
		while ( $i <= Config::WORDNET_SYNONYM_LIMIT && isset($arr1[$i]) ) {
			$string = $arr1[$i];
			$strpos = strpos($string, "=>");
			$cutout = substr($string, 2, $strpos-2);
			array_push($arr2, $cutout);
			$cutout = substr($string, $strpos+2);
			array_push($arr2, $cutout);
			$i++;
		}
		//print_r($arr2);
		// extraktion der Synonyme
		$synonyms = array($word);
		$i = 0;
		foreach ( $arr2 as $syn_sense_string ) {
			$syn_sense_array = explode(", ", $syn_sense_string);
			foreach ( $syn_sense_array as $synonym ) {
				$synonym = ltrim(rtrim(preg_replace("/\r|\n/s", "", $synonym)));
				// Synonym nur hinzufuegen, wenn es aus genau einem Wort besteht
				if ( !substr_count($synonym, " ") && !in_array($synonym, $synonyms) && $i <= Config::WORDNET_SYNONYM_LIMIT ) {
					array_push($synonyms, $synonym);
					$i++;
				}
			}
		}
		if ( in_array("accept", $synonyms) ) {
			$flip = array_flip($synonyms);
			$key = $flip["accept"];
			$stem = PorterStemmer::Stem($word);
			if ( $stem == "receiv"  ) unset($synonyms[$key]);
		}
		
		return $synonyms;
	}
	
	private static function extractAntonyms($cli_output) {
		// Array erstellen mit einzelnen Bedeutungen
		$arr1 = explode("=>", $cli_output);
	
		$arr2 = array();		
		foreach ( $arr1 as $index => $part ) {
			if ( $index === 0 ) continue;
			$splitpoint = strpos($part, "Sense ");
			$cutout = ltrim(rtrim(substr($part, 0, $splitpoint)));
			if ( !empty($cutout) ) array_push($arr2, $cutout);
		}
		if ( empty($arr2) && count($arr1) == 2 ) array_push($arr2, $arr1[1]);
		
		$antonyms = array();
		foreach ( $arr2 as $antonymString ) {
			$antonymArr = explode(", ", $antonymString);
			foreach ( $antonymArr as $antonym ) {
				if ( substr_count($antonym, " ") === 0 && !in_array($antonym, $antonyms) ) array_push($antonyms, ltrim(rtrim($antonym)));
			}
		}

		return $antonyms;
	}

	/**
	 * false, falls nicht vom korrekten Worttyp (also bspw. checking ist kein Nomen)
	 */
	private static function checkIfWordIsOfCorrectType($cli_output) {
		$arr = explode("Sense ", $cli_output);
		return !empty($arr[0]);
	}

	public function getSynonymsAndMorphologyLinks($word) {
		$cli_output = shell_exec(Config::WORDNET_EXE." ".$word." -synsn -synsv -derin -deriv -antsn -antsv");
		$results = self::extractResultParts($cli_output);
		return $results;
	}

	public function extractResultParts($cli_output) {
		$this->syn_noun_cli = $this->getSplit($cli_output, 1);
		$this->syn_verb_cli = $this->getSplit($cli_output, 2);
		$this->verb_nouns_cli = $this->getSplit($cli_output, 3);
		$this->noun_verbs_cli = $this->getSplit($cli_output, 4);
		$this->ants_noun_cli = $this->getSplit($cli_output, 5);
		$this->ants_verb_cli = $this->getSplit($cli_output, 6);
	}
	
	private function getSplit($cli_output, $index) {
		if ( substr_count($cli_output, $this->splits[$index]) > 0 ) {
			$stopBefore = strpos($cli_output, $this->splits[$index]);
			if ( $index == max(array_keys($this->splits)) ) {
				$stopAfter = strlen($cli_output);
				return substr($cli_output, $stopBefore, $stopAfter-$stopBefore);
			}
			for ( $j=$index+1; $j<max(array_keys($this->splits)); $j++ ) {
				if ( substr_count($cli_output, $this->splits[$j]) == 1 ) {
					$stopAfter = strpos($cli_output, $this->splits[$j]);
					return substr($cli_output, $stopBefore, $stopAfter-$stopBefore);
				}
			}
			$stopAfter = strlen($cli_output);
			return substr($cli_output, $stopBefore, $stopAfter-$stopBefore);
		}
		return "";
	}
	
	public static function checkIfVerbsAreSynonym($word1, $word2) {
		$synsForWord1 = self::getVerbSynonyms($word1);
		$synsForWord2 = self::getVerbSynonyms($word2);
		//print_r($synsForWord1);
		//print_r($synsForWord2);
		foreach ( $synsForWord1 as $currWord ) {
			if ( in_array($currWord, $synsForWord2) ) return true;
		}
		
		$furtherSyns = array(
			array("word1" => "create", "word2" => "process"),
			array("word1" => "deliver", "word2" => "return"),
			array("word1" => "sign", "word2" => "process"),
			array("word1" => "check", "word2" => "investigate")
		);
		
		$base1 = self::getBaseFormOfVerb($word1);
		$base2 = self::getBaseFormOfVerb($word2);
		
		if ( $base1 == $base2 ) return true;
		
		foreach ( $furtherSyns as $pair ) {
			if ( $pair["word1"] == $base1 && $pair["word2"] == $base2 ) return true;
			if ( $pair["word1"] == $base2 && $pair["word2"] == $base1 ) return true;
		}
		
		return false;
	}
	
	public static function checkIfNounsAreSynonym($word1, $word2) {
		$synsForWord1 = self::getNounSynonyms($word1);
		$synsForWord2 = self::getNounSynonyms($word2);
		//print_r($synsForWord1);
		//print_r($synsForWord2);
		foreach ( $synsForWord1 as $currWord ) {
			if ( in_array($currWord, $synsForWord2) ) return true;
		}
		return false;
	}
	
	public static function checkIfVerbsAreAntonyms($word1, $word2) {
		// TODO
		$antonyms = array(
			array("word1" => "send", "word2" => "receive")
		);
		
		$word1 = strtolower($word1);
		$word2 = strtolower($word2);
		
		// try it on the given words for performance reasons
		foreach ( $antonyms as $pair ) {
			if ( $pair["word1"] == $word1 && $pair["word2"] == $word2 ) return true;
			if ( $pair["word1"] == $word2 && $pair["word2"] == $word1 ) return true;
		}		
		
		$word1 = self::getBaseFormOfVerb($word1);
		if ( is_null($word1) ) return false;
		$word2 = self::getBaseFormOfVerb($word2);
		if ( is_null($word2) ) return false;
		
		//print(" Basic form of word1: ".$word1."\n");
		//print(" Basic form of word2: ".$word2."\n");
		
		foreach ( $antonyms as $pair ) {
			if ( $pair["word1"] == $word1 && $pair["word2"] == $word2 ) return true;
			if ( $pair["word1"] == $word2 && $pair["word2"] == $word1 ) return true;
		}
		
		return false;
	}

}
?>