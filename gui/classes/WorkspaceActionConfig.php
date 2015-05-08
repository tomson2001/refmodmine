<?php
/**
 * WorkspaceActionConfig
 * 
 * Here you are able to define new (CLI) functionalities and include them as a workspace
 * action. Therefore, you need to make entries in $actions (define the action), 
 * $fileTypeInfos (define the result) and $workspaceActions (active the action in the workspace) 
 * 
 * @author Tom Thaler <tom.thaler@dfki.de>
 *
 */
class WorkspaceActionConfig {
	
	public $execCodeBases = array(
		"JAVA" 	=> "java -jar",
		"PHP"	=> "php"
	);
	
	public $fileTypeInfos = array(
		
		"metrics"	=> array(
			"Name"			=> "Metrics Table",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-th",
			"OpenWith"		=> "workspaceCSVViewer",
			"Descriptions"	=> array(
				"Default"		=> "Default",
				"WI2015-Clustering" => "WI2015-Clustering" 
			)
		),
		
		"simmatrix"	=> array(
			"Name"			=> "Similarity Matrix",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-transfer",
			"OpenWith"		=> "workspaceCSVViewer",
			"Descriptions"	=> array(
				
				// the PHP RMM things
				"ssbocan" 		=> "Common Activity Names",
				"lms" 			=> "Label Matching Similarity",
				"fbse" 			=> "Feature Based Similarity Estimation",
				"pocnae" 		=> "Common Nodes and Edges",
				"geds" 			=> "Graph Edit Distance Similarity",
				"amaged" 		=> "Activity Matching and Graph Edit Distance",
				"cf" 			=> "Causal Footprints",
				
				// the JAVA RMM things
				"Percentage-of-common-nodes" 			=> "Percentage of common nodes",
				"Percentage-of-common-nodes-and-edges" 	=> "Percentage of common nodes and edges",
				"Graph-Edit-Distance" 					=> "Graph Edit Distance",
				"Causal-Footprints" 					=> "Causal Footprints",
				"LCS-of-Traces" 						=> "Longest Common Subsequece of Traces",
				"Trace-(behaviour)" 					=> "Trace (behaviour)",
				"Terminology-(with-frequencies)" 		=> "Terminology (with frequencies)",
				"Terminology-(without-frequencies)" 	=> "Terminology (without frequencies)",
				"Ordermatrix-similarity" 				=> "Ordermatrix similarity",
				"Fragment-Sim-Count"					=> "Fragment Similarity Count",
				"Fragment-Sim-Size" 					=> "Fragment Similarity Size",
				"All" 									=> "All",
				"Similarity" 							=> "Similarity",
				"Distance"								=> "Distance",
					
				"both" 									=> "with headings",
				"row" 									=> "row headings only",
				"column" 								=> "column headings only",
				"off" 									=> "without headings",
			)
		),
		
		"featurevectors"	=> array(
			"Name"			=> "Feature Vectors",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-sort-by-attributes",
			"OpenWith"		=> "workspaceCSVViewer",
			"Descriptions"	=> array(
				"identical" 		=> "Identical Mapping"
			)
		),
		
		"epctext"	=> array(
			"Name"			=> "EPC-Text",
			"FileExtension"	=> "txt",
			"Icon"			=> "glyphicon glyphicon-text-color",
			"OpenWith"		=> null,
			"Descriptions"	=> array(
				"default" 		=> "-"
			)
		),
		
		"cluster"	=> array(
			"Name"			=> "Model Clusters",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-equalizer",
			"OpenWith"		=> "workspaceCSVViewer",
			"Descriptions"	=> array(
				"hierarchical" 		=> "hierarchical",
				"PAM"				=> "PAM"
			)
		),
		
		"dendogram"	=> array(
			"Name"			=> "Dendogram",
			"FileExtension"	=> "svg",
			"Icon"			=> "glyphicon glyphicon-indent-right",
			"OpenWith"		=> null,
			"Descriptions"	=> array(
				"hierarchical" 		=> "hierarchical",
				"PAM"				=> "PAM"
			)
		),
		
		"vocabulary"	=> array(
			"Name"			=> "Vocabulary",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-list-alt",
			"OpenWith"		=> "workspaceCSVViewer",
			"Descriptions"	=> array(
				"German" 		=> "German",
				"English"		=> "English",
				"Functions"	=> "Functions",
				"Events"	=> "Events",
				"All"		=> "Functions, Events"
			)
		)
			
	);
	
	public $userDependencyParams = array("INPUT_TEXT", "SELECT_ONE_MODEL", "SELECT_ONE_METRICS", "SELECT_ONE_SIMMATRIX", "SELECT_ONE_MAPPING");

	/**
	 * Available functionalities
	 * 
	 * Constants:
	 *   CONST_SESSION_E_MAIL
	 *   CONST_WORKSPACE_EPML
	 *   INPUT_TEXT
	 *   SELECT_ONE_MODEL
	 *   SELECT_ONE_METRICS
	 *   SELECT_ONE_SIMMATRIX
	 *   SELECT_ONE_MAPPING
	 *   
	 * EmbedInPHP means that the command will be executed by a separate PHP script. This is necessary for the E-Mail notification.
	 * An @ before the param name means, that the it will produce the form "value" indead of "paramName=value" 
	 */
	public $actions = array(
		
		"CALCULATE_METRICS" => array(
			"Name"			=> "Calculate Metrics",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature"	=> array(
				"todo"
			),
			"Parameters"	=> array(
				"CLI"				=> null,
				"CALCULATE_METRICS"	=> null,
				"INPUT_DATA" 		=> "CONST_WORKSPACE_EPML",
				"OUTPUT_DATA"		=> "CONST_WORKSPACE_EPML.metrics.%METRICS%", 
				"METRICS"			=> array(
					"Default" => "EVENTS FUNCTIONS AND_SPLITS AND_JOINS XOR_SPLITS XOR_JOINS OR_SPLITS OR_JOINS CONNECTORS NODES ARCS DIAMETER DENSITY_1 COEFFICIENT_CONNECTIVITY",
					"WI2015-Clustering" => "NODES ARCS DENSITY_1 COEFFICIENT_CONNECTIVITY"
				)
			)
		),
		
		"CALCULATE_FEATURE_VECTORS" => array(
			"Name"			=> "Calculate Feature Vectors",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature"	=> array(
				"todo"
			),
			"Parameters"	=> array(
				"CLI"				=> null,
				"CALCULATE_FEATURE_VECTORS"	=> null,
				"INPUT_DATA" 		=> "CONST_WORKSPACE_EPML",
				"OUTPUT_DATA"		=> "CONST_WORKSPACE_EPML.featurevectors.identical",
			)
		),
		
		"EPC_TO_TEXT" => array(
			"Name"			=> "EPC to Text",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature"	=> array(
				"todo"
			),
			"Parameters"	=> array(
				"CLI"				=> null,
				"EPC_TO_TEXT"	=> null,
				"INPUT_DATA" 		=> "CONST_WORKSPACE_EPML",
				"OUTPUT_DATA"		=> "CONST_WORKSPACE_EPML.epctext.default",
			)
		),
		
		"EXTRACT_VOCABULARY" => array(
			"Name"			=> "Extract Vocabulary",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature"	=> array(
				"todo"
			),
			"Parameters"	=> array(
				"CLI"			=> null,
				"EXTRACT_VOCABULARY"	=> null,
				"EPCS" 		=> "CONST_WORKSPACE_EPML",
				"@NODES"			=> array(
					"All"		=> "ALL",
					"Events"	=> "EVENTS",
					"Functions"	=> "FUNCTIONS"
					
				),
				"@LANGUAGE"		=> array(
					"English"	=> "ENG",
					"German"	=> "GER"
				),
				"OUTPUT_DATA"		=> "CONST_WORKSPACE_EPML.vocabulary.%@NODES%.%@LANGUAGE%"
			)
		),
		
		"CALCULATE_SIMDIST" => array(
			"Name"			=> "Calculate Similarities/Distances",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature"	=> array(
				"todo"
			),
			"Parameters"	=> array(
				"CLI"				=> null,
				"CALCULATE_SIMILARITY"	=> null,
				"INPUT_DATA" 		=> "CONST_WORKSPACE_EPML",
				//"MAPPING"			=> "SELECT_ONE_MAPPING",
				"SIMILARITY_MEASURES" => array(
					"Percentage-of-common-nodes" 			=> "Percentage_of_common_nodes",
					"Percentage-of-common-nodes-and-edges" 	=> "Percentage_of_common_nodes_and_edges",
					"Graph-Edit-Distance" 					=> "Graph_Edit_Distance",
					"Causal-Footprints" 					=> "Causal_Footprints",
					"LCS-of-Traces" 						=> "LCS_of_Traces",
					"Trace-(behaviour)" 					=> "Trace_(behaviour)",
					"Terminology-(with-frequencies)" 		=> "Terminology_(with_frequencies)",
					"Terminology-(without-frequencies)" 	=> "Terminology_(without_frequencies)",
					"Ordermatrix-similarity" 				=> "Ordermatrix_similarity",
					"Fragment-Sim-Count" 					=> "Fragment_Sim_Count",
					"Fragment-Sim-Size" 					=> "Fragment_Sim_Size",
					"All" => "ALL"
				),
				"GROUP_BY"			=> "SIMILARITY_MEASURE", 
				"MEASURE_TYPE"		=> array(
					"Similarity"		=> "SIMILARITY",
					"Distance"			=> "DISTANCE"
				),
				"VARIABLE_NAMES"	=> array(
					"both" => "BOTH",
					"row" => "ROW",
					"column" => "COLUMN",
					"off" => "OFF",
				),
				"OUTPUT_DATA"		=> "CONST_WORKSPACE_EPML.simmatrix.%SIMILARITY_MEASURES%.%MEASURE_TYPE%.%VARIABLE_NAMES%"
			)
		),
		
		"CLUSTER_MODELS_BASED_ON_DIST" => array(
			"Name"			=> "Cluster Models based on Distance Matrix",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature"	=> array(
				"todo"
			),
			"Parameters"	=> array(
				"CLI"			=> null,
				"CLUSTER_MODELS"	=> null,
				"INPUT_DATA" 	=> "SELECT_ONE_SIMMATRIX",
				"METHOD"		=> array(
					"hierarchical"	=> "HCLUST",
					"PAM"			=> "PAM"
				),
				"DENDROGRAM"		=> "CONST_WORKSPACE_EPML.dendogram.%METHOD%",
				"SIZE"			=> array(
					"Auto" => "AUTO", "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "10" => "10"  
				),
				"CLUSTER_DISTANCE" => array(
					"WARD_D"	=> "WARD_D"
				),
				"INPUT_TYPE"	=> "SIMILARITY_MATRIX",
				"OUTPUT_DATA"	=> "CONST_WORKSPACE_EPML.cluster.%METHOD%",
				"EVALUATION"	=> array(
					"all"	=> "ALL"
				)
			)
		),
		
		"CALCULATE_SIMILARITY_SSBOCAN" => array(
			"Name"			=> "Common Activity Names",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIModelSimilarity.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"Akkiraju et al. 2010: Discovering Business Process Similarities: An Empirical Study with SAP Best Practice Business Processes"
			),
			"Parameters"	=> array(
				"measure"		=> "ssbocan",
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.simmatrix.ssbocan",
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
			
		"CALCULATE_SIMILARITY_LMS" => array(
			"Name"			=> "Label Matching Similarity",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIModelSimilarity.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"Dijkman et al. 2011: Similarity of business process models: metrics and evaluation"
			),
			"Parameters"	=> array(
				"measure"		=> "lms",
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.simmatrix.lms",
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
			
		"CALCULATE_SIMILARITY_FBSE" => array(
			"Name"			=> "Feature Based Similarity Estimation",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIModelSimilarity.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"Yan et al. 2010: Fast Business Process Similarity Search with Feature-Based Similarity Estimation"
			),
			"Parameters"	=> array(
				"measure"		=> "fbse",
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.simmatrix.fbse",
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
			
		"CALCULATE_SIMILARITY_POCNAE" => array(
			"Name"			=> "Common Nodes and Edges",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIModelSimilarity.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"Minor et al. 2007: Representation and Structure-Based Similarity Assessment for Agile Workflows"
			),
			"Parameters"	=> array(
				"measure"		=> "pocnae",
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.simmatrix.pocnae",
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
			
		"CALCULATE_SIMILARITY_GEDS" => array(
			"Name"			=> "Graph Edit Distance Similarity",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIModelSimilarity.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"Dijkman et al. 2009: Graph Matching Algorithms for Business Process Model Similarity Search"
			),
			"Parameters"	=> array(
				"measure"		=> "geds",
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.simmatrix.geds",
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
			
		"CALCULATE_SIMILARITY_AMAGED" => array(
			"Name"			=> "Activity Matching and Graph Edit Distance",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIModelSimilarity.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"La Rosa et al. 2010: Merging Business Process Models"
			),
			"Parameters"	=> array(
				"measure"		=> "amaged",
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.simmatrix.amaged",
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
			
		"CALCULATE_SIMILARITY_CF" => array(
			"Name"			=> "Causal Footprints",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIModelSimilarity.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"Dongen et al. 2008: Measuring Similarity between Business Process Models"
			),
			"Parameters"	=> array(
				"measure"		=> "cf",
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.simmatrix.cf",
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		)

	);
	
	// Available Functionalities operating on the whole workspace
	public $workspaceActions = array(
			
		"Tools" => array(
			"CALCULATE_METRICS",
			"CALCULATE_FEATURE_VECTORS",
			"CALCULATE_SIMDIST",
			"EXTRACT_VOCABULARY"
			//"CLUSTER_MODELS_BASED_ON_DIST"
		),
			
		"Process Model Similarity" => array(
			"CALCULATE_SIMILARITY_SSBOCAN",
			"CALCULATE_SIMILARITY_LMS",
			"CALCULATE_SIMILARITY_FBSE",
			"CALCULATE_SIMILARITY_POCNAE",
			"CALCULATE_SIMILARITY_GEDS",
			"CALCULATE_SIMILARITY_AMAGED",
			"CALCULATE_SIMILARITY_CF"
				
		),
			
		"Process Matching" => array(
			
		)
	);
	
	
	public function __construct() {
		
	}
	
	public function getActionData($actionName) {
		if ( isset($this->actions[$actionName]) ) return $this->actions[$actionName];
		return false;
	}
	
	public function getFileTypeName($fileType) {
		if ( isset($this->fileTypeInfos[$fileType]) ) return $this->fileTypeInfos[$fileType]["Name"];
		return null;
	}
	
	public function getFileTypeExtension($fileType) {
		if ( isset($this->fileTypeInfos[$fileType]) ) return $this->fileTypeInfos[$fileType]["FileExtension"];
		return null;
	} 
	
	public function getFileTypeIcon($fileType) {
		if ( isset($this->fileTypeInfos[$fileType]) ) return $this->fileTypeInfos[$fileType]["Icon"];
		return null;
	}
	
	public function getFileTypeOpenMethod($fileType) {
		if ( isset($this->fileTypeInfos[$fileType]) ) return $this->fileTypeInfos[$fileType]["OpenWith"];
		return null;
	}
	
	public function getFileTypeDescriptions($fileType, $fileParams) {
		$descriptions = array();
		foreach ( $fileParams as $descriptionKey ) {
			$description = $this->getFileTypeDescription($fileType, $descriptionKey);
			array_push($descriptions, $description);
		}
		return implode(", ", $descriptions);
	}
	
	public function getFileTypeDescription($fileType, $descriptionKey) {
		if ( isset($this->fileTypeInfos[$fileType]) ) return $this->fileTypeInfos[$fileType]["Descriptions"][$descriptionKey];
		return null;
	}
	
}
?>