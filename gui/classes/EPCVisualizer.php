<?php

class EPCVisualizer {

    private $epc;
    private $num;
    private $mapping;
    private $insertedNodes = array();
    private $insertedEdges = array();
    private $insertedSatelliteObjects = array();
    private $insertedRelations = array();
    private $defaultColorFunction = "#80ff80";
    private $defaultColorEvent = "#FF8080";
    private $defaultColorOrgaUnit = "#FFFF80";
    private $defaultColorEdge = "gray";
    private $defaultColorRelation = "gray";
    private $defaultColorOperator = "gray";
    private $colorCounter = 0;
    private $distinctColors = [[240, 163, 255], [0, 117, 220], [153, 63, 0], [76, 0, 92], [25, 25, 25], [0, 92, 49], [43, 206, 72], [255, 204, 153], [128, 128, 128], [148, 255, 181], [143, 124, 0], [157, 204, 0], [194, 0, 136], [0, 51, 128], [255, 164, 5], [255, 168, 187], [66, 102, 0], [255, 0, 16], [94, 241, 242], [0, 153, 143], [224, 255, 102], [116, 10, 255], [153, 0, 0], [255, 255, 128], [255, 255, 0], [255, 80, 5]];
    private $jsCode = "";

    public function __construct(EPC $epc, $num, $mapping) {
        $this->epc = $epc;
        $this->num = $num;
        $this->mapping = $mapping;
    }

    // source: https://gist.github.com/Pushplaybang/5432844
    private function rgb2hex($rgb) {
        return '#' . sprintf('%02x', $rgb[0]) . sprintf('%02x', $rgb[1]) . sprintf('%02x', $rgb[2]);
    }

    public function generateVisJSCode() {

        $this->jsCode .= '
			<script type="text/javascript">
				var nodes = null;
				var edges = null;
                                var relations = null;
                                var satelliteObjects = undefined;
				
				function drawEPC' . $this->num . '() {
                                    name ="' . $this->epc->name . '";
					nodes = [';

        $this->insertedNodes = array();
        $startNodes = $this->epc->getAllStartNodes();
        $this->addNodesToVisualization($startNodes, 1);
        $this->addSatelliteObjectsToVisualization();
        $this->jsCode .= implode(",", $this->insertedNodes);

        $this->jsCode .= ']; edges = [';

        $this->addEdgesToVisualization();
        $this->addRelationsToVisualization();
        $this->jsCode .= implode(",", $this->insertedEdges);

        $this->jsCode .= '];';

        $this->jsCode .= '
				// create a network
				var container = document.getElementById("EPC' . $this->num . '");
				
                                
                                
				epcViz = new EPCViz(name, container, nodes, edges);

			}
			</script>
		';

        return $this->jsCode;
    }

    public function generateVisJSCodeEPCDiv() {

        $this->jsCode .= '
			<script type="text/javascript">
				function drawEPC' . $this->num . '(container, matchViz) {
                                var nodes = null;
				var edges = null;
                                var satelliteObjects = undefined;
                                var relations = null;                                    

                                    name ="' . $this->epc->name . '";
					nodes = [';

        $this->insertedNodes = array();
        $startNodes = $this->epc->getAllStartNodes();
        $this->addNodesToVisualization($startNodes, 1);
        $this->addSatelliteObjectsToVisualization();
        $this->jsCode .= implode(",", $this->insertedNodes);

        $this->jsCode .= ']; edges = [';

        $this->addEdgesToVisualization();
        $this->addRelationsToVisualization();
        $this->jsCode .= implode(",", $this->insertedEdges);

        $this->jsCode .= '];';

        $this->jsCode .= '
				// create a network
                                if (container === undefined){
                                    container = document.getElementById("EPC");
                                    onClickViewEpcViz_' . $this->epc->id . ' = new EPCViz(name, container, nodes, edges, matchViz);
                                        return;
                                }
				//var container = document.getElementById("EPC");
				
                                
                                
				epcViz_' . $this->epc->id . ' = new EPCViz(name, container, nodes, edges, matchViz);
                                
if (matchViz !== undefined){
    epcViz_' . $this->epc->id . '.setMatchViz(matchViz);
        var id = container.id;
       epcVisualizersNew[id] = [];
       epcVisualizersNew[id].push(epcViz_' . $this->epc->id . ');
}
			}
			</script>
		';

        return $this->jsCode;
    }

    public function generateVisJSCodeInMatchVizMultiple() {
        $this->jsCode = "";
        $this->jsCode .= '
				var nodes = null;
				var edges = null;
                                var relations = null;
				var satelliteObjects = undefined;
				
                                    name ="' . $this->epc->name . '";
					nodes = [';
        $this->insertedNodes = array();
        $startNodes = $this->epc->getAllStartNodes();
        $this->addNodesToVisualization($startNodes, 1);
        $this->addSatelliteObjectsToVisualization();
        $this->jsCode .= implode(",", $this->insertedNodes);

        $this->jsCode .= ']; edges = [';

        $this->addEdgesToVisualization();
        $this->addRelationsToVisualization();
        $this->jsCode .= implode(",", $this->insertedEdges);

        $this->jsCode .= '];';

        $this->jsCode .= '
				// create a network
				var container = document.getElementById("left_EPC' . $this->num . '");
				var container2 = document.getElementById("right_EPC' . $this->num . '");
                                
                                
				left_epcViz' . $this->num . ' = new EPCViz(name, container, nodes, edges);
                                right_epcViz' . $this->num . ' = new EPCViz(name, container2, nodes, edges);

                                visualizers.push(right_epcViz' . $this->num . ');
                                visualizers.push(left_epcViz' . $this->num . ');
			
		';

        return $this->jsCode;
    }

    public function generateVisJSCodeInMatchViz() {
        $this->jsCode = "";
        $this->jsCode .= '
				var nodes = null;
				var edges = null;
                                var satelliteObjects = undefined;
                                var relations = null;
				
				
                                    name ="' . $this->epc->name . '";
					nodes = [';
        $this->insertedNodes = array();
        $startNodes = $this->epc->getAllStartNodes();
        $this->addNodesToVisualization($startNodes, 1);
        $this->addSatelliteObjectsToVisualization();
        $this->jsCode .= implode(",", $this->insertedNodes);

        $this->jsCode .= ']; edges = [';

        $this->addEdgesToVisualization();
        $this->addRelationsToVisualization();
        $this->jsCode .= implode(",", $this->insertedEdges);

        $this->jsCode .= '];';

        $this->jsCode .= '
				// create a network
				var container = document.getElementById("EPC' . $this->num . '");
                                
                                
				epcViz' . $this->num . ' = new EPCViz(name, container, nodes, edges);

			
		';

        return $this->jsCode;
    }

    /**
     * Recursive node adding
     * 
     * @param unknown $nodes
     * @param unknown $level
     */
    private function addNodesToVisualization($nodes, $level) {
        foreach ($nodes as $id => $mixed) {
            if (array_key_exists($id, $this->insertedNodes))
                continue;
            $color = null;
//                        if ($this->mapping != null){
//                            $color = $this->rgb2hex($this->distinctColors[$this->colorCounter]);
//                            $this->colorCounter++;
//                        }
            $nodeCode = $this->getNodeCode($id, $level, $color);
            $this->insertedNodes[$id] = $nodeCode;
            $succNodes = $this->epc->getSuccessor($id);
            $succNodes = array_flip($succNodes);
            if (empty($succNodes))
                continue;
            $this->addNodesToVisualization($succNodes, $level + 1);
        }
    }

    private function getNodeCode($nodeID, $level, $color) {
        $nodeType = $this->epc->getType($nodeID);

        if ($color == null) {
            if ($nodeType == "function") {
                $color = $this->defaultColorFunction;
            } else if ($nodeType == "event") {
                $color = $this->defaultColorEvent;
            } else if ($nodeType == "xor" || $nodeType == "or" || $nodeType == "and") {
                $color = $this->defaultColorOperator;
            }
        }

        switch ($nodeType) {
            case "function": return "{id: '" . $nodeID . "', label: '" . str_replace("'", "\'", $this->epc->functions[$nodeID]) . "', type: 'function', group: '" . $this->epc->name . "', color: '" . $color . "', level: " . $level . "}\n";
            //case "function": return "{id: '".$nodeID."', label: '".str_replace("'", "\'", $this->epc->functions[$nodeID])."', image: DIR + 'activity.gif', shape: 'image', group: '".$this->epc->name."', color: '#00ff00', level: ".$level."}\n";
            case "event": return "{id: '" . $nodeID . "', label: '" . str_replace("'", "\'", $this->epc->events[$nodeID]) . "', type: 'event', group: '" . $this->epc->name . "', color: '" . $color . "', level: " . $level . "}\n"; #
            //case "event":	 return "{id: '".$nodeID."', label: '".str_replace("'", "\'", $this->epc->events[$nodeID])."', image: DIR + 'event.gif', shape: 'image', group: '".$this->epc->name."', color: '#FF6CFF', level: ".$level."}\n";
            case "xor": return "{id: '" . $nodeID . "', label: 'xor', type: 'operator', group: '" . $this->epc->name . "', color: '" . $color . "', level: " . $level . "}\n";
            case "or": return "{id: '" . $nodeID . "', label: 'or', type: 'operator', group: '" . $this->epc->name . "', color: '" . $color . "', level: " . $level . "}\n";
            case "and": return "{id: '" . $nodeID . "', label: 'and', type: 'operator', group: '" . $this->epc->name . "', color: '" . $color . "', level: " . $level . "}\n";
        }
    }

    private function addRelationsToVisualization() {
        foreach ($this->epc->functionOrgUnitAssignments as $relationID => $mixed) {

            $toString = $this->epc->functionOrgUnitAssignments[$relationID];

            $edgeCode = "{from: '" . $relationID . "', to: '" . $this->epc->functionOrgUnitAssignments[$relationID] . "', type:'relation', style: 'arrow', color: '" . $this->defaultColorRelation . "'}\n";
            array_push($this->insertedEdges, $edgeCode);
        }
    }

    private function addEdgesToVisualization() {
        $this->insertedEdges = array();
        foreach ($this->epc->edges as $edge) {
            foreach ($edge as $sourceID => $targetID) {
                $edgeCode = "{from: '" . $sourceID . "', to: '" . $targetID . "', type:'sequence', style: 'arrow', color: '" . $this->defaultColorEdge . "'}\n";
                array_push($this->insertedEdges, $edgeCode);
            }
        }
    }

    private function addSatelliteObjectsToVisualization() {
        foreach ($this->epc->orgUnits as $id => $mixed) {
            $orgUnitCode = "{id: '" . $id . "', label: '" . str_replace("'", "\'", $this->epc->orgUnits[$id]) . "', type: 'orgUnit', color: '" . $this->defaultColorOrgaUnit . "'}\n";
            array_push($this->insertedNodes, $orgUnitCode);
        }
    }

}

?>