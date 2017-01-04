<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MappingFile
 *
 * @author Simon
 */
class MappingFile {

    public $workspace;
    public $filename = null;
    public $filenameInWorkspace = null;
    public $filetype = null;
    public $matchings = array();
    private $nextMatchingID = 0;
    public $mergedMatching = null;

    public function __construct() {
        
    }

    public function getMatchesJSON() {
//            $matchesJson = array();
//            foreach ($this->matchings as $matching){
//                array_push($matchesJson, $matching->getJSON());
//            }

        return json_encode($this->matchings);
    }

    public function getMergedMatchingJSON() {
        return $this->mergedMatching->getJSON();
    }

    public function setFileName($filename) {
        $this->filename = $filename;
    }

    public function setFileType($filetype) {
        $this->filetype = $filetype;
    }

    public function addMatching($matching) {
        array_push($this->matchings, $matching);
    }

    public function loadMatching($filename, WorkspaceEPML $workspace) {
        if ($this->filetype === "rdfmatching") {
            $this->loadRDFMatching($filename);
        } else if ($this->filetype === "xmlmatching") {
            $this->loadXMLMatching($filename, $workspace, true);
        } else if ($this->filetype === "matching") {
            // unzip
            $zip = new ZipArchive();
            $files = array();
            if ($zip->open($filename)) {
                // http://stackoverflow.com/questions/9817525/in-php-is-it-possible-to-inspect-content-of-a-zip-file-without-extracting-its-co
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    array_push($files, basename($stat['name']));
                }

                $suc = false;
                $dirName = $filename . ".FOLDER";
                $dirName = str_replace("workspace.epml.", "", $dirName);

                if (sizeof($files) > 0) {
                    $suc = $zip->extractTo($dirName, $files);

                    foreach ($files as $filenameInDir) {
                        $this->loadRDFMatching($dirName . "/" . $filenameInDir);
                        unlink($dirName . "/" . $filenameInDir);
                    }
                    rmdir($dirName);
                }

                $zip->close();
            } else {
                exit("\nCannot open <" . $output . ">. Error creating zip file.\n");
            }
        } else {
            echo '<script language="JavaScript"><!--alert(\"wrong matching filetype!\");//--></script>';
        }
    }

    private function loadRDFMatching($filename) {
        $mapping = new GenericMapping();
        $mapping->loadRDF_BPMContest2015($filename, $this->nextMatchingID++);
        array_push($this->matchings, $mapping);
    }

    public function save(WorkspaceEPML $workspace) {
        if ($this->filetype === "rdfmatching") {
            if (sizeof($this->matchings) > 1) {
                $generatedFiles = array();
                foreach ($this->matchings as $key => $matching) {
                    $gm = new GenericMapping();
                    $gm->models = $this->matchings[$key]["models"];
                    $gm->maps = $this->matchings[$key]["maps"];

                    $fileNameRDF = null;
                    //$fileNameRDF = str_replace($this->filenameInWorkspace, "", $this->filename)."workspace.epml.rdfmatching.".$this->matchings[$key]["name"].".rdf";
                    array_push($generatedFiles, $gm->exportRDF_BPMContest2015(false, $fileNameRDF));
                }

                $output = str_replace("/workspace.epml.rdfmatching", "/workspace.epml.matching", $this->filename);
                $zip = new ZipArchive();
                if ($zip->open($output, ZipArchive::CREATE)) {
                    foreach ($generatedFiles as $filename) {
                        $pos = strrpos($filename, "/");
                        $file = substr($filename, $pos + 21);
                        $zip->addFile($filename, $file);
                    }
                    $zip->close();
                    foreach ($generatedFiles as $filename) {
                        unlink($filename);
                    }
                    $numFiles = count($generatedFiles);
                    print("done (#files: " . $numFiles . ", status" . $zip->status . ")");
                } else {
                    exit("\nCannot open <" . $output . ">. Error creating zip file.\n");
                }
            } else {
                foreach ($this->matchings as $key => $matching) {
                    $gm = new GenericMapping();
                    $gm->models = $this->matchings[$key]["models"];
                    $gm->maps = $this->matchings[$key]["maps"];
                    if ($this->matchings[$key]["name"] == null) {
                        $fileNameRDF = str_replace($this->filenameInWorkspace, "", $this->filename);
                        $gm->exportRDF_BPMContest2015StartingFileName(false, $fileNameRDF, "workspace.epml.rdfmatching.");
                    } else {
                        $fileNameRDF = $this->filename;
                        //$fileNameRDF = str_replace($this->filenameInWorkspace, "", $this->filename)."workspace.epml.rdfmatching.".$this->matchings[$key]["name"].".rdf";
                        $gm->exportRDF_BPMContest2015(false, $fileNameRDF);
                    }
                }
            }
        } else if ($this->filetype === "xmlmatching") {
            $this->exportXMLMatching($workspace);
        } else {
            echo '<script language="JavaScript"><!--alert(\"wrong matching filetype!\");//--></script>';
        }
    }

    private function exportXMLMatching(WorkspaceEPML $workspace) {
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $content .= "   <matchings>\n";
        foreach ($this->matchings as $key => $matching) {
            $content.= "    <matching name=\"" . $this->matchings[$key]["name"] . "\">\n";

            $content .= "       <epcs>\n";
            foreach ($this->matchings[$key]["models"] as $modelKey => $model) {
                $content .= "           <epc epcDescription=\"" . $this->matchings[$key]["models"][$modelKey] . "\"/>\n";
            }
            $content .= "       </epcs>\n";

            $content .= "       <matches>\n";
            foreach ($this->matchings[$key]["maps"] as $mapKey => $map) {
                $status = $this->matchings[$key]["maps"][$mapKey]["status"];
                if ($status == "CLOSED"){
                    $status = "CONFIRMED";
                }
                $content .= "           <match value=\"" . $this->matchings[$key]["maps"][$mapKey]["value"] . "\" status=\"" . $status . "\" interpretation=\"" . $this->matchings[$key]["maps"][$mapKey]["interpretation"] . "\">\n";
                foreach ($this->matchings[$key]["maps"][$mapKey]["nodeIDs"] as $nodeKey => $value) {
                    $epc = $workspace->getEPCByName($this->matchings[$key]["maps"][$mapKey]["modelIDs"][$nodeKey]);
                    $serachedNodeId = $this->matchings[$key]["maps"][$mapKey]["nodeIDs"][$nodeKey];
                    $label = $epc->getLabel($serachedNodeId);

                    if ($epc->isLabelUnique($label)) {
                        $content .= "                <node nodeDescription=\"" . htmlspecialchars($label) . "\" epcDescription=\"" . $this->matchings[$key]["maps"][$mapKey]["modelIDs"][$nodeKey] . "\" />\n";
                    } else {
                        // predecessors or sucsessors needed for identification
                        $content .= "                <node nodeDescription=\"" . htmlspecialchars($label) . "\" epcDescription=\"" . $this->matchings[$key]["maps"][$mapKey]["modelIDs"][$nodeKey] . "\">\n";

                        $idsForLabel = $epc->getIDsForLabelFunctionsAndEvents($label);



                        // lösche die ID des tatsächlichen Knotens
                        $deleteIndex = -1;
                        for ($index = 0; $index < count($idsForLabel); $index++) {
                            if ($idsForLabel[$index] === $serachedNodeId) {
                                $deleteIndex = $index;
                            }
                        }
                        array_splice($idsForLabel, $deleteIndex, 1);

                        // check predecessors
                        $unique = false;

                        // count entspricht dem level
                        $count = 1;

                        // predecessor IDs for each level for the searched node
                        $predsOfNodeIDs = array();
                        $ar1 = array();
                        $ar1[0] = $serachedNodeId;
                        $predsOfNodeIDs[0] = $ar1;

                        // predecessor Labels for each level for the searched node
                        $predsOfNodeLabels = array();
                        $ar2 = array();
                        $ar2[0] = $label;
                        $predsOfNodeLabels[0] = $ar2;


                        // Vorgänger IDs aller Knoten mit gleichem Label
                        $predsIds = array();
                        foreach ($idsForLabel as $keyPreds => $id) {
                            $predsIds[$keyPreds] = array();
                            $ar = array();
                            $ar[0] = $id;
                            $predsIds[$keyPreds][0] = $ar;
                        }

                        // Vorgänger Labels aller Knoten mit gleichem Label
                        $predsLabels = array();
                        foreach ($idsForLabel as $keyPreds => $id) {
                            $predsLabels[$keyPreds] = array();
                            $ar3 = array();
                            $ar3[0] = $epc->getLabel($id);
                            $predsLabels[$keyPreds][0] = $ar3;
                        }

                        // stop ist true, wenn der gesuchte/tatsächliche Knoten keine Vorgänger mehr hat
                        $stop = false;
                        while (!$unique && !$stop) {



                            // for each predecessor of previous level of searched node do: add new predecessors
                            foreach ($predsOfNodeIDs[$count - 1] as $key2 => $pred) {
                                // set IDs
                                $tmpPreds = $epc->getPredecessor($pred);
                                if (sizeof($tmpPreds) == 0) {
                                    $stop = true;
                                }
                                if ($predsOfNodeIDs[$count] == null) {
                                    $predsOfNodeIDs[$count] = array();
                                }
                                $predsOfNodeIDs[$count] = array_merge($predsOfNodeIDs[$count], $tmpPreds);

                                // set Labels
                                $tmpPredsLabels = array();
                                foreach ($tmpPreds as $onePred) {
                                    array_push($tmpPredsLabels, $epc->getLabel($onePred));
                                }
                                if ($predsOfNodeLabels[$count] == null) {
                                    $predsOfNodeLabels[$count] = array();
                                }
                                $predsOfNodeLabels[$count] = array_merge($predsOfNodeLabels[$count], $tmpPredsLabels);
                            }



                            // if (!$stop){
                            // gesuchter Knoten hat noch Vorgänger
                            // iteriere über jeden Knoten mit gleichem Label
                            foreach ($predsIds as $keyPreds => $predList) {

                                // for each predecessor of previous level do: add new predecessors
                                foreach ($predList[$count - 1] as $key2 => $pred) {
                                    // set IDs
                                    $tmpPreds = $epc->getPredecessor($pred);
                                    if ($predsIds[$keyPreds][$count] == null) {
                                        $predsIds[$keyPreds][$count] = array();
                                    }
                                    $predsIds[$keyPreds][$count] = array_merge($predsIds[$keyPreds][$count], $tmpPreds);

                                    // set Labels
                                    $tmpPredsLabels = array();
                                    foreach ($tmpPreds as $onePred) {
                                        array_push($tmpPredsLabels, $epc->getLabel($onePred));
                                    }
                                    if ($predsLabels[$keyPreds][$count] == null) {
                                        $predsLabels[$keyPreds][$count] = array();
                                    }
                                    $predsLabels[$keyPreds][$count] = array_merge($predsLabels[$keyPreds][$count], $tmpPredsLabels);
                                }
                            }


                            // prüfe für jeden Knoten mit gleichem Label, ob die Vorgänger identisch zu den Vorgängern des gesuchten Knotens sind
                            $delKeys = array();
                            foreach ($predsLabels as $delKey => $value) {
                                $arraysEqual = true;
                                if (sizeof($value) == sizeof($predsOfNodeLabels)) {
                                    for ($index2 = 0; $index2 < count($value); $index2++) {

                                        sort($value[$index2]);
                                        sort($predsOfNodeLabels[$index2]);
                                        if (sizeof($value[$index2]) == sizeof($predsOfNodeLabels[$index2]) && $value[$index2] === $predsOfNodeLabels[$index2]) {
                                            
                                        } else {
                                            $arraysEqual = false;
                                            break;
                                        }
                                    }
                                } else {
                                    $arraysEqual = false;
                                }
                                if ($arraysEqual) {
                                    // do nothing: arrays are equal
                                } else {
                                    // arrays are not equal thus delete node from list of nodes
                                    array_push($delKeys, $delKey);
                                }
                            }

                            // lösche alle Knoten, die andere Vorgänger haben
                            if (sizeof($delKeys) > 0) {
                                for ($index1 = count($delKeys) - 1; $index1 >= 0; $index1--) {
                                    array_splice($predsLabels, $delKeys[$index1]);
                                    array_splice($predsIds, $delKeys[$index1]);
                                }
                                if (sizeof($predsIds) == 0) {
                                    // es gibt keinen Knoten mit dem gleichen Label und den gleichen Vorgängern
                                    $unique = true;
                                }
                            }


                            $count++;
                            //}
                        }

                        if ($unique) {
                            // node found
                            // TODO: add predecessor nodes to xml

                            $content .= "                  <nodeId context=\"predecessors\">\n";
                            foreach ($predsOfNodeLabels as $keyOfPreds => $preds) {
                                if ($keyOfPreds !== 0) {
                                    if (sizeof($preds) > 0) {
                                        $content .= "                    <level level=\"" . $keyOfPreds . "\">\n";
                                        foreach ($preds as $key2 => $pred) {
                                            $content .= "                      <nodeDescription nodeDesc=\"" . htmlspecialchars($pred) . "\" />\n";
                                        }
                                        $content .= "                    </level>\n";
                                    } else {
                                        $content .= "                    <level level=\"" . $keyOfPreds . "\" />\n";
                                    }
                                }
                            }
                            $content .= "                  </nodeId>\n";
                        } else {
                            // lösche die ID des tatsächlichen Knotens
                            $deleteIndex = -1;
                            for ($index = 0; $index < count($idsForLabel); $index++) {
                                if ($idsForLabel[$index] === $serachedNodeId) {
                                    $deleteIndex = $index;
                                }
                            }
                            array_splice($idsForLabel, $deleteIndex, 1);

                            // check sucecessors
                            $unique = false;

                            // count entspricht dem level
                            $count = 1;

                            // sucecessor IDs for each level for the searched node
                            $sucsOfNodeIDs = array();
                            $ar1 = array();
                            $ar1[0] = $serachedNodeId;
                            $sucsOfNodeIDs[0] = $ar1;

                            // sucecessor Labels for each level for the searched node
                            $sucsOfNodeLabels = array();
                            $ar2 = array();
                            $ar2[0] = $label;
                            $sucsOfNodeLabels[0] = $ar2;


                            // Nachfolger IDs aller Knoten mit gleichem Label
                            $sucsIds = array();
                            foreach ($idsForLabel as $keySucs => $id) {
                                $sucsIds[$keySucs] = array();
                                $ar = array();
                                $ar[0] = $id;
                                $sucsIds[$keySucs][0] = $ar;
                            }

                            // Nachfolger Labels aller Knoten mit gleichem Label
                            $sucsLabels = array();
                            foreach ($idsForLabel as $keySucs => $id) {
                                $sucsLabels[$keySucs] = array();
                                $ar3 = array();
                                $ar3[0] = $epc->getLabel($id);
                                $sucsLabels[$keySucs][0] = $ar3;
                            }

                            // stop ist true, wenn der gesuchte/tatsächliche Knoten keine Nachfolger mehr hat
                            $stop = false;
                            while (!$unique && !$stop) {



                                // for each sucecessor of previous level of searched node do: add new sucecessors
                                foreach ($sucsOfNodeIDs[$count - 1] as $key2 => $suc) {
                                    // set IDs
                                    $tmpSucs = $epc->getSuccessor($suc);
                                    if (sizeof($tmpSucs) == 0) {
                                        $stop = true;
                                    }
                                    if ($sucsOfNodeIDs[$count] == null) {
                                        $sucsOfNodeIDs[$count] = array();
                                    }
                                    $sucsOfNodeIDs[$count] = array_merge($sucsOfNodeIDs[$count], $tmpSucs);

                                    // set Labels
                                    $tmpSucsLabels = array();
                                    foreach ($tmpSucs as $oneSuc) {
                                        array_push($tmpSucsLabels, $epc->getLabel($oneSuc));
                                    }
                                    if ($sucsOfNodeLabels[$count] == null) {
                                        $sucsOfNodeLabels[$count] = array();
                                    }
                                    $sucsOfNodeLabels[$count] = array_merge($sucsOfNodeLabels[$count], $tmpSucsLabels);
                                }



                                // if (!$stop){
                                // gesuchter Knoten hat noch Nachfolger
                                // iteriere über jeden Knoten mit gleichem Label
                                foreach ($sucsIds as $keySucs => $sucList) {

                                    // for each sucecessor of previous level do: add new sucecessors
                                    foreach ($sucList[$count - 1] as $key2 => $suc) {
                                        // set IDs
                                        $tmpSucs = $epc->getSuccessor($suc);
                                        if ($sucsIds[$keySucs][$count] == null) {
                                            $sucsIds[$keySucs][$count] = array();
                                        }
                                        $sucsIds[$keySucs][$count] = array_merge($sucsIds[$keySucs][$count], $tmpSucs);

                                        // set Labels
                                        $tmpSucsLabels = array();
                                        foreach ($tmpSucs as $oneSuc) {
                                            array_push($tmpSucsLabels, $epc->getLabel($oneSuc));
                                        }
                                        if ($sucsLabels[$keySucs][$count] == null) {
                                            $sucsLabels[$keySucs][$count] = array();
                                        }
                                        $sucsLabels[$keySucs][$count] = array_merge($sucsLabels[$keySucs][$count], $tmpSucsLabels);
                                    }
                                }


                                // prüfe für jeden Knoten mit gleichem Label, ob die Nachfolger identisch zu den Nachfolgern des gesuchten Knotens sind
                                $delKeys = array();
                                foreach ($sucsLabels as $delKey => $value) {
                                    $arraysEqual = true;
                                    if (sizeof($value) == sizeof($sucsOfNodeLabels)) {
                                        for ($index2 = 0; $index2 < count($value); $index2++) {

                                            sort($value[$index2]);
                                            sort($sucsOfNodeLabels[$index2]);
                                            if (sizeof($value[$index2]) == sizeof($sucsOfNodeLabels[$index2]) && $value[$index2] === $sucsOfNodeLabels[$index2]) {
                                                
                                            } else {
                                                $arraysEqual = false;
                                                break;
                                            }
                                        }
                                    } else {
                                        $arraysEqual = false;
                                    }
                                    if ($arraysEqual) {
                                        // do nothing: arrays are equal
                                    } else {
                                        // arrays are not equal thus delete node from list of nodes
                                        array_push($delKeys, $delKey);
                                    }
                                }

                                // lösche alle Knoten, die andere Nachfolger haben
                                if (sizeof($delKeys) > 0) {
                                    for ($index1 = count($delKeys) - 1; $index1 >= 0; $index1--) {
                                        array_splice($sucsLabels, $delKeys[$index1]);
                                        array_splice($sucsIds, $delKeys[$index1]);
                                    }
                                    if (sizeof($sucsIds) == 0) {
                                        // es gibt keinen Knoten mit dem gleichen Label und den gleichen Nachfolgern
                                        $unique = true;
                                    }
                                }


                                $count++;
                                //}
                            }

                            if ($unique) {
                                // node found
                                // TODO: add succsessors nodes to xml

                                $content .= "                  <nodeId context=\"successors\">\n";
                                foreach ($sucsOfNodeLabels as $keyOfSucs => $sucs) {
                                    if ($keyOfSucs !== 0) {
                                        if (sizeof($sucs) > 0) {
                                            $content .= "                    <level level=\"" . $keyOfSucs . "\">\n";
                                            foreach ($sucs as $key2 => $suc) {
                                                $content .= "                      <nodeDescription nodeDesc=\"" . htmlspecialchars($suc) . "\" />\n";
                                            }
                                            $content .= "                    </level>\n";
                                        } else {
                                            $content .= "                    <level level=\"" . $keyOfSucs . "\" />\n";
                                        }
                                    }
                                }
                                $content .= "                  </nodeId>\n";
                            }
                        }
                        $content .= "                </node>\n";
                    }
                }
                $content .= "           </match>\n";
            }
            $content .= "       </matches>\n";

            $content .= "   </matching>\n";
        }
        $content .= "</matchings>\n";

        chdir(dirname(getcwd()));


        $fileGenerator = new FileGenerator($this->filename, $content);
        $fileGenerator->setPathFilename($this->filename);
        $file = $fileGenerator->execute(false);
    }

    private function loadXMLMatching($filename, $workspace, $loadMergedMatching) {
        $matchingFile = new MappingFile();
        $matchingFile->setFileName($filename);
        $this->worksapce = $workspace;



        $xmlContent = file_get_contents($filename);
        if (empty($xmlContent))
            return;
        $xml = new SimpleXMLElement($xmlContent);
        foreach ($xml->xpath("matching") as $xml_matching) {
            $mapping = new GenericMapping();
            $mapping->filename = $filename;
            $mapping->id = $this->nextMatchingID++;
            $mapping->name = (string) $xml_matching["name"];


            foreach ($xml_matching->xpath("epcs") as $xml_epcs) {
                foreach ($xml_epcs->xpath("epc") as $xml_epc) {
                    array_push($mapping->models, (string) $xml_epc["epcDescription"]);
                }
            }
            foreach ($mapping->models as $modelName) {
                $workspaceEPC = $workspace->getEPCByName($modelName);
                if ($workspaceEPC == null) {
                    $mapping->models = array();
                    return;
                }
            }


            foreach ($xml_matching->xpath("matches") as $xml_matches) {
                foreach ($xml_matches->xpath("match") as $match) {
                    $value = (string) $match["value"];
                    $status = (string) $match["status"];
                    if ($status == "CONFIRMED"){
                        $status = "CLOSED";
                    }
                    $interpretation = (string) $match["interpretation"];

                    $nodeIDs = array();
                    $nodeLabels = array();
                    $modelIDs = array();

                    foreach ($match->xpath("node") as $node) {
                        $epc = $workspace->getEPCByName((string) $node["epcDescription"]);
                        $useOrder = false;
                        foreach ($node->xpath("nodeId") as $nodeID) {
                            $order = array();
                            $context = (string) $nodeID["context"];
                            $useOrder = true;
                            $order[0] = array();
                            foreach ($nodeID->xpath("level") as $level) {
                                $levelValue = (int) $level["level"];
                                $order[$levelValue] = array();

                                foreach ($level->xpath("nodeDescription") as $nodeDescription) {
                                    array_push($order[$levelValue], htmlspecialchars_decode((string) $nodeDescription["nodeDesc"]));
                                    // array_push($order, (string)$nodeDescription["nodeDesc"]);
                                }
                            }
                        }

                        $nodeID = -1;
                        if ($useOrder) {
                            $nodeID = $epc->getIDForLabelWithContext(htmlspecialchars_decode((string) $node["nodeDescription"]), $context, $order);
                        } else {
                            $nodeID = $epc->getFirstIDForLabel(htmlspecialchars_decode((string) $node["nodeDescription"]));
                        }

                        array_push($nodeIDs, $nodeID);
                        array_push($nodeLabels, htmlspecialchars_decode((string) $node["nodeDescription"]));
                        array_push($modelIDs, (string) $node["epcDescription"]);
                    }

                    $mapping->addMap($nodeIDs, $modelIDs, $value, $status, $interpretation);
                    unset($modelIDs);
                    unset($nodeLabels);
                }
            }
            array_push($this->matchings, $mapping);
        }


//            $mapping->loadXML($filename, $this->matchingID++);
//            if ($loadMergedMatching && count($this->matchings) > 1){
//                // create a merged Macthing for visualization
//                $_POST["matchings"] = $this->filename;
//$_POST["model_set"] = $workspace->file;
//$mergeFileNameComponents = explode("/", $this->filename);
//$mergeFileNameComponents[sizeof($mergeFileNameComponents)-1] = "merge.".$mergeFileNameComponents[sizeof($mergeFileNameComponents)-1];
//$mergeFileName = "";
//foreach ($mergeFileNameComponents as $comp){
//    $mergeFileName = $mergeFileName."/".$comp;
//}
//$mergeFileName = substr($mergeFileName, 1);
//$_POST["mergefile"] = $mergeFileName;
//$actionHandler = new WorkspaceActionHandler();
//$actionHandler->run("MERGE_MATCHES");
//$this->mergedMatching = $this->loadXMLMatching($mergeFileName, $workspace, false);
//
//            }
    }

}
