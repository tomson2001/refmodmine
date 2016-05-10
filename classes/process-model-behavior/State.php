<?php
/**
 * Zustaende sind ueber Ihrer Token definiert. Token liegen auf Kanten
 *
 * @author Tom Thaler
 *
 */
class State {

	public $id;
	public $tokens = array(); // Token = Aktivierte Kanten (Initial: Token liegen auf Kanten nach Startereignissen)

	public function __construct($tokens) {
		$tokens = $this->sort($tokens);
		$this->tokens = $tokens;
		$this->setID();
	}
	
	public function sort($tokens) {
		$orderArray = array();
		
		// Etwas flascher klopfen
		foreach ($tokens as $token) {
			foreach ( $token as $source => $target ) {
				if ( isset($orderArray[$source]) ) {
					array_push($orderArray[$source], $target);
				} else {
					$orderArray[$source] = array($target);
				}
			}
		}
		
		// Keys sortieren
		ksort($orderArray);
		
		// Werte sortieren
		foreach ( $orderArray as $source => $targetArray ) {
			sort($orderArray[$source]);
		}
		
		// Token in korrekter Ordnung extrahieren
		$tokens = array();
		foreach ( $orderArray as $source => $targetArray ) {
			foreach ( $targetArray as $target ) {
				$token = array($source => $target);
				array_push($tokens, $token);
			}
		}
		
		return $tokens;
	}

	/**
	 * Ersetzt die automatisch erzeugte ID des Zustandes
	 *
	 * @param mixed $id
	 */
	public function setID($id=null) {
		$this->id = "";
		foreach ( $this->tokens as $token ) {
			foreach ( $token as $source => $target ) {
				$this->id .= "_".$source."-".$target;
			}
		}
		return $this->id == "" ? "REFMOD-AUTOGEN: Initial" : $this->id;
	}

	/**
	 * Prueft anhand der vorgelagerten Knoten Zustaende auf Gleichheit
	 *
	 * @param State $state
	 * @return boolean
	 */
	public function equals(State $state) {
		return $this->id == $state->id;
	}
	
	public function contains($requiredTokens) {
		$numOfRequiredTokens = count($requiredTokens);
		$countMatches = 0;
		foreach ( $this->tokens as $currToken ) {
			foreach ( $requiredTokens as $currRequiredToken ) {
				if ( $currRequiredToken == $currToken ) {
					$countMatches++;
					continue;
				}
			}
		}
		return $numOfRequiredTokens == $countMatches;
	}
	
	public function removeTokens($tokens) {
		foreach ( $this->tokens as $index => $currToken ) {
			foreach ( $tokens as $currRemToken ) {
				if ( $currRemToken == $currToken ) {
					unset($this->tokens[$index]);
					continue;
				}
			}
		}
		$this->refresh();
	}
	
	public function addToken($token) {
		if ( !$this->contains(array($token)) ) {
			array_push($this->tokens, $token);
			$this->refresh();
			return true;
		}
		return false;
	}
	
	public function refresh() {
		sort($this->tokens);
		$this->setID();
	}

}
?>