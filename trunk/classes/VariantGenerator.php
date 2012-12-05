<?php
/**
 * Die Klasse generiert unter einer zu waehlenden Parametrisierung
 * Variationen von Prozessmodellen (konkret EPKs). Dazu bestehen
 * folgende Möglichkeiten:
 * - Tauschen von Funktionsnoten
 * - Entfernen von Funktionsknoten
 * - Hinzufuegen von Funktionsknoten;
 * - Aendern von Labels
 *
 * @author Tom Thaler
 */
class VariantGenerator {

	private $switch; // Knoten tauschen ==> Verschieben: Anzahl der Vertauschungen (1 => Zwei Knoten vertauscht!)
	private $delete; // Anzahl der zu loeschenden Funktionsknoten
	private $add; // Anzahl der Funktionsknoten, die hinzugefuegt werden sollen
	private $edit; // Anzahl der Funktionslabels, die veraendert werden sollen
	private $editLetters; // Anzahl der Character, die veraendert werden sollen

	private $touchedFunctions = array(); // Enthaelt die IDs der Funktionsknoten, die bereits an einer Variation beteiligt waren
	private $epc = null; // Die variierte EPK

	public function __construct(EPC $epc, $switch, $delete, $add, $edit, $editLetters = 1) {
		$this->switch = $switch;
		$this->delete = $delete;
		$this->add = $add;
		$this->edit = $edit;
		$this->editLetters = $editLetters;

		$this->epc = clone $epc;
		$this->generateVariant();
	}

	/**
	 * Generiert eine Variante der gegebenen EPK mit den angegebenen Parametern
	 */
	private function generateVariant() {
		$this->switchFunctions();
		$this->deleteFunctions();
		$this->addFunctions();
		$this->editLabels();
	}

	/**
	 * Fuehrt Vertauschungen von Funktionen durch. Es wird dabei auch beruecksichtigt,
	 * dass kein Funktionsknoten zweimal getauscht wird. D.h. Wenn F1 und F2 getauscht
	 * werden, werden diese beiden Knoten nicht mehr fuer weitere Tauschungen beruecksichtigt.
	 */
	private function switchFunctions() {
		for ( $i=0; $i<$this->switch; $i++ ) {
			$ids = array();
			$labels = array();
			foreach ( $this->epc->functions as $id => $label ) {
				if ( !in_array($id, $this->touchedFunctions) ) {
					array_push($ids, $id);
					array_push($labels, $label);
					array_push($this->touchedFunctions, $id);
					if ( count($ids) == 2 ) {
						$this->epc->functions[$ids[0]] = $labels[1];
						$this->epc->functions[$ids[1]] = $labels[0];
						break;
					}
				}
			}
		}
	}

	/**
	 * Entfernt Funktionsknoten. Es dabei beruecksichtigt, dass die ausgewaehlte Knoten
	 * nicht bereits durch andere Operationen (z.B. Funktionstausch) behandelt wurde.
	 */
	private function deleteFunctions() {
		for ( $i=0; $i<$this->delete; $i++ ) {
			foreach ( $this->epc->functions as $id => $label ) {
				if ( !in_array($id, $this->touchedFunctions) ) {
					$predeccesors = $this->epc->getPredecessor($id);
					$successors = $this->epc->getSuccessor($id);
					array_push($this->touchedFunctions, $id);
						
					// Kanten zur Funktion entfernen
					foreach ( $successors as $succID ) {
						$this->epc->deleteEdge($id, $succID);
					}

					// Kanten von der Funktion weg entfernen
					foreach ( $predeccesors as $predID ) {
						$this->epc->deleteEdge($predID, $id);
					}

					// Neue Kanten zwischen Vorgaenger und Nachfolger hinzufuegen
					foreach ( $predeccesors as $predID ) {
						foreach ( $successors as $succID ) {
							$this->epc->addEdge($predID, $succID);
						}
					}
					
					// Funktion entfernen
					unset($this->epc->functions[$id]);
					break;
				}
			}
		}
	}

	/**
	 * Fuegt Funktionsknoten an eine zufaellige Senke der EPK an (also an ein Ende).
	 * Die Labels werden dabei von Variant_F0 bis Variant_Fn hochgezaehlt. 
	 */
	private function addFunctions() {
		for ( $i=0; $i<$this->add; $i++ ) {
			$lastNodeID = $this->epc->getLastNode();
			$newID = $this->epc->getFreeNodeID();
			$this->epc->functions[$newID] = "Variant_F".$i;
			if ( !is_null($lastNodeID) ) {
				$this->epc->addEdge($lastNodeID, $newID);
			}
			array_push($this->touchedFunctions, $newID);
		}
	}

	/**
	 * Veraendert Labels in einer gegebenen Anzahl von Characters ($this->editLetters)
	 * Es wird darauf geachtet, dass die Operation nicht auf Knoten angewendet wird, 
	 * die bereits anderweitig bearbeitet wurden (z.B. neue oder getauschte Funktionen)
	 */
	private function editLabels() {
		for ( $i=0; $i<$this->edit; $i++ ) {
			foreach ( $this->epc->functions as $id => $label ) {
				if ( !in_array($id, $this->touchedFunctions) ) {
					$editChars = strlen($label) > $this->editLetters ? $this->editLetters : strlen($label);
					$newLabel = $this->getRandomString($editChars);
					$newLabel .= substr($label, $editChars);
					$this->epc->functions[$id] = $newLabel;
					array_push($this->touchedFunctions, $id);
					break;
				}
			}
		}
	}
	
	/**
	 * Generiert einen String mit zufaelligen Buchstaben und Zahlen der Laenge $len
	 * 
	 * @param int $len Laenge des gewunschten Strings
	 * @return string
	 */
	private function getRandomString($len) {
		$chars = array ('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
			   'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
			   '0','1','2','3','4','5','6','7','8','9');
		$max = count($chars)-1;
		$string = "";
		for ($i=0; $i<$len; $i++) {
			$string .= $chars[rand(0,$max)];
		}
		return $string;
	}

	/**
	 * Gibt die erzeugte Modellvariante entsprechend der angegebenen Parameter zurueck
	 * 
	 * @return EPC
	 */
	public function getVariant() {
		return $this->epc;
	}

}
?>