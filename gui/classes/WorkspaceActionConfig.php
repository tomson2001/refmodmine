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
			"isUploadable"	=> true,
			"Descriptions"	=> array(
				"Default"			=> "Default",
				"WI2015-Clustering" => "WI2015-Clustering",
				"Corpus-Metrics"	=> "Corpus-Metrics"
			)
		),
			
		"valueseries"	=> array(
				"Name"			=> "Value Series",
				"FileExtension"	=> "csv",
				"Icon"			=> "glyphicon glyphicon-sort-by-attributes",
				"OpenWith"		=> "workspaceCSVViewer",
				"isUploadable"	=> true,
				"Descriptions"	=> array(

				)
		),
			
		"matrix"	=> array(
				"Name"			=> "Matrix",
				"FileExtension"	=> "csv",
				"Icon"			=> "glyphicon glyphicon-transfer",
				"OpenWith"		=> "workspaceCSVViewer",
				"isUploadable"	=> false,
				"Descriptions"	=> array(
					"correlation" => "Empirical Correlation Coefficients"
				)
		),
		
		"simmatrix"	=> array(
			"Name"			=> "Similarity Matrix",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-transfer",
			"OpenWith"		=> "workspaceCSVViewer",
			"isUploadable"	=> true,
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
			"isUploadable"	=> true,
			"Descriptions"	=> array(
				"identical" 		=> "Identical Mapping"
			)
		),
		
		"epctext"	=> array(
			"Name"			=> "EPC-Text",
			"FileExtension"	=> "txt",
			"Icon"			=> "glyphicon glyphicon-text-color",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"Descriptions"	=> array(
				"default" 		=> "-"
			)
		),
		
		"cluster"	=> array(
			"Name"			=> "Model Clusters",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-equalizer",
			"OpenWith"		=> "workspaceCSVViewer",
			"isUploadable"	=> false,
			"Descriptions"	=> array(
				"hierarchical" 		=> "hierarchical",
				"PAM"				=> "PAM"
			)
		),
		
		"dendogram"	=> array(
			"Name"			=> "Dendogram",
			"FileExtension"	=> "svg",
			"Icon"			=> "glyphicon glyphicon-indent-right",
			"OpenWith"		=> "workspaceImgViewer",
			"isUploadable"	=> true,
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
			"isUploadable"	=> false,
			"Descriptions"	=> array(
				"German" 		=> "German",
				"English"		=> "English",
				"Functions"	=> "Functions",
				"Events"	=> "Events",
				"All"		=> "Functions, Events"
			)
		),
			
		"model"	=> array(
			"Name"			=> "EPC",
			"FileExtension"	=> "epml",
			"Icon"			=> "glyphicon glyphicon-picture",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"Descriptions"	=> array(
				"noevents" 		=> "without events",
				"refmod"		=> "Reference model",
				"li"			=> "approach of li",
				"fromPNML"		=> "transformed from petri net"
			)
		),
			
		"pnml"	=> array(
			"Name"			=> "PetriNet",
			"FileExtension"	=> "pnml",
			"Icon"			=> "glyphicon glyphicon-picture",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"Descriptions"	=> array(
			)
		),
			
		"bpmn"	=> array(
			"Name"			=> "BPMN Model",
			"FileExtension"	=> "bpmn",
			"Icon"			=> "glyphicon glyphicon-picture",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"Descriptions"	=> array(
			)
		),
			
		"behaveprofile"	=> array(
			"Name"			=> "Behavioural Profile",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-road",
			"OpenWith"		=> "workspaceCSVViewer",
			"isUploadable"	=> false,
			"Descriptions"	=> array(
				"Basic" 		=> "Basic",
				"Causal" 		=> "Causal",
				"Tree" 			=> "Tree",
				"Net"			=> "Net",
				"Unfolding"		=> "Unfolding"
			)
		),
			
		"nlptags"	=> array(
			"Name"			=> "NLP Tags",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-comment",
			"OpenWith"		=> "workspaceCSVViewerHeadingTop",
			"isUploadable"	=> false,
			"Descriptions"	=> array(
				"English"	=> "English",
				"German"	=> "German"
			)
		),
			
		"labels"	=> array(
			"Name"			=> "Node Labels",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-th-list",
			"OpenWith"		=> "workspaceCSVViewerHeadingTop",
			"isUploadable"	=> false,
			"Descriptions"	=> array(
				"standard"	=> "Standard"
			)
		),
			
		"processlog"	=> array(
			"Name"			=> "Process Log",
			"FileExtension"	=> "mxml",
			"Icon"			=> "glyphicon glyphicon-menu-hamburger",
			"OpenWith"		=> null,
			"isUploadable"	=> true,
			"Descriptions"	=> array(
		
			)
		),
			
		"matching"	=> array(
			"Name"			=> "Matching",
			"FileExtension"	=> "zip",
			"Icon"			=> "glyphicon glyphicon-random",
			"OpenWith"		=> null,
			"isUploadable"	=> true,
			"Descriptions"	=> array(
				"rmm-nscm"	=> "N-Ary Semantic Cluster Matching"
			)
		),
			
		"models"	=> array(
			"Name"			=> "EPCs",
			"FileExtension"	=> "epml",
			"Icon"			=> "glyphicon glyphicon-picture",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"Descriptions"	=> array(
				"German-to-English" 	=> "German-to-English",
				"German-to-French" 		=> "German-to-French",
				"German-to-Italian" 	=> "German-to-Italian",
				"English-to-Dutch"		=> "English-to-Dutch", 
				"English-to-German" 	=> "English-to-German",
				"French-to-English"		=> "French-to-English",
				"French-to-German"		=> "French-to-German",
				"Italian-to-German"		=> "Italian-to-German",
				"Italian-to-English"	=> "Italian-to-English",
				"Dutch-to-English"		=> "Dutch-to-English"
			)
		),
		
		"translationreport"	=> array(
			"Name"			=> "Translation Report",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-text-color",
			"OpenWith"		=> "workspaceCSVViewerHeadingTop",
			"isUploadable"	=> false,
			"Descriptions"	=> array(
				"German-to-English" 	=> "German-to-English",
				"German-to-French" 		=> "German-to-French",
				"German-to-Italian" 	=> "German-to-Italian",
				"English-to-Dutch"		=> "English-to-Dutch", 
				"English-to-German" 	=> "English-to-German",
				"French-to-English"		=> "French-to-English",
				"French-to-German"		=> "French-to-German",
				"Italian-to-German"		=> "Italian-to-German",
				"Italian-to-English"	=> "Italian-to-English",
				"Dutch-to-English"		=> "Dutch-to-English"
			)
		)
			
	);
	
	public $userDependencyParams = array("INPUT_TEXT", "SELECT_ONE_MODEL", "SELECT_ONE_METRICS", "SELECT_ONE_VALUE_SERIES", "SELECT_ONE_SIMMATRIX", "SELECT_ONE_MAPPING", "SELECT_ONE_PNML");

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
	 * An @ before the param name means, that the it will produce the form "value" instead of "paramName=value" 
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
					"Default" => "EVENTS FUNCTIONS AND_SPLITS AND_JOINS XOR_SPLITS XOR_JOINS OR_SPLITS OR_JOINS CONNECTORS NODES ARCS DIAMETER DENSITY_1 COEFFICIENT_OF_CONNECTIVITY",
					"WI2015-Clustering" => "start_events ARCS DENSITY_1 COEFFICIENT_OF_CONNECTIVITY",
					"Corpus-Metrics" => "start_events internal_events end_events events functions and_splits and_joins xor_splits xor_joins or_splits or_joins connectors nodes arcs density_1 density_2 coefficient_of_connectivity coefficient_of_network_complexity cyclomatic_number avg_connector_degree max_connector_degree separability sequentiality depth mismatch heterogeneity token_splits control_flow_complexity join_complexity weighted_coupling"                
				)
			)
		),
		
		"CONVERTER" => array(
				"Name"			=> "Convert Model File",
				"CodeBase"		=> "JAVA",
				"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
				"EmbedInPHP"	=> true,
				"Literature"	=> array(
						"todo"
				),
				"Parameters"	=> array(
						"CLI"				=> null,
						"CONVERT_EPC"		=> null,
						"INPUT_DATA" 		=> "INPUT_TEXT",
						"OUTPUT_DATA"		=> "INPUT_TEXT",
						"outputFormat"		=> array(
								"AML"  => "AML",
								"EPML" => "EPML"
						)
				)
		),
		
		"CREATE_REFERENCE_MODEL_LI" => array(
			"Name"			=> "Approach of Li",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature"	=> array(
				"Li, Li et al.: Dissertation (TODO)"
			),
			"Parameters"	=> array(
				"CLI"				=> null,
				"CREATE_REFERENCE_MODEL"	=> null,
				"INPUT_DATA" 		=> "CONST_WORKSPACE_EPML",
				"OUTPUT_DATA"		=> "CONST_WORKSPACE_EPML.model.refmod.li",
				"METHOD"			=> "LI"
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
		
		"CALCULATE_BEHAVIOURAL_PROFILE" => array(
			"Name"			=> "Calculate Behavioural Profile",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature"	=> array(
				"todo"
			),
			"Parameters"	=> array(
				"CLI"				=> null,
				"CALCULATE_BEHAVIOURAL_PROFILE"	=> null,
				"INPUT_DATA" 		=> "CONST_WORKSPACE_EPML",
				"OUTPUT_DATA"		=> "CONST_WORKSPACE_EPML.behaveprofile.%TYPE%.%CREATOR%",
				"TYPE"	=> array(
					"Basic" => "BASIC",
					"Causal" => "CAUSAL"
				),
				"CREATOR"	=> array(
					"Tree" 		=> "TREE",
					"Net"		=> "NET",
					"Unfolding"	=> "UNFOLDING"
				)
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
		
		"REMOVE_EVENTS" => array(
			"Name"			=> "Remove Events",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature"	=> array(
				"todo"
			),
			"Parameters"	=> array(
				"CLI"				=> null,
				"REMOVE_EVENTS"		=> null,
				"INPUT" 		=> "CONST_WORKSPACE_EPML",
				"OUTPUT"		=> "CONST_WORKSPACE_EPML.model.noevents",
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
			"Name"			=> "Further Similarities/Distances Measures",
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
				"needs a distance matrix as input"
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
				"INPUT_TYPE"	=> "DISSIMILARITY_MATRIX",
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
		),
			
		"NLP_TAGGING" => array(
			"Name"			=> "NLP Tagging",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIModelNLPTagger.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"none"
			),
			"Parameters"	=> array(
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.nlptags.%language%",
				"language"		=> array("English" => "en", "German" => "de"),
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
		
		"LABEL_EXTRACTION" => array(
			"Name"			=> "Label Extraction",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIModelLabelExtractor.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"none"
			),
			"Parameters"	=> array(
					"input"			=> "CONST_WORKSPACE_EPML",
					"output"		=> "CONST_WORKSPACE_EPML.labels.standard",
					"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
			
		"MATCHING_NSCM" => array(
			"Name"			=> "N-Ary Semantic Cluster Matching",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLINArySemanticClusterMatching.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"The Process Matching Contest 2013 - Thaler, Hake, Fettke, Loos: RefMod-Miner/NSCM"
			),
			"Parameters"	=> array(
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.matching.rmm-nscm",
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
			
		"MODEL_TRANSLATION" => array(
			"Name"			=> "Model Translation",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIModelTranslator.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"todo"
			),
			"Parameters"	=> array(
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.models.%language_combination%",
				"reportCSV"		=> "CONST_WORKSPACE_EPML.translationreport.%language_combination%",
				"language_combination" => array(
					"German-to-English" 	=> "de-en",
					"German-to-French" 		=> "de-fr",
					"German-to-Italian" 	=> "de-fr",
					"English-to-Dutch"		=> "en-nl", 
					"English-to-German" 	=> "en-de",
					"French-to-English"		=> "fr-en",
					"French-to-German"		=> "fr-de",
					"Italian-to-German"		=> "it-de",
					"Italian-to-English"	=> "it-en",
					"Dutch-to-English"		=> "nl-en"
				),
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
		
		"CONVERT_PNML2EPML" => array(
			"Name"			=> "Convert PMNL to EPML",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIConvertPNML2EPML.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"none"
			),
// 			"Parameters"	=> array(
// 					"input"			=> "SELECT_ONE_PNML",
// 					"output"		=> "CONST_WORKSPACE_EPML.model.%SELECT_ONE_PNML@input%.fromPNML",
// 					"notification"	=> "CONST_SESSION_E_MAIL"
// 			)
			"Parameters"	=> array(
					"input"			=> "INPUT_TEXT",
					"output"		=> "INPUT_TEXT",
					"notification"	=> "INPUT_TEXT"
			)
		),
		
		"CONVERT_BPMN2EPML" => array(
			"Name"			=> "Convert BPMN to EPML",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLIConvertBPMN2EPML.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"none"
			),
			"Parameters"	=> array(
					"input"			=> "INPUT_TEXT",
					"output"		=> "INPUT_TEXT",
					"notification"	=> "INPUT_TEXT"
			)
		),
		
		"CORRELATION_CALCULATOR" => array(
			"Name"			=> "Calculate Empirical Correlation",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLICorrelationCalculator.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"Calculates the Empirical Correlation Coefficient based on value series."
			),
			"Parameters"	=> array(
					"input"			=> "SELECT_ONE_VALUE_SERIES",
					"output"		=> "CONST_WORKSPACE_EPML.matrix.correlation",
					"notification"	=> "CONST_SESSION_E_MAIL"
			)
		)

	);
	
	// Available Functionalities operating on the whole workspace
	public $workspaceActions = array(
			
		"General Analysis" => array(
			"CALCULATE_METRICS",
			"CALCULATE_FEATURE_VECTORS",
			//"REMOVE_EVENTS", // is ready, but buggy, BUG reported 326
			//"CALCULATE_BEHAVIOURAL_PROFILE" // is ready, bug not works at the moment (seems not being fully implemented)
		),
			
		"Process Matching" => array(
			"MATCHING_NSCM"
		),	
					
		"Process Model Similarity" => array(
			"CALCULATE_SIMILARITY_SSBOCAN",
			"CALCULATE_SIMILARITY_LMS",
			"CALCULATE_SIMILARITY_FBSE",
			"CALCULATE_SIMILARITY_POCNAE",
			"CALCULATE_SIMILARITY_GEDS",
			"CALCULATE_SIMILARITY_AMAGED",
			"CALCULATE_SIMILARITY_CF",
			"CALCULATE_SIMDIST"
		),
			
		"Process Model Clustering" => array(
			"CLUSTER_MODELS_BASED_ON_DIST" // is ready, but buggy, BUG reported 325
		),
			
		"Reference Model Mining" => array(
			//"CREATE_REFERENCE_MODEL_LI" // returns null
		),
			
		"Natural Language Processing" => array(
			"NLP_TAGGING",
			"LABEL_EXTRACTION",
			"MODEL_TRANSLATION"
			//"EXTRACT_VOCABULARY" // is ready, but an buggy, BUG reported 327
		),
		
		"Further Tools" => array(
			"CORRELATION_CALCULATOR"
			//"CONVERT_PNML2EPML"
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
		if ( $fileParams[1] == "custom" ) {
			unset($fileParams[1]);
			return implode(", ", $fileParams);
		}
		$descriptions = array();
		foreach ( $fileParams as $descriptionKey ) {
			$description = $this->getFileTypeDescription($fileType, $descriptionKey);
			array_push($descriptions, $description);
		}
		return implode(", ", $descriptions);
	}
	
	public function getFileTypeDescription($fileType, $descriptionKey) {
		if ( isset($this->fileTypeInfos[$fileType]) ) {
			return isset($this->fileTypeInfos[$fileType]["Descriptions"][$descriptionKey]) ? $this->fileTypeInfos[$fileType]["Descriptions"][$descriptionKey] : $descriptionKey;
		}
		return null;
	}
	
}
?>