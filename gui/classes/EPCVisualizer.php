<?php
class EPCVisualizer {
	
	private $epc;
	private $insertedNodes = array();
	private $insertedEdges = array();
	
	private $jsCode = "";
	
	public function __construct(EPC $epc) {
		$this->epc = $epc;
	}
	
	public function generateVisJSCode() {
		
		$this->jsCode .= '
			<script type="text/javascript">
				var nodes = null;
				var edges = null;
				var network = null;
				
				var DIR = "gui/img/";
				
				function drawEPC() {
					nodes = [';
		
		$startNodes = $this->epc->getAllStartNodes();
		$this->addNodesToVisualization($startNodes, 1);
		$this->jsCode .= implode(",", $this->insertedNodes);
		
		$this->jsCode .= ']; edges = [';
		
		$this->addEdgesToVisualization();
		$this->jsCode .= implode(",", $this->insertedEdges);
		
		$this->jsCode .= '];';
		
		$this->jsCode .= '
				// create a network
				var container = document.getElementById("EPC");
				var data = {
					nodes: nodes,
					edges: edges
				};
				var options = {
					width:  "550px",
      				height: "650px",
					stabilize: true,
				
				    layout: {
						hierarchical: {
							nodeSpacing: 200,
				 			direction: "UD",
							sortMethod: "directed"
						}
				    },
					navigation: true,
					keyboard: true,
					edges: {
					  smooth: false
					}	
				};
				network = new vis.Network(container, data, options);
			}
			</script>
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
		foreach ( $nodes as $id => $mixed ) {
			if ( array_key_exists($id, $this->insertedNodes) ) continue;
			$nodeCode = $this->getNodeCode($id, $level);
			$this->insertedNodes[$id] = $nodeCode;
			$succNodes = $this->epc->getSuccessor($id);
			$succNodes = array_flip($succNodes);
			if ( empty($succNodes) ) continue;
			$this->addNodesToVisualization($succNodes, $level+1);
		}
	}
	
	private function getNodeCode($nodeID, $level) {
		$nodeType = $this->epc->getType($nodeID);
		switch ( $nodeType ) {
			case "function": return "{id: '".$nodeID."', label: '".str_replace("'", "\'", $this->epc->functions[$nodeID])."', shape: 'box', group: '".$this->epc->name."', color: '#80ff80', level: ".$level."}\n";
			//case "function": return "{id: '".$nodeID."', label: '".str_replace("'", "\'", $this->epc->functions[$nodeID])."', image: DIR + 'activity.gif', shape: 'image', group: '".$this->epc->name."', color: '#00ff00', level: ".$level."}\n";
			case "event":	 return "{id: '".$nodeID."', label: '".str_replace("'", "\'", $this->epc->events[$nodeID])."', shape: 'ellipse', group: '".$this->epc->name."', color: '#FF8080', level: ".$level."}\n";	#
			//case "event":	 return "{id: '".$nodeID."', label: '".str_replace("'", "\'", $this->epc->events[$nodeID])."', image: DIR + 'event.gif', shape: 'image', group: '".$this->epc->name."', color: '#FF6CFF', level: ".$level."}\n";
			case "xor":		 return "{id: '".$nodeID."', label: '', image: DIR + 'xor.gif', shape: 'image', group: '".$this->epc->name."', color: 'gray', level: ".$level."}\n";
			case "or":		 return "{id: '".$nodeID."', label: '', image: DIR + 'or.gif', shape: 'image', group: '".$this->epc->name."', color: 'gray', level: ".$level."}\n";
			case "and":		 return "{id: '".$nodeID."', label: '', image: DIR + 'and.gif', shape: 'image', group: '".$this->epc->name."', color: 'gray', level: ".$level."}\n";
		}
	}
	
	private function addEdgesToVisualization() {
		foreach ( $this->epc->edges as $edge ) {
			foreach ( $edge as $sourceID => $targetID ) {
				$edgeCode = "{from: '".$sourceID."', to: '".$targetID."', arrows:'to', style: 'arrow', color: 'gray'}\n";
				array_push($this->insertedEdges, $edgeCode);
			}	
		}
	}
	
}
?>