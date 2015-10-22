<?php
class GenericMapping {
	
	public $models = array(); // $models[id] = name
	
	/**
	 * Maps
	 * 
	 * id => array(
	 *	"nodeIDs" 			=> array(0 => id1, 1 => id2, ...), 
	 *  "modelIDs" 			=> array(0 => model_of_nodeid1, 1 => model_of_nodeid2, ...), 
	 *  "status" 			=> OPEN | CLOSED, 
	 *  "interpretation" 	=> SPECIALIZATION | SIMILAR | EQUAL | CONCATENATION | PART_OF | ANALOGUE
	 *  "value" 			=> 0.25
	 *  "refEpcID" 			=> null
	 */
	public $maps = array();
	
	private $validStatus = array("OPEN", "CLOSED");
	private $validInterpretations = array("SPECIALIZATION", "SIMILAR", "EQUAL", "CONCATENATION", "PART_OF", "ANALOGUE");
	private $validValueInterval = array("min" => 0, "max" => 1);
	 
	
	public function __construct() {
		
	}
	
	public function addModel($modelID, $modelName) {
		$this->models[$modelID]	= $modelName;
	}
	
	public function addMap(Array $nodeIDs, Array $modelIDs, $value, $status = "OPEN", $interpretation = "SIMILAR", $refEpcID = null) {
		// check match
		$numNodeIDs = count($nodeIDs);
		$numModelIDs = count($modelIDs);
		if ( $numNodeIDs != $numModelIDs ) return false;
		if ( $numNodeIDs == 0 ) return false;
		if ( $value < $this->validValueInterval["min"] || $value > $this->validValueInterval["max"] ) return false;
		if ( !in_array($status, $this->validStatus) ) return false;
		if ( !in_array($interpretation, $this->validInterpretations) ) return false;
		
		$map = array(
			"nodeIDs" 			=> $nodeIDs,
			"modelIDs" 			=> $modelIDs,
			"status" 			=> $value,
			"interpretation" 	=> $status,
			"value" 			=> $interpretation,
			"refEpcID" 			=> $refEpcID
		);
		array_push($this->maps, $map);
		return true;
	}
	
	public function exportRDF_BPMContest2015($modelsSwitched = false) {
		
		$index = 1;
		$modelID1 = null;
		$modelID2 = null;
		$modelName1 = null;
		$modelName2 = null;
		foreach ( $this->models as $id => $name ) {
			if ( $index == 1 ) {
				if ( $modelsSwitched ) { $modelID2 = $id; $modelName2 = $name; }
				else { $modelID1 = $id; $modelName1 = $name; }
			} else {
				if ( $modelsSwitched ) { $modelID1 = $id; $modelName1 = $name; }
				else { $modelID2 = $id; $modelName2 = $name; }
			}
			$index++;
		}
		
		$content =  "<?xml version='1.0' encoding='utf-8' standalone='no'?>\n";
		$content .= "<rdf:RDF xmlns='http://knowledgeweb.semanticweb.org/heterogeneity/alignment#'
		xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#'
		xmlns:xsd='http://www.w3.org/2001/XMLSchema#'
		xmlns:alext='http://exmo.inrialpes.fr/align/ext/1.0/' 
		xmlns:align='http://knowledgeweb.semanticweb.org/heterogeneity/alignment#'>\n";
		$content .= "<Alignment>\n";
		$content .= "  <xml>yes</xml>\n";
		$content .= "  <level>0</level>\n";
		$content .= "  <type>**</type>\n";
		
		$content .= "  <onto1>\n";
		$content .= "    <Ontology rdf:about=\"null\">\n";
		$content .= "      <location>null</location>\n";
		$content .= "    </Ontology>\n";
		$content .= "  </onto1>\n";
		$content .= "  <onto2>\n";
		$content .= "    <Ontology rdf:about=\"null\">\n";
		$content .= "      <location>null</location>\n";
		$content .= "    </Ontology>\n";
		$content .= "  </onto2>\n";
		
		foreach ( $this->maps as $map ) {
			$content .= "  <map>\n";
			$content .= "    <Cell>\n";
			$entityID = 0;
			
			$currNodeID1 = null;
			$currNodeID2 = null;
			
			foreach ( $map["nodeIDs"] as $index => $nodeID ) {
				if ( $map["modelIDs"][$index] == $modelID1 ) $currNodeID1 = $nodeID;
				if ( $map["modelIDs"][$index] == $modelID2 ) $currNodeID2 = $nodeID;
			}
			
			$content .= "      <entity1 rdf:resource='http://".$modelName1."#".$currNodeID1."'/>\n";
			$content .= "      <entity2 rdf:resource='http://".$modelName2."#".$currNodeID2."'/>\n";
			$content .= "      <relation>=</relation>\n";
			$content .= "      <measure rdf:datatype='http://www.w3.org/2001/XMLSchema#float'>1.0</measure>\n";
			$content .= "    </Cell>\n";
			$content .= "  </map>\n";
		}
		
		$content .= "</Alignment>\n";
		$content .= "</rdf:RDF>";
		
		$fileName = $modelName1."-".$modelName2;
		
		$fileGenerator = new FileGenerator($fileName.".rdf", $content);
		$fileGenerator->setFilename($fileName.".rdf");
		$file = $fileGenerator->execute();
		return $file;
	}
	
	public function exportRDF_BPMContest2015_Dataset3($modelsSwitched = false) {
	
		$index = 1;
		$modelID1 = null;
		$modelID2 = null;
		$modelName1 = null;
		$modelName2 = null;
		foreach ( $this->models as $id => $name ) {
			if ( $index == 1 ) {
				if ( $modelsSwitched ) { $modelID2 = $id; $modelName2 = $name; }
				else { $modelID1 = $id; $modelName1 = $name; }
			} else {
				if ( $modelsSwitched ) { $modelID1 = $id; $modelName1 = $name; }
				else { $modelID2 = $id; $modelName2 = $name; }
			}
			$index++;
		}
	
		$content =  "<?xml version='1.0' encoding='utf-8' standalone='no'?>\n";
		$content .= "<rdf:RDF xmlns='http://knowledgeweb.semanticweb.org/heterogeneity/alignment#'
		xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#'
		xmlns:xsd='http://www.w3.org/2001/XMLSchema#'
		xmlns:alext='http://exmo.inrialpes.fr/align/ext/1.0/'
		xmlns:align='http://knowledgeweb.semanticweb.org/heterogeneity/alignment#'>\n";
		$content .= "<Alignment>\n";
		$content .= "  <xml>yes</xml>\n";
		$content .= "  <level>0</level>\n";
		$content .= "  <type>**</type>\n";
	
		$content .= "  <onto1>\n";
		$content .= "    <Ontology rdf:about=\"null\">\n";
		$content .= "      <location>null</location>\n";
		$content .= "    </Ontology>\n";
		$content .= "  </onto1>\n";
		$content .= "  <onto2>\n";
		$content .= "    <Ontology rdf:about=\"null\">\n";
		$content .= "      <location>null</location>\n";
		$content .= "    </Ontology>\n";
		$content .= "  </onto2>\n";
	
		foreach ( $this->maps as $map ) {
			$content .= "  <map>\n";
			$content .= "    <Cell>\n";
			$entityID = 0;
				
			$currNodeID1 = null;
			$currNodeID2 = null;
				
			foreach ( $map["nodeIDs"] as $index => $nodeID ) {
				if ( $map["modelIDs"][$index] == $modelID1 ) $currNodeID1 = $nodeID;
				if ( $map["modelIDs"][$index] == $modelID2 ) $currNodeID2 = $nodeID;
			}
				
			$content .= "      <entity1 rdf:resource='http://sap/".$modelName1."/".$currNodeID1."'/>\n";
			$content .= "      <entity2 rdf:resource='http://sap/".$modelName2."/".$currNodeID2."'/>\n";
			$content .= "      <relation>=</relation>\n";
			$content .= "      <measure rdf:datatype='http://www.w3.org/2001/XMLSchema#float'>1.0</measure>\n";
			$content .= "    </Cell>\n";
			$content .= "  </map>\n";
		}
	
		$content .= "</Alignment>\n";
		$content .= "</rdf:RDF>";
	
		$fileName = $modelName1;
		$pos = strpos($fileName, "/");
		$fileName = substr($fileName, 0, $pos);
	
		$fileGenerator = new FileGenerator($fileName.".rdf", $content);
		$fileGenerator->setFilename($fileName.".rdf");
		$file = $fileGenerator->execute();
		return $file;
	}
	
	public function exportTXT_BPMContest2013(&$epc1, &$epc2) {
		$content = "";
		foreach ( $this->models as $modelID => $modeName ) {
			$content .= $modeName."\r\n";
		}
		
		$isFirst = true;
		foreach ( $this->maps as $map ) {
			$prefix = "";
			if ( $isFirst ) { $isFirst = false; } 
			else { $prefix = "\r\n"; }

			$id1 = $map["nodeIDs"][0];
			$id2 = $map["nodeIDs"][1];
			$label1 = $epc1->getNodeLabel($id1);
			$label2 = $epc2->getNodeLabel($id2);
			$content .= $prefix.$label1.",".$label2;
		}
		
		$fileGenerator = new FileGenerator(trim($epc1->name)."_".trim($epc2->name).".txt", $content);
		$fileGenerator->setFilename(trim($epc1->name)."_".trim($epc2->name).".txt");
		$file = $fileGenerator->execute();
		return $file;
	}
	
}
?>
