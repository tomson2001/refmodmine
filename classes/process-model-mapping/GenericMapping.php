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
        public $filename = null;
        public $name = null;
        
        public $id = -1;
        private $matchID = 0;
	
	private $validStatus = array("OPEN", "CLOSED");
	private $validInterpretations = array("SPECIALIZATION", "SIMILAR", "EQUAL", "CONCATENATION", "PART_OF", "ANALOGUE");
	private $validValueInterval = array("min" => 0, "max" => 1);
	 
	
	public function __construct() {
		
	}
        
        public function removeNodeFromMap($nodeID){
            foreach ( $this->maps as $mapIndex => $map ) {
			
                            foreach ( $map["nodeIDs"] as $index => $MatchNodeID ) {
                                if ($MatchNodeID == $nodeID){
                                    unset($this->maps[$mapIndex]["nodeIDs"][$index]);
                                    unset($this->maps[$mapIndex]["modelIDs"][$index]);
                                }
                            }
			
		}
        }
        
         public function removeMatch($matchID){
            foreach ( $this->maps as $mapIndex => $map ) {
			
                            
                                if ($map[matchID] == $matchID){
                                    unset($this->maps[$mapIndex]);
                                    unset($this->maps[$mapIndex]);
                                }
                            
			
		}
        }
        
	
	public function addModel($modelID, $modelName) {
		$this->models[$modelID]	= $modelName;
	}
	
	public function assignEPC(EPC $epc) {
		foreach ( $this->models as $id => $modelName ) {
			if ( $modelName == $epc->name ) {
				if ( !isset($this->epcs) ) $this->epcs = array();
				$this->epcs[$modelName] = $epc;
				return true;
			}
		}
		return false;
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
                        "matchID"                       => $this->matchID++,
			"nodeIDs" 			=> $nodeIDs,
			"modelIDs" 			=> $modelIDs,
			"status" 			=> $status,
			"interpretation" 	=> $interpretation,
			"value" 			=> $value,
			"refEpcID" 			=> $refEpcID
		);
		array_push($this->maps, $map);
		return true;
	}
	
	public function containsMap($map) {
		foreach ( $this->maps as $currMap ) {
			if ( self::equalsMap($map, $currMap) ) return true;
		}
		return false;
	}
	
	/**
	 * Checks whether two maps are equal (true) or not (false). A map is defined as follows
	 *
	 * array(
	 *	"nodeIDs" 			=> array(0 => id1, 1 => id2, ...),
	 *  "modelIDs" 			=> array(0 => model_of_nodeid1, 1 => model_of_nodeid2, ...),
	 *  "status" 			=> OPEN | CLOSED,
	 *  "interpretation" 	=> SPECIALIZATION | SIMILAR | EQUAL | CONCATENATION | PART_OF | ANALOGUE
	 *  "value" 			=> 0.25
	 *  "refEpcID" 			=> null
	 *  
	 *  The check includes the assignment of nodeIDs and modelIDs, the status, and the interpretation
	 *  
	 *  @param $checkValue a check of the value can be disabled by setting that param to false
	 */
	public static function equalsMap($map1, $map2, $checkValue=true) {
		
		//var_dump($map1);
		//var_dump($map2["status"]);
		
		if ( $map1["status"] != $map2["status"] ) return false;
		if ( $map1["interpretation"] != $map2["interpretation"] ) return false;
		
		if ( $checkValue ) {
			if ( $map1["value"] != $map2["value"] ) return false;
		}
		
		$nodesOfModelsOfMap1 = array();
		foreach ( $map1["nodeIDs"] as $nodeIndex => $nodeID ) {
			$modelID = $map1["modelIDs"][$nodeIndex];
			if ( !isset($nodesOfModelsOfMap1[$modelID]) ) {
				$nodesOfModelsOfMap1[$modelID] = array();
			}
			array_push($nodesOfModelsOfMap1[$modelID], $nodeID);
		}
		
		foreach ( $map2["nodeIDs"] as $nodeIndex => $nodeID ) {
			$modelID = $map2["modelIDs"][$nodeIndex];
			if ( !isset($nodesOfModelsOfMap1[$modelID]) ) return false;
			if ( !in_array($nodeID, $nodesOfModelsOfMap1[$modelID]) ) return false;
		}
		
		return true;
		
	}
	
	/**
	 * Maps are defined as above. This function generates string of it in particular formats.
	 * 
	 * @param string $format NODE_LABELS
	 */
	public static function convertMapToString($map, $format="NODE_LABELS", $epcs=null) {
		
		switch ( $format ) {
			case "NODE_LABELS":
				if ( is_null($epcs) ) //return false;
				exit("EPCs missing");
				
				// build a new array containing the $epcs with the ID as an index
				$indexedEPCs = array();
				foreach ( $epcs as $epc ) {
					$indexedEPCs[$epc->name] = $epc;
				}
				
				// build array for nodes of models
				$nodesOfModelsOfMap = array();
				foreach ( $map["nodeIDs"] as $nodeIndex => $nodeID ) {
					$modelID = $map["modelIDs"][$nodeIndex];
					if ( !isset($nodesOfModelsOfMap[$modelID]) ) {
						$nodesOfModelsOfMap[$modelID] = array();
					}
					array_push($nodesOfModelsOfMap[$modelID], $nodeID);
				}
				ksort($nodesOfModelsOfMap);
				
				$string = "";
				
				foreach ( $nodesOfModelsOfMap as $modelID => $nodes ) {
					if ( !isset($indexedEPCs[$modelID]) ) //return false;
					exit("Model not found");
					$string .= "{";
					
					$labels = array();
					foreach ( $nodes as $nodeID ) {
						$label = $indexedEPCs[$modelID]->getLabel($nodeID);
						if ( $label === false ) return false;
						array_push($labels, $label);
					}
					
					$string .= implode(",", $labels)."}";
				}
				return $string;
				
				break;	
			default: return false;
		}
		
	}
	
	public function loadRDF_BPMContest2015($filename, $id) {
                $this->id = $id;
                $dir = getcwd();
                $this->filename = $filename;
		$rdfContent = file_get_contents($filename);
		if ( empty($rdfContent) ) return;
		$xml = new SimpleXMLElement($rdfContent);
		
		$modelRetrieved = false;
		
		$modelName1 = "";
		$modelName2 = "";
		
		foreach ( $xml->Alignment->map as $map ) {
	
			$cell = $map->Cell;
			
			$entity1 = (string) $cell->entity1->attributes('rdf', true)->resource;
			$entity2 = (string) $cell->entity2->attributes('rdf', true)->resource;
			
			// Get model names
			if ( !$modelRetrieved ) {
				$modelName1 = str_replace("http://", "", $entity1);
				$rpos = strrpos($modelName1, "#");
				$modelName1 = str_replace(".epml", "", substr($modelName1, 0, $rpos));
//				$modelName1 = str_replace(" ", "", str_replace(":", "", $modelName1));
				$this->models[0] = $modelName1;
				
				$modelName2 = str_replace("http://", "", $entity2);
				$rpos = strrpos($modelName2, "#");
				$modelName2 = str_replace(".epml", "", substr($modelName2, 0, $rpos));
//				$modelName2 = str_replace(" ", "", str_replace(":", "", $modelName2));
				$this->models[1] = str_replace(".epml", "", substr($modelName2, 0, $rpos));
				$modelRetrieved = true;
			}
			
			// Get node ids of the mapped nodes
			$nodeID1 = str_replace("http://", "", $entity1);
			$rpos = strrpos($nodeID1, "#");
			$nodeID1 = substr($nodeID1, $rpos+1, strlen($nodeID1)-$rpos);
				
			$nodeID2 = str_replace("http://", "", $entity2);
			$rpos = strrpos($nodeID2, "#");
			$nodeID2 = substr($nodeID2, $rpos+1, strlen($nodeID2)-$rpos);
			
			!$this->addMap(array($nodeID1, $nodeID2), array($modelName1, $modelName2), 1);
		}
	}
	
        public function exportRDF_BPMContest2015StartingFileName($modelsSwitched = false, $path=null, $startingFileName=null) {
		
		$index = 1;
		$modelID1 = null;
		$modelID2 = null;
		$modelName1 = null;
		$modelName2 = null;
                if (sizeof($this->models) > 2){
                    // do nothing
                } else {
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
			
                        if (sizeof($this->models) == 2){
			foreach ( $map["nodeIDs"] as $index => $nodeID ) {
				if ( $map["modelIDs"][$index] == $modelName1 ) $currNodeID1 = $nodeID;
				if ( $map["modelIDs"][$index] == $modelName2 ) $currNodeID2 = $nodeID;
			}
                        $content .= "      <entity1 rdf:resource='http://".$modelName1."#".$currNodeID1."'/>\n";
			$content .= "      <entity2 rdf:resource='http://".$modelName2."#".$currNodeID2."'/>\n";
			$content .= "      <relation>=</relation>\n";
			$content .= "      <measure rdf:datatype='http://www.w3.org/2001/XMLSchema#float'>".$map["value"]."</measure>\n";
			$content .= "    </Cell>\n";
			$content .= "  </map>\n";
                        } else {
                            $count = 1;
                            foreach ( $map["nodeIDs"] as $index => $nodeID ) {
                                $content .= "      <entity".$count." rdf:resource='http://".$map["modelIDs"][$index]."#".$map["nodeIDs"][$index]."'/>\n";
                                $count++;
                            }
			$content .= "      <relation>=</relation>\n";
			$content .= "      <measure rdf:datatype='http://www.w3.org/2001/XMLSchema#float'>".$map["value"]."</measure>\n";
			$content .= "    </Cell>\n";
			$content .= "  </map>\n";
                        }
                        
			
			
                        
		}
		
		$content .= "</Alignment>\n";
		$content .= "</rdf:RDF>";
                
                $suc = chdir(Config::ABS_PATH);
                
		

                    $filename = $path.$startingFileName.$modelName1."-".$modelName2.".rdf";
			$fileGenerator = new FileGenerator($filename, $content);
			$fileGenerator->setPathFilename($filename);
			$file = $fileGenerator->execute(false);
		
		return $file;
	}
        
	public function exportRDF_BPMContest2015($modelsSwitched = false, $filename=null) {
		
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
				if ( $map["modelIDs"][$index] == $modelName1 ) $currNodeID1 = $nodeID;
				if ( $map["modelIDs"][$index] == $modelName2 ) $currNodeID2 = $nodeID;
			}
			
			$content .= "      <entity1 rdf:resource='http://".$modelName1."#".$currNodeID1."'/>\n";
			$content .= "      <entity2 rdf:resource='http://".$modelName2."#".$currNodeID2."'/>\n";
			$content .= "      <relation>=</relation>\n";
			$content .= "      <measure rdf:datatype='http://www.w3.org/2001/XMLSchema#float'>".$map["value"]."</measure>\n";
			$content .= "    </Cell>\n";
			$content .= "  </map>\n";
		}
		
		$content .= "</Alignment>\n";
		$content .= "</rdf:RDF>";
                
                $suc = chdir(Config::ABS_PATH);
		
		if ( is_null($filename) ) {
			$filename = $modelName1."-".$modelName2.".rdf";
			
			$fileGenerator = new FileGenerator($filename, $content);
			$fileGenerator->setFilename($filename);
			$file = $fileGenerator->execute();
		} else {
			$fileGenerator = new FileGenerator($filename, $content);
			$fileGenerator->setPathFilename($filename);
			$file = $fileGenerator->execute(false);
		}
		return $file;
	}
	
	public function exportRDF_BPMContest2015_Dataset3($modelsSwitched = false, $filename = null) {
	
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
                            $debug = $map["modelIDs"][$index];
				if ( $map["modelIDs"][$index] == $modelName1 ) $currNodeID1 = $nodeID;
				if ( $map["modelIDs"][$index] == $modelName2 ) $currNodeID2 = $nodeID;
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
	
                if ($filename !== null){
                $suc = chdir(Config::ABS_PATH);
                    $fileName = $filename;
                    $fileGenerator = new FileGenerator($fileName, $content);
		$fileGenerator->setPathFilename($fileName);
                } else {
                    $fileName = $modelName1;
                    $pos = strpos($fileName, "/");
                    $fileName = substr($fileName, 0, $pos);
                    $fileGenerator = new FileGenerator($fileName.".rdf", $content);
		$fileGenerator->setFilename($fileName.".rdf");
                }
	
		
		$file = $fileGenerator->execute();
		return $file;
	}
        
        public function getJSON(){
            $arr = array();
            $arr["mappingID"] = $this->id;
            $arr["fileName"] = $this->filename;
            $arr["matches"] = $this->maps;
            $code = json_encode($arr);
            $code2 = json_decode($code);
            return $code;
        }
        
        public function getJSONself(){
            $json = "{matchingID: ".$this->id.", matches: ";
            $isFirst = true;
            if (count($this->maps) > 0){
                $json = $json."[";
                foreach ( $this->maps as $map ) {
			$prefix = "";
			if ( $isFirst ) { $isFirst = false; } 
			else { $json = $json.", "; }

                        $mid1 = $map["modelIDs"][0];
			$mid2 = $map["modelIDs"][1];
                        
			$id1 = $map["nodeIDs"][0];
			$id2 = $map["nodeIDs"][1];
                        
			$json = $json."{matchID: ".$map["matchID"].", value: ".$map["value"].", nodes: [{modelID: '".$mid1."', nodeID: '".$id1."'}, {modelID: '".$mid2."', nodeID: '".$id2."'}]}";
		}
                $json = $json."]}";
            }
            return $json;
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
	
	/**
	 * for the contest 2015 rdf files
	 * Preliminary: assign epcs using function assignEPC()
	 */
	public function removeAllButFunctionMappings($removeMapsWithNonExistingNodeIDs=FALSE) {
		
		if ( !isset($this->epcs) ) {
			exit("no EPCs assigned to GenericMapping\n\n");
		}
		
		$numRemoved = 0; 
		foreach ( $this->maps as $mapID => $map ) {
			$nodeID1 = $map["nodeIDs"][0];
			$nodeID2 = $map["nodeIDs"][1];
			
			$modelName1 = $this->models[$map["modelIDs"][0]];
			$modelName2 = $this->models[$map["modelIDs"][1]];
			
			$doRemove = false;
			
			if ( !isset($this->epcs[$modelName1]) ) exit("\nError: EPC \"".$modelName1."\" not found!\n");
			if ( !isset($this->epcs[$modelName2]) ) exit("\nError: EPC \"".$modelName2."\" not found!\n");
			
			if ( $removeMapsWithNonExistingNodeIDs ) {
				if ( !$this->epcs[$modelName1]->isNodeIDAssigned($nodeID1) ) $doRemove = true;
				if ( !$this->epcs[$modelName2]->isNodeIDAssigned($nodeID2) ) $doRemove = true;
			} else {
				if ( !$this->epcs[$modelName1]->isNodeIDAssigned($nodeID1) ) exit("\nError: Node-ID \"".$nodeID1."\" in EPC \"".$modelName1."\" not found!\n");
				if ( !$this->epcs[$modelName2]->isNodeIDAssigned($nodeID2) ) exit("\nError: Node-ID \"".$nodeID2."\" in EPC \"".$modelName2."\" not found!\n");
			}
			
			if ( !$doRemove ) {
				if ( !$this->epcs[$modelName1]->isFunction($nodeID1) ) $doRemove = true;
				if ( !$this->epcs[$modelName2]->isFunction($nodeID2) ) $doRemove = true;
			}			
			
			if ( $doRemove ) {
				//print("      map \"".$this->epcs[$modelName1]->getLabel($nodeID1)."\" (".$nodeID1.") to \"".$this->epcs[$modelName2]->getLabel($nodeID2)."\" (".$nodeID2.") removed\n");
				$numRemoved++;
				unset($this->maps[$mapID]);
			} else {
				//print("      map \"".$this->epcs[$modelName1]->getLabel($nodeID1)."\" (".$nodeID1.") to \"".$this->epcs[$modelName2]->getLabel($nodeID2)."\" (".$nodeID2.") is ok\n");
			}
		}
		return $numRemoved;
	}
	
}
?>
