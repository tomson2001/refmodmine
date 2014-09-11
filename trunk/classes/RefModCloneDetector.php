<?php
class RefModCloneDetector {

	public $epc1;
	public $epc2;

	private $humanReadableStringEdgesOfEPC1;
	private $humanReadableIDEdgesOfEPC1;
	private $humanReadableStringEdgesOfEPC2;
	private $humanReadableIDEdgesOfEPC2;

	public $clones = array();

	public function __construct(EPC $epc1, EPC $epc2) {
		$this->epc1 = $epc1;
		$this->epc2 = $epc2;

		/**
		 * Vorbereitung des CloneDetection
		 */

		// Human Readable Edges berechnen
		$edges1 = $epc1->convertEdgesToHumanReadableWithIDs();
		$this->humanReadableStringEdgesOfEPC1 = $edges1["stringEdges"];
		$this->humanReadableIDEdgesOfEPC1 = $edges1["idEdges"];
		$edges2 = $epc2->convertEdgesToHumanReadableWithIDs();
		$this->humanReadableStringEdgesOfEPC2 = $edges2["stringEdges"];
		$this->humanReadableIDEdgesOfEPC2 = $edges2["idEdges"];

		// Kanten auf die gematchten Katen reduzieren
		$this->reduceEdges();
	}

	/**
	 * Fuehrt eine rekursive Berechnung der Clones durch
	 *
	 * @param unknown_type $sources Alle Startknoten des aktuellen Clone-Fragments
	 * @param unknown_type $inner	Das aktuelle Clone-Fragment
	 * @param unknown_type $targets Alle Endknoten des aktuellen Clone-Fragments
	 */
	public function execute($borderNodes=null, $inner=null) {

		// Abbruchkriterium
		if ( empty($this->humanReadableStringEdgesOfEPC1) && is_null($borderNodes) && is_null($inner) ) {
			return;
		}

		// Erster Schritt des Clone-Detection
		if ( is_null($inner) ) {
			$borderNodes = array();

			$idEdge = $this->humanReadableIDEdgesOfEPC1[0];
			$inner = array($idEdge);

			$keys = array_keys($idEdge);
			$sourceID = $keys[0];
			$targetID = $idEdge[$sourceID];

			array_push($borderNodes, $sourceID);
			array_push($borderNodes, $targetID);

			unset($this->humanReadableStringEdgesOfEPC1[0]);
			unset($this->humanReadableIDEdgesOfEPC1[0]);
			
			$this->execute($borderNodes, $inner);
			return;
		}

		// Clone hinzufuegen, wenn keine weiteren matchbaren quellen und senken existieren
		if ( is_null($borderNodes) ) {
			array_push($this->clones, $inner);
			$this->humanReadableIDEdgesOfEPC1 = array_values($this->humanReadableIDEdgesOfEPC1);
			$this->humanReadableStringEdgesOfEPC1 = array_values($this->humanReadableStringEdgesOfEPC1);
			$this->execute();
			return;
		}

		/**
		 * Erweiterung des Clones ($inner)
		 */
		$newBorderNodes = array();
		foreach ( $borderNodes as $nodeID ) {
			$edges = $this->pullEdgesInEPC1WhichComeFrom($nodeID);
			foreach ( $edges as $edge ) {
				$targetID = $edge[$nodeID];
				array_push($inner, $edge);
				array_push($newBorderNodes, $targetID);
			}
				
			$edges = $this->pullEdgesInEPC1WhichGoTo($nodeID);
			foreach ( $edges as $edge ) {
				$keys = array_keys($edge);
				$sourceID = $keys[0];
				array_push($inner, $edge);
				array_push($newBorderNodes, $sourceID);
			}
		}

		if ( empty($newBorderNodes) ) $newBorderNodes = null;
		$this->execute($newBorderNodes, $inner);
		return;
	}

	public function pullEdgesInEPC1WhichComeFrom($nodeID) {
		$pulledEdges = array();
		$workingArray1 = $this->humanReadableIDEdgesOfEPC1;
		$workingArray2 = $this->humanReadableStringEdgesOfEPC1;
		foreach ( $this->humanReadableIDEdgesOfEPC1 as $index => $edge ) {
			if ( isset($edge[$nodeID]) ) {
				array_push($pulledEdges, $edge);
				unset($workingArray1[$index]);
				unset($workingArray2[$index]);
			}
		}
		$this->humanReadableIDEdgesOfEPC1 = array_values($workingArray1);
		$this->humanReadableStringEdgesOfEPC1 = array_values($workingArray2);
		return $pulledEdges;
	}

	public function pullEdgesInEPC1WhichGoTo($nodeID) {
		$pulledEdges = array();
		$workingArray1 = $this->humanReadableIDEdgesOfEPC1;
		$workingArray2 = $this->humanReadableStringEdgesOfEPC1;
		foreach ( $this->humanReadableIDEdgesOfEPC1 as $index => $edge ) {
			if ( in_array($nodeID, $edge) ) {
				array_push($pulledEdges, $edge);
				unset($workingArray1[$index]);
				unset($workingArray2[$index]);
			}
		}
		$this->humanReadableIDEdgesOfEPC1 = array_values($workingArray1);
		$this->humanReadableStringEdgesOfEPC1 = array_values($workingArray2);
		return $pulledEdges;
	}

	/**
	 * Reduziert die Kanten-Arrays auf die Kanten, die in beiden EPKs enthalten sind
	 */
	private function reduceEdges() {
		// Reduzierung der Edges aus EPC1
		$workingArray1 = $this->humanReadableIDEdgesOfEPC1;
		$workingArray2 = $this->humanReadableStringEdgesOfEPC1;
		foreach ( $this->humanReadableStringEdgesOfEPC1 as $index => $edge ) {
			if ( !in_array($edge, $this->humanReadableStringEdgesOfEPC2) ) {
				unset($workingArray1[$index]);
				unset($workingArray2[$index]);
			}
		}

		$this->humanReadableIDEdgesOfEPC1 = array_values($workingArray1);
		$this->humanReadableStringEdgesOfEPC1 = array_values($workingArray2);

		// Reduzierung der Edges aus EPC2
		$workingArray1 = $this->humanReadableIDEdgesOfEPC2;
		$workingArray2 = $this->humanReadableStringEdgesOfEPC2;
		foreach ( $this->humanReadableStringEdgesOfEPC2 as $index => $edge ) {
			if ( !in_array($edge, $this->humanReadableStringEdgesOfEPC1) ) {
				unset($workingArray1[$index]);
				unset($workingArray2[$index]);
			}
		}

		$this->humanReadableIDEdgesOfEPC2 = array_values($workingArray1);
		$this->humanReadableStringEdgesOfEPC2 = array_values($workingArray2);
	}

	public function exportClonesAsEPML($folder="") {
		if ( empty($this->clones) ) return null;

		$content =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$content .= "<epml:epml xmlns:epml=\"http://www.epml.de\"\n";
		$content .= "  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"epml_1_draft.xsd\">\n";
		$content .= " <directory name=\"Clones_".$this->epc1->modelPath."---".$this->epc2->modelPath."\">";

		$epcID = 1;
		$arcID = 100000;
		foreach ( $this->clones as $clone ) {
			$numOfEdges = count($clone);
			$content .= "  <epc EpcId=\"".$epcID."\" name=\"Clone ".$epcID." (size: ".$numOfEdges.")\">\n";

			$includedNodes = array();

			foreach ( $clone as $edge ) {

				$keys = array_keys($edge);
				$sourceID = $keys[0];
				$targetID = $edge[$sourceID];

				if ( !in_array($sourceID, $includedNodes) ) {
					$content .= $this->getEPMLNode($sourceID);
					array_push($includedNodes, $sourceID);
				}
				if ( !in_array($targetID, $includedNodes) ) {
					$content .= $this->getEPMLNode($targetID);
					array_push($includedNodes, $targetID);
				}

				$content .= "    <arc id=\"".$arcID."\">\n";
				$content .= "      <flow source=\"".$sourceID."\" target=\"".$targetID."\" />\n";
				$content .= "    </arc>\n";

				$arcID++;
			}

			$content .= "  </epc>\n";
			$epcID++;
		}

		$content .= " </directory>\n</epml:epml>";

		$filename = trim("Clones_".$this->epc1->name."_".$this->epc1->id."---".$this->epc2->name."_".$this->epc2->id.".epml");
		$fileGenerator = new FileGenerator($filename, $content);
		$fileGenerator->setPath($folder);
		$fileGenerator->setFilename($filename);
		$file = $fileGenerator->execute(false);
		return $file;
	}

	private function getEPMLNode($nodeID) {
		if ( $this->epc1->isFunction($nodeID) ) {
			$epmlNode = "    <function id=\"".$nodeID."\">\n";
			$epmlNode .= "      <name>".$this->epc1->convertIllegalChars($this->epc1->getNodeLabel($nodeID))."</name>\n";
			$epmlNode .= "    </function>\n";
		}

		if ( $this->epc1->isEvent($nodeID) ) {
			$epmlNode = "    <event id=\"".$nodeID."\">\n";
			$epmlNode .= "      <name>".$this->epc1->convertIllegalChars($this->epc1->getNodeLabel($nodeID))."</name>\n";
			$epmlNode .= "    </event>\n";
		}

		if ( $this->epc1->isConnector($nodeID) ) {
			$epmlNode = "    <".$this->epc1->getNodeLabel($nodeID)." id=\"".$nodeID."\">\n";
			$epmlNode .= "      <name/>\n";
			$epmlNode .= "    </".$this->epc1->getNodeLabel($nodeID).">\n";
		}
		return $epmlNode;
	}

}
?>