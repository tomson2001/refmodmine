<?php
/**
 * Repraesentiert die Ontology eines Funktionsknoten mit Synonymen aus WordNet
 *
 * @author Tom Thaler
 */
class FunctionOntologyWithSynonyms {

	public $epc;
	public $id;
	public $label;

	public $nouns = array(); // Wortstaemme und Synonyme der Nomen innerhalb des Labels
	public $verbs = array(); // Wortstaemme und Synonyme der Verben innerhalb des Lables
	public $antonyms = array(); // Antonyme

	public $wordstems = array(); // Die Wortstaemme (PorterStems) aller Woerter innerhalb des Labels
	public $original_words = array(); // Mapping der PorterStems auf die originalen Woerter, also wordstem => word
	public $synonyms = array();

	private $words_ignore_list = array(
			"to", "and", "or", "of", "for", "by", "is", "if", "then", "else", "is", "are",
			"the", "with", "on", "has", "have", "about", "above", "within", "top", "letter",
			"a", "as", "in", "other", "according"
	);

	private $remove_character_list = array("!", "?", "%", "'", "'s", "*", "<", ">", "(", ")", "/", ";", ",", ".");

	private $useSynonyms = true;

	/**
	 * Konstruktor
	 *
	 * @param EPC    $epc    Die EPK, zu der die Funktion gehoert
	 * @param mixed  $funcID Die ID der Funktion
	 * @param string $label  Das Label der Funktion
	 */
	public function __construct(EPC &$epc, $funcID, $label) {
		$this->epc = $epc;
		$this->id = $funcID;
		$this->label = $label;
		$this->buildOntology();
	}

	/**
	 * Baut die Ontologie des Funktionsknotens
	 */
	private function buildOntology() {
		// 1. Operation: strtolower
		$label = strtolower($this->label);
		$label = str_replace("\n", " ", $label);
		$label = str_replace("\r", " ", $label);

		// 2. Split der Labels anhand der Leerzeichen
		$label_components = explode(" ", $label);

		// 3. Zahlen und $remove_chars rausnehmen
		foreach ( $label_components as $index => $component ) {
			$component = trim($component);
			foreach ( $this->remove_character_list as $char ) {
				$component = str_replace($char, "", $component);
			}
			$label_components[$index] = $component;
			// Labels mit Zahlen rausnehmen
			if ( preg_match("/^[0-9]*$/", $component) || empty($component) ) unset($label_components[$index]);
			// Dummy-Transitionen rausnehmen
			if ( preg_match("/^t[0-9]*$/", $component) ) unset($label_components[$index]);
		}

		// 4. Ignores rausnehmen
		foreach ( $label_components as $index => $component ) {
			if ( in_array($component, $this->words_ignore_list) ) unset($label_components[$index]);
		}

		// 5. Wortstaemme ermitteln (PorterStemmer)
		// 6. Synonyme, Verben, Mixed erstellen
		foreach ( $label_components as $component ) {
			$wordstem = PorterStemmer::Stem($component);
			array_push($this->wordstems, $wordstem);
			$this->original_words[$wordstem] = $component;

			if ( $this->useSynonyms ) {
				
				$wn = new WordNet($component);
				
				// Nomen
				$syns = $wn->getNounSyns();
				// Wortstamm als Key
				if ( !empty($syns) ) {
					//$wordstem = $syns[0];
					$this->nouns[$wordstem] = array();
					$this->synonyms[$wordstem] = array();
					$verbConversions = $wn->getVerbConversionsOfNoun();
					foreach ( $verbConversions as $verb ) {
						array_push($this->nouns[$wordstem], $verb);
					}
				}
				foreach ( $syns as $syn ) {
					if ( !in_array($syn, $this->nouns) ) array_push($this->nouns[$wordstem], $syn);
					if ( !in_array($syn, $this->synonyms) ) array_push($this->synonyms[$wordstem], $syn);
				}

				// Verben
				$syns = $wn->getVerbSyns();
				// Wortstamm als Key
				if ( !empty($syns) ) {
					//$wordstem = is_null($wordstem) ? $syns[0] : $wordstem;
					$this->verbs[$wordstem] = array();
					if ( !isset($this->synonyms[$wordstem]) ) $this->synonyms[$wordstem] = array();
					$nounConversions = $wn->getNounConversionsOfVerb();
					foreach ( $nounConversions as $noun ) {
						array_push($this->verbs[$wordstem], $noun);
					}
				}
				foreach ( $syns as $syn ) {
					if ( !in_array($syn, $this->verbs) ) array_push($this->verbs[$wordstem], $syn);
					if ( !in_array($syn, $this->synonyms) ) array_push($this->synonyms[$wordstem], $syn);
				}
				
				// Antonyme
				$ants = $wn->getAllAntonyms();
				if ( !empty($ants) ) {
					$this->antonyms[$wordstem] = array();
					foreach ( $ants as $ant ) {
						array_push($this->antonyms[$wordstem], $ant);
					}
				} 

				// Testausgabe
				//print("\"".$component."\"");
				//print("-");
			}
		}

	}

	/**
	 * prueft, ob sich bei dem Funktionsknoten dem label zufolge auch um ein Ergebnis handelt koennte
	 *
	 * @return boolean
	 */
	public function couldBeEvent_tmp() {
		//if ( empty($this->verbs) ) return true;
		$foundCorrectHandlingWord = false;
		$numWordstems = count($this->wordstems);
		foreach ( $this->wordstems as $wordstem ) {
			$originalWord = $this->original_words[$wordstem];
			// Pruefung, ob es sich sicher um ein Nomen handelt
			if ( in_array($wordstem, array_keys($this->nouns)) && !in_array($wordstem, array_keys($this->verbs))
			) {
				// Auch ein Nomen kann eine Funktion beschreiben, z.B. Acceptance, Enrollment, Rejection
				if ( substr($originalWord, -4) == "ance" || substr($originalWord, -4) == "ment" || substr($originalWord, -3) == "ion" ) {
					$foundCorrectHandlingWord = true;
					break;
				} else {
					print(" | \"".$wordstem."\" continued");
				}
			} else {
				if ( substr($originalWord, -2) != "ed" ) {
					$foundCorrectHandlingWord = true;
					break;
				}
			}
		}
		// 		foreach ( $this->verbs as $orginalWord => $syns ) {
		// 			// Pruefen, ob Wort auf ed endet, was auf sowas wie received, accepted, rejected usw. hindeutet
		// 			if ( substr($orginalWord, -2) != "ed" ) {
		// 				$foundCorrectHandlingWord = true;
		// 				break;
		// 			}
		// 		}
		//print(".");
		return !$foundCorrectHandlingWord;
	}

	public function couldBeEvent() {
		if ( substr_count($this->label, "?") ) return false;
		$numWordstems = count($this->wordstems);
		// Das Label besteht aus nur einem Wort
		if ( $numWordstems == 1 ) {
			$wordstem = $this->wordstems[0];
			$originalWord = $this->original_words[$wordstem];
			if ( in_array($wordstem, array_keys($this->nouns)) && !in_array($wordstem, array_keys($this->verbs)) ) {
				// Wort ist ein definitiv ein Nomen. Auch ein Nomen kann eine Funktion beschreiben, z.B. Acceptance, Enrollment, Rejection
				if ( substr($originalWord, -4) == "ance" || substr($originalWord, -4) == "ment" || substr($originalWord, -3) == "ion" ) return false;
				return true;
			} elseif ( in_array($wordstem, array_keys($this->verbs)) ) {
				// Word ist ein Verb
				return $this->hasWordEventCharacteristic($originalWord);
			}
			return false;
		} elseif ( $numWordstems == 2 ) {
			// Label besteht aus zwei Woertern
			$wordstem1 = $this->wordstems[0];
			$originalWord1 = $this->original_words[$wordstem1];
			$wordstem2 = $this->wordstems[1];
			$originalWord2 = $this->original_words[$wordstem2];
			// Wenn das zweite Wort auf "ed" Endet und das erste ein Nomen ist, koennte es sein,
			// dass sich um ein Ereignis handelt, z.B. Documents received vs. Check Documents / Application accepted
			if ( in_array($wordstem1, array_keys($this->nouns)) && in_array($wordstem2, array_keys($this->verbs)) ) {
				//$result = $this->hasWordEventCharacteristic($originalWord2) ? "Ereignis" : "Funktion";
				//print(".".$originalWord2."->".$result."|");
				return $this->hasWordEventCharacteristic($originalWord2);
			}
			// Es koennte auch ein Verb vorhanden sein zusammen mit einem Ajektiv, was ebenfalls fuer ein Ereignis sprechen kann, z.B. accepted provisionally
			if ( in_array($wordstem1, array_keys($this->verbs)) && !in_array($wordstem2, array_keys($this->synonyms)) ) return $this->hasWordEventCharacteristic($originalWord1);
			if ( in_array($wordstem2, array_keys($this->verbs)) && !in_array($wordstem1, array_keys($this->synonyms)) ) return $this->hasWordEventCharacteristic($originalWord2);
			// Es handelt sich bei beiden Wortern definitiv um Nomen
			if ( in_array($wordstem1, array_keys($this->nouns)) && !in_array($wordstem1, array_keys($this->verbs)) 
				&& in_array($wordstem2, array_keys($this->nouns)) && !in_array($wordstem2, array_keys($this->verbs)) ) return true;
			return false;
		} else {
			foreach ( $this->wordstems as $wordstem ) {
				$originalWord = $this->original_words[$wordstem];
				if ( in_array($wordstem, array_keys($this->nouns)) && !in_array($wordstem, array_keys($this->verbs)) ) {
					// Auch ein Nomen kann eine Funktion beschreiben, z.B. Acceptance, Enrollment, Rejection
					if ( substr($originalWord, -4) == "ance" || substr($originalWord, -4) == "ment" || substr($originalWord, -3) == "ion" ) return true;
					return false;
				} else {
					return $this->hasWordEventCharacteristic($originalWord);
				}
			}
		}
		return true;
	}

	private function hasWordEventCharacteristic($word) {
		if ( substr($word, -2) == "ed" ) return true;
		return false;
	}

}
?>