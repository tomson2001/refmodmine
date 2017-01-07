<?php

require './process-model/EPC.php';
require './workspace/WorkspaceEPML.php';
require './tools/FileGenerator.php';
require 'Config.php';
session_start();

// if isset is missing


$suc = chdir(Config::ABS_PATH);
$workspace = new WorkspaceEPML();
$model_set = $workspace->file;
$result = $workspace->file . "result.epml";

$command = "java -jar " . Config::ABS_PATH . "lib/master.jar CLI LAYOUT model_set=" . Config::ABS_PATH . $model_set . " result=" . Config::ABS_PATH . $result;
//$command = "php CLIExternalExecution.php command=".$command." description=empty sessionid=none notification=none checksum=none ";
$res = exec($command, $output);
//print_r($output);

$content_file = file_get_contents(Config::ABS_PATH . $result);
$xml = new SimpleXMLElement($content_file);

$models = "[";

foreach ($xml->xpath("//epc") as $xml_epc) {
    $epcID = $xml_epc["epcId"];
    $epcName = htmlspecialchars($xml_epc["name"]);
    $xPathEPCPart = isset($xml_epc["epcId"]) ? "//epc[@epcId='" . $epcID . "']" : "//epc[@EpcId='" . $epcID . "']";
    //loadEPML($xml_epc, $epcID, true, $models, $xPathEPCPart);


    $nodes = "[";
    $edges = "[";


    // Funktionen laden
    foreach ($xml->xpath($xPathEPCPart . "/function") as $function) {
        $graphics = $function->graphics;
        $nodeLayout = $graphics->nodeLayout;
        $position = $graphics->position;
        $node = "{\"id\": \"" . $function["id"] . "\", \"label\": \"" . rtrim(ltrim($function->name)) . "\", \"type\": \"function\", \"layout\": {\"x\": " . $position["x"] . ", \"y\": " . $position["y"] . ", \"width\": " . $nodeLayout["width"] . ", \"height\": " . $nodeLayout["depth"] . "}}";
        $nodes = $nodes . $node . ", ";
    }

    // Ereignisse laden
    foreach ($xml->xpath($xPathEPCPart . "/event") as $event) {
        $graphics = $event->graphics;
        $nodeLayout = $graphics->nodeLayout;
        $position = $graphics->position;
        $node = "{\"id\": \"" . $event["id"] . "\", \"label\": \"" . rtrim(ltrim($event->name)) . "\", \"type\": \"event\", \"layout\": {\"x\": " . $position["x"] . ", \"y\": " . $position["y"] . ", \"width\": " . $nodeLayout["width"] . ", \"height\": " . $nodeLayout["depth"] . "}}";
        $nodes = $nodes . $node . ", ";
    }

    // XOR laden
    foreach ($xml->xpath($xPathEPCPart . "/xor") as $xor) {
        $graphics = $xor->graphics;
        $nodeLayout = $graphics->nodeLayout;
        $position = $graphics->position;
        $diameter = $nodeLayout["radius"] * 2;

        // Temporary Fix
        $posX = $position["x"] + (37.5 - $nodeLayout["radius"]);
        $posy = $position["y"] + (22.5 - $nodeLayout["radius"]);

        $node = "{\"id\": \"" . $xor["id"] . "\", \"label\": \"" . rtrim(ltrim($xor->name)) . "\", \"type\": \"operator\", \"layout\": {\"x\": " . $posX . ", \"y\": " . $posy . ", \"width\": " . $diameter . ", \"height\": " . $diameter . "}}";
        $nodes = $nodes . $node . ", ";
    }

    // OR laden
    foreach ($xml->xpath($xPathEPCPart . "/or") as $or) {
        $graphics = $or->graphics;
        $nodeLayout = $graphics->nodeLayout;
        $position = $graphics->position;
        $diameter = $nodeLayout["radius"] * 2;

        // Temporary Fix
        $posX = $position["x"] + (37.5 - $nodeLayout["radius"]);
        $posy = $position["y"] + (22.5 - $nodeLayout["radius"]);

        $node = "{\"id\": \"" . $or["id"] . "\", \"label\": \"" . rtrim(ltrim($or->name)) . "\", \"type\": \"operator\", \"layout\": {\"x\": " . $posX . ", \"y\": " . $posy . ", \"width\": " . $diameter . ", \"height\": " . $diameter . "}}";
        $nodes = $nodes . $node . ", ";
    }

    // AND laden
    foreach ($xml->xpath($xPathEPCPart . "/and") as $and) {
        $graphics = $and->graphics;
        $nodeLayout = $graphics->nodeLayout;
        $position = $graphics->position;
        $diameter = $nodeLayout["radius"] * 2;

        // Temporary Fix
        $posX = $position["x"] + (37.5 - $nodeLayout["radius"]);
        $posy = $position["y"] + (22.5 - $nodeLayout["radius"]);

        $node = "{\"id\": \"" . $and["id"] . "\", \"label\": \"" . rtrim(ltrim($and->name)) . "\", \"type\": \"operator\", \"layout\": {\"x\": " . $posX . ", \"y\": " . $posy . ", \"width\": " . $diameter . ", \"height\": " . $diameter . "}}";
        $nodes = $nodes . $node . ", ";
    }

    // OrgEinheiten laden
    foreach ($xml->xpath($xPathEPCPart . "/role") as $role) {
        
    }

    // Kanten laden
    foreach ($xml->xpath($xPathEPCPart . "/arc") as $edge) {
        $flow = $edge->flow;
        $relation = $edge->relation;
        if (isset($flow['source']) && isset($flow['target'])) {
            $sourceIndex = (string) $flow['source'];
            $targetIndex = (string) $flow['target'];

            $graphics = $edge->graphics;
            $edgePoints = $graphics->EdgePoints;

            $pointNum = 1;
            $found = true;
            $layout = "";
            while ($found) {
                $found = false;
                $pointX = "x" . $pointNum;
                $pointY = "y" . $pointNum;

                if (isset($edgePoints[$pointX])) {
                    $layoutOfPoint = "{\"x\": " . $edgePoints[$pointX] . ", \"y\": " . $edgePoints[$pointY] . "}, ";

//                                        // Temporary Fix: MACHT EINIGES ANDERES KAPUTT
//                                        $nextPointY = "y".($pointNum+1);
//                                        if (!isset($edgePoints[$nextPointY])) {
//                                            if (((float)$edgePoints["y1"])<((float)$edgePoints[$pointY])){
//                                                if (((float)$edgePoints[$pointY])-((float)$edgePoints["y1"]) == 16){
//                                                    $prev = ((float)$edgePoints["y1"]);
//                                                    $prev = $prev + 20;
//                                                    $layoutOfPoint = "{\"x\": ".$edgePoints[$pointX].", \"y\": ".$prev."}, ";
//                                                }
//                                            }  
//                                        } 

                    $found = true;
                    $layout = $layout . $layoutOfPoint;
                }
                $pointNum++;
            }

            $layout = substr($layout, 0, -2);

            $edge = "{\"source\": \"" . $sourceIndex . "\", \"target\": \"" . $targetIndex . "\", \"layout\": [" . $layout . "]}";
            $edges = $edges . $edge . ", ";
        }
    }


    $nodes = substr($nodes, 0, -2);
    $edges = substr($edges, 0, -2);
    $nodes = $nodes . "]";
    $edges = $edges . "]";

    $modelCode = "{\"name\": \"" . $epcName . "\", \"nodes\": " . $nodes . ", \"edges\": " . $edges . "}, ";

    $models = $models . $modelCode;
}

$models = substr($models, 0, -2);
$models = $models . "]";

$h = "h";


//$resString = "";
//foreach ($models as $model){
//    $resString = $resString.$model;
//}

echo $models;

//echo $modelCode;

function loadEPML($xml, $modelID, $useOriginalIDs = true, $models, $xPathEPCPart) {
    $nodes = "";
    $edges = "";


    // Funktionen laden
    foreach ($xml->xpath($xPathEPCPart . "/function") as $function) {
        $index = $useOriginalIDs ? (string) $function["id"] : $this->getNextID();
        $graphics = $function->graphics;
        $nodeLayout = $graphics->nodeLayout;
        $position = $graphics->position;
        $node = "{id: " . $function["id"] . ", label: " + rtrim(ltrim($function->name)) + ", type: event, layout: {x: " + $position["x"] + ", y: " + $position["y"] + ", width: " + $nodeLayout["width"] + ", height: " + $nodeLayout["height"] . "}}";
        $nodes = $nodes . $node . ", ";
    }

    // Ereignisse laden
    foreach ($xml->xpath($xPathEPCPart . "/event") as $event) {
        $index = $useOriginalIDs ? (string) $function["id"] : $this->getNextID();
        $graphics = $function->graphics;
        $nodeLayout = $graphics->nodeLayout;
        $position = $graphics->position;
        $node = "{id: " . $function["id"] . ", label: " + rtrim(ltrim($function->name)) + ", type: event, layout: {x: " + $position["x"] + ", y: " + $position["y"] + ", width: " + $nodeLayout["width"] + ", height: " + $nodeLayout["height"] . "}}";
        $nodes = $nodes . $node . ", ";
    }

    // XOR laden
    foreach ($xml->xpath($xPathEPCPart . "/xor") as $xor) {
        $index = $useOriginalIDs ? (string) $function["id"] : $this->getNextID();
        $graphics = $function->graphics;
        $nodeLayout = $graphics->nodeLayout;
        $position = $graphics->position;
        $node = "{id: " . $function["id"] . ", label: " + rtrim(ltrim($function->name)) + ", type: event, layout: {x: " + $position["x"] + ", y: " + $position["y"] + ", width: " + $nodeLayout["radius"] + ", height: " + $nodeLayout["radius"] . "}}";
        $nodes = $nodes . $node . ", ";
    }

    // OR laden
    foreach ($xml->xpath($xPathEPCPart . "/or") as $or) {
        $index = $useOriginalIDs ? (string) $function["id"] : $this->getNextID();
        $graphics = $function->graphics;
        $nodeLayout = $graphics->nodeLayout;
        $position = $graphics->position;
        $node = "{id: " . $function["id"] . ", label: " + rtrim(ltrim($function->name)) + ", type: event, layout: {x: " + $position["x"] + ", y: " + $position["y"] + ", width: " + $nodeLayout["radius"] + ", height: " + $nodeLayout["radius"] . "}}";
        $nodes = $nodes . $node . ", ";
    }

    // AND laden
    foreach ($xml->xpath($xPathEPCPart . "/and") as $and) {
        $index = $useOriginalIDs ? (string) $function["id"] : $this->getNextID();
        $graphics = $function->graphics;
        $nodeLayout = $graphics->nodeLayout;
        $position = $graphics->position;
        $node = "{id: " . $function["id"] . ", label: " + rtrim(ltrim($function->name)) + ", type: event, layout: {x: " + $position["x"] + ", y: " + $position["y"] + ", width: " + $nodeLayout["radius"] + ", height: " + $nodeLayout["radius"] . "}}";
        $nodes = $nodes . $node . ", ";
    }

    // OrgEinheiten laden
    foreach ($xml->xpath($xPathEPCPart . "/role") as $role) {
        
    }

    // Kanten laden
    foreach ($xml->xpath($xPathEPCPart . "/arc") as $edge) {
        
    }

    array_push($models, $nodes);
}

?>