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
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"Default"			=> "Default",
				"WI2015-Clustering" => "WI2015-Clustering",
				"Corpus-Metrics"	=> "Corpus-Metrics"
			),
			"Specification" => ""
		),
			
		"valueseries"	=> array(
				"Name"			=> "Value Series",
				"FileExtension"	=> "csv",
				"Icon"			=> "glyphicon glyphicon-sort-by-attributes",
				"OpenWith"		=> "workspaceCSVViewer",
				"isUploadable"	=> true,
				"uploadAction"	=> null,
				"Descriptions"	=> array(

				),
				"Specification" => "The value series (csv) contains up to 9 different numeric value series in a vertical representations. The column 
									headers should be \"series1\", \"series2\" ... up to \"series9\". The numeric values are in the following rows
									(e.g. 15.3). The cell delimiter is semicolon. The row delimiter is \\n. An example would be:<br /><br />
								<table border=\"1\" cellpadding=\"2\" cellspacing=\"2\"><tr><td>series1</td><td>series2</td><td>series3</td></tr>
								<tr><td>1.23</td><td>15.6</td><td>1.5</td></tr>
								<tr><td>5.84</td><td>2.75</td><td>1.89</td></tr>
								<tr><td>70.53</td><td>135.5</td><td>5.3</td></tr>
								</table>"
		),
			
		"matrix"	=> array(
				"Name"			=> "Matrix",
				"FileExtension"	=> "csv",
				"Icon"			=> "glyphicon glyphicon-transfer",
				"OpenWith"		=> "workspaceCSVViewer",
				"isUploadable"	=> false,
				"uploadAction"	=> null,
				"Descriptions"	=> array(
					"correlation" => "Empirical Correlation Coefficients"
				),
				"Specification" => ""
		),
		
		"simmatrix"	=> array(
			"Name"			=> "Similarity Matrix",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-transfer",
			"OpenWith"		=> "workspaceCSVViewer",
			"isUploadable"	=> true,
			"uploadAction"	=> null,
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
			),
			"Specification" => "A Similarity Matrix (csv) contains similarity values between 0 and 100 between process models. The row and column
								headers contain the model names. Thus, the first column of the first row is empty. The cell delimiter is a semicolon. 
								An example would be:<br /><br />
								<table border=\"1\" cellpadding=\"2\" cellspacing=\"2\"><tr><td></td><td>Model A</td><td>Model B</td><td>Model C</td></tr>
								<tr><td>Model A</td><td>100</td><td>15.3</td><td>70.6</td></tr>
								<tr><td>Model B</td><td>15.3</td><td>100</td><td>50.89</td></tr>
								<tr><td>Model C</td><td>70.6</td><td>50.89</td><td>100</td></tr>
								</table>"
		),
		
		"featurevectors"	=> array(
			"Name"			=> "Feature Vectors",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-sort-by-attributes",
			"OpenWith"		=> "workspaceCSVViewer",
			"isUploadable"	=> true,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"identical" 		=> "Identical Mapping"
			),
			"Specification" => "The feature vectors csv describes the occurence of activity nodes in particular models. The row header contains all 
								activity labels of all models, the column header contains the models. The values are either 0 (activity is not the model)
								or 1 (activity is in the model). Line delimiter is \\n and cell delimiter is semicolon. An example would be:<br /><br />
								<table border=\"1\" cellpadding=\"2\" cellspacing=\"2\"><tr><td></td><td>Node 1</td><td>Node 2</td><td>Node 3</td></tr>
								<tr><td>Model A</td><td>1</td><td>0</td><td>1</td></tr>
								<tr><td>Model B</td><td>1</td><td>1</td><td>1</td></tr>
								<tr><td>Model C</td><td>0</td><td>1</td><td>1</td></tr>
								</table>"
		),
		
		"epctext"	=> array(
			"Name"			=> "EPC-Text",
			"FileExtension"	=> "txt",
			"Icon"			=> "glyphicon glyphicon-text-color",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"default" 		=> "-"
			),
			"Specification" => ""
		),
		
		"cluster"	=> array(
			"Name"			=> "Model Clusters",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-equalizer",
			"OpenWith"		=> "workspaceCSVViewer",
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"hierarchical" 		=> "hierarchical",
				"PAM"				=> "PAM"
			),
			"Specification" => ""
		),
		
		"dendogram"	=> array(
			"Name"			=> "Dendogram",
			"FileExtension"	=> "svg",
			"Icon"			=> "glyphicon glyphicon-indent-right",
			"OpenWith"		=> "workspaceImgViewer",
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"hierarchical" 		=> "hierarchical",
				"PAM"				=> "PAM"
			),
			"Specification" => ""
		),
		
		"vocabulary"	=> array(
			"Name"			=> "Vocabulary",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-list-alt",
			"OpenWith"		=> "workspaceCSVViewer",
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"German" 		=> "German",
				"English"		=> "English",
				"Functions"	=> "Functions",
				"Events"	=> "Events",
				"All"		=> "Functions, Events"
			),
			"Specification" => ""
		),
			
		"model"	=> array(
			"Name"			=> "EPC",
			"FileExtension"	=> "epml",
			"Icon"			=> "glyphicon glyphicon-picture",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"noevents" 		=> "without events",
				"refmod"		=> "Reference model",
				"li"			=> "approach of li",
				"fromPNML"		=> "transformed from petri net",
				"fromText"		=> "mined from textual description",
				"epml"			=> "",
			),
			"Specification" => ""
		),
			
		"pnml"	=> array(
			"Name"			=> "PetriNet",
			"FileExtension"	=> "pnml",
			"Icon"			=> "glyphicon glyphicon-picture",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
			)
		),
			
		"bpmn"	=> array(
			"Name"			=> "BPMN Model",
			"FileExtension"	=> "bpmn",
			"Icon"			=> "glyphicon glyphicon-picture",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"fromText"				=> "mined from text",
				"bpmn"		=> ""
			),
			"Specification" => ""
		),
			
		"behaveprofile"	=> array(
			"Name"			=> "Behavioural Profile",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-road",
			"OpenWith"		=> "workspaceCSVViewer",
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"Basic" 		=> "Basic",
				"Causal" 		=> "Causal",
				"Tree" 			=> "Tree",
				"Net"			=> "Net",
				"Unfolding"		=> "Unfolding"
			),
			"Specification" => ""
		),
			
		"nlptags"	=> array(
			"Name"			=> "NLP Tags",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-comment",
			"OpenWith"		=> "workspaceCSVViewerHeadingTop",
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"English"	=> "English",
				"German"	=> "German"
			),
			"Specification" => ""
		),
			
		"labels"	=> array(
			"Name"			=> "Node Labels",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-th-list",
			"OpenWith"		=> "workspaceCSVViewerHeadingTop",
			"isUploadable"	=> false,
			"uploadAction"	=> null,
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
			"uploadAction"	=> null,
			"Descriptions"	=> array(
		
			),
			"Specification" => "The MXML format is used for describing process logs. Details and the specification of the format can be 
								found here: <a href=\"http://www.processmining.org/logs/mxml\" target=\"_blank\">http://www.processmining.org/logs/mxml</a>"
		),
			
		"matching"	=> array(
			"Name"			=> "Matching",
			"FileExtension"	=> "zip",
			"Icon"			=> "glyphicon glyphicon-random",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"rmm-nscm"		=> "N-Ary Semantic Cluster Matching (PMMC2013)",
				"rmm-nhcm"		=> "N-Ary Homogeneity-based Cluster Matching (PMMC2015)",
				"rmm-map2015"	=> "Matching (PMC2015)"
			),
			"Specification" => ""
		),
			
		"rdfmatching"	=> array(
			"Name"			=> "Matching",
			"FileExtension"	=> "rdf",
			"Icon"			=> "glyphicon glyphicon-random",
			"OpenWith"		=> null,
			"isUploadable"	=> true,
			"uploadAction"	=> "doConvertUploadedRDFMatchingToXMLMatching",
			"Descriptions"	=> array(
				"rmm-nscm"		=> "N-Ary Semantic Cluster Matching (PMMC2013)",
				"rmm-nhcm"		=> "N-Ary Homogeneity-based Cluster Matching (PMMC2015)",
				"rmm-map2015"	=> "Matching (PMC2015)"
			),
			"Specification" => "Details on the rdf format used for process matching can found on the site of the Process Matching Contest 2015: 
								<a href=\"https://ai.wu.ac.at/emisa2015/contest.php\" target=\"_blank\">https://ai.wu.ac.at/emisa2015/contest.php</a>"
		),
			
		"xmlmatching"	=> array(
			"Name"			=> "Matching",
			"FileExtension"	=> "xml",
			"Icon"			=> "glyphicon glyphicon-random",
			"OpenWith"		=> null,
			"isUploadable"	=> true,
			"uploadAction"	=> null,
			"Descriptions"	=> array(
				"rmm-nscm"		=> "N-Ary Semantic Cluster Matching (PMMC2013)",
				"rmm-nhcm"		=> "N-Ary Homogeneity-based Cluster Matching (PMMC2015)",
				"rmm-map2015"	=> "Matching (PMC2015)"
			),
			"Specification" => "The datatype xml represents a matching between models. The XML format for saving matchings allows persisting single 
								or multiple matchings of arbitrary arity and complexity. The xml document is structured as follows:<br><br>
								&lt;?xml version=\"1.0\" encoding=\"UTF-8\"?&gt;<br>
								  &nbsp;&nbsp;&lt;matchings&gt;<br>
								    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;matching&gt;&lt;/matching&gt;<br>
								    &nbsp;&nbsp;&lt;/matching&gt;<br>
								    &nbsp;&nbsp;. . .<br>
								  &lt;/matchings&gt;<br><br>
								Each document has a root element called \"matchings\2. The children of this root element are matching
								elements. The root element has only elements of type matching as children. A matching element is
								structured as follows:<br><br>
								&lt;matching name=\"my_matching\"&gt;<br>
								&nbsp;&nbsp;&lt;epcs&gt;<br>
								&nbsp;&nbsp;. . .<br>
								&nbsp;&nbsp;&lt;/epcs&gt;<br>
								&nbsp;&nbsp;&lt;matches&gt;<br>
								&nbsp;&nbsp;. . .<br>
								&nbsp;&nbsp;&lt;/matches&gt;<br>
								&lt;/matching&gt;<br><br>
								A matching element has a name attribute. The name attribute is expected to contain an arbitrary
								valid string value (see XML specication http://www.w3.org/TR/REC-xml/). A matching element
								has two children &lt;epcs&gt; and &lt;matches&gt;. The &lt;epcs&gt; element can contain several &lt;epc&gt; elements
								representing the models involved in a matching. An &lt;epc&gt; element has an attribute epcDescription
								that represents a specic model. The &lt;epcs&gt; element is structured as follows:&lt;br&gt;&lt;br&gt;<br>
								&lt;epcs&gt;<br>
								&nbsp;&nbsp;&lt;epc epcDescription=\"MODEL_A\" /&gt;<br>
								&nbsp;&nbsp;&lt;epc epcDescription=\"MODEL_B\" /&gt;<br>
								&nbsp;&nbsp;. . .<br>
								&lt;/epcs&gt;<br><br>
								The &lt;matches&gt; element contains several &lt;match&gt; elements representing a matching's matches. The
								elements are structured es follows:<br><br>
								&lt;match refEpc=EPK_A value=\"0.5\" status=\"OPEN\" interpretation=\"SPECIALISATION\"&gt;<br>
								&nbsp;&nbsp;&lt;relationalProperties&gt; &lt;property name=\"NON_SYMMETRIC\"/&gt;<br>
								&nbsp;&nbsp;. . .<br>
								&nbsp;&nbsp;&lt;/relationalProperties&gt;<br>
								&nbsp;&nbsp;&lt;node nodeDescription=\"Antrag prüfen\" epcDescription=\"EPK_A&gt;<br>
								&nbsp;&nbsp;&lt;node nodeDescription=\"Antrag technisch prüfen\" epcDescription=\"EPK_B&gt;<br>
								&nbsp;&nbsp;&lt;node nodeDescription=\"Antrag kaufmännisch prüfen\" epcDescription=\"EPK_B/&gt;<br>
								&nbsp;&nbsp;. . .<br>
								&lt;/match&gt;<br><br>
								A &lt;match&gt; element has four optional attributes. The attribute 'refEpc' references an epc specied in
								an &lt;epc&gt; element. 'refEpc' is expected to describe a string value that matches an epcDescription value
								of an &lt;epc&gt; element. This attribute is used to represent 1:N matches. The attribute 'value' describes
								a value that can be assigned to a match. 'value' is expected to describe a double value as dened
								in the section for primitive datatypes. This attribute is used to assign a similarity value to a match.
								The attribute 'status' describes the state of a match. A match can be either open or closed. Hence
								the attribute 'status' expects the string value 'OPEN' or 'CLOSED'. The attribute 'interpretation'
								describes the semantic relation between the nodes. Supported interpretation values can be found in
								the description of the command 'MATCH'. The &lt;match&gt; element contains several &lt;node&gt; elements
								that describe the nodes involved in a match. Each &lt;node&gt; element has an attribute 'nodeDescription'
								that is expected to contain the nodes label/description and an attribute 'epcDescription' which is
								expected to describe a string value that matches an epcDescription value of an &lt;epc&gt; element.<br><br>
								Example:<br><br>
								&lt;?xml version=\"1.0\" encoding=\"UTF-8\"?&gt;<br>
								&lt;matchings&gt;<br>
								&nbsp;&nbsp;&lt;matching name=\"myMatching\"&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&lt;epcs&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;epc epcDescription=\"EPK_A\" /&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;epc epcDescription=\"EPK_B\" /&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&lt;/epcs&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&lt;matches&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;match refEpc=EPK_A value=\"0.5\" status=\"OPEN\" interpretation=\"SPECIALISATION\"&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;relationalProperties&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;property name=\"NON_SYMMETRIC\"/&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/relationalProperties&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;node nodeDescription=\"Antrag prüfen\" epcDescription=\"EPK_A&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;node nodeDescription=\"Antrag technisch prüfen\" epcDescription=\"EPK_B&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;node nodeDescription=\"Antrag kaufmännisch prüfen\" epcDescription=\"EPK_B/&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/match&gt;<br>
								&nbsp;&nbsp;&nbsp;&nbsp;&lt;/matches&gt;<br>
								&nbsp;&nbsp;&lt;/matching&gt;<br>
								&lt;/matchings&gt;"
		),
			
		"models"	=> array(
			"Name"			=> "EPCs",
			"FileExtension"	=> "epml",
			"Icon"			=> "glyphicon glyphicon-picture",
			"OpenWith"		=> null,
			"isUploadable"	=> false,
			"uploadAction"	=> null,
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
				"Dutch-to-English"		=> "Dutch-to-English",
				"fromText"				=> "mined from text"
			),
			"Specification" => ""
		),
		
		"translationreport"	=> array(
			"Name"			=> "Translation Report",
			"FileExtension"	=> "csv",
			"Icon"			=> "glyphicon glyphicon-text-color",
			"OpenWith"		=> "workspaceCSVViewerHeadingTop",
			"isUploadable"	=> false,
			"uploadAction"	=> null,
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
			),
			"Specification" => ""
		)
			
	);
	
	public $userDependencyParams = array("INPUT_TEXT", "INPUT_TEXTFIELD", "SELECT_ONE_MODEL", "SELECT_ONE_METRICS", "SELECT_ONE_VALUE_SERIES", "SELECT_ONE_SIMMATRIX", "SELECT_ONE_XML_MATCHING", "SELECT_ONE_PNML");

	/**
	 * Available functionalities
	 * 
	 * Constants:
	 *   CONST_SESSION_E_MAIL
	 *   CONST_WORKSPACE_EPML
	 *   INPUT_TEXT
	 *   INPUT_TEXTFIELD
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
		
		"CONVERT_MATCHING" => array(
			"Name"			=> "Convert Matching from RDF to XML format",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::REFMOD_MINER_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature"	=> array(
					"RDF specification: https://ai.wu.ac.at/emisa2015/contest.php"
			),
			"Parameters"	=> array(
				"CLI"				=> null,
				"CONVERT_MATCHING"		=> null,
				"matchings" 	=> "INPUT_TEXT",
				"model_set"		=> "CONST_WORKSPACE_EPML",
				"output_file" => "INPUT_TEXT",
				"format"	=> "xml"
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
			"Name"			=> "RefMod-Mine/NSCM",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLINArySemanticClusterMatching.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"The Process Model Matching Contest 2013 - Thaler, Hake, Fettke, Loos: RefMod-Miner/NSCM (N-Ary Semantic Cluster Matching)"
			),
			"Parameters"	=> array(
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.matching.rmm-nscm",
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
			
		"MATCHING_NHCM" => array(
			"Name"			=> "RefMod-Mine/NHCM",
			"CodeBase"		=> "PHP",
			"ScriptBase"	=> "CLINAryHomogeneityBasedClusterMatching.php",
			"EmbedInPHP"	=> false,
			"Literature" 	=> array(
				"The Process Model Matching Contest 2015 - Thaler, Hake, Dadashnia, Niesen, Sonntag, Fettke, Loos: RefMod-Mine/NHCM (N-Ary Homogeneity-based Cluster Matching)"
			),
			"Description"	=> "Business Process Model Matching: Identification of correspondences between nodes of business process models.",
			"Parameters"	=> array(
				"input"			=> "CONST_WORKSPACE_EPML",
				"output"		=> "CONST_WORKSPACE_EPML.matching.rmm-nhcm",
				"notification"	=> "CONST_SESSION_E_MAIL"
			)
		),
		
		"MATCHING_2015" => array(
				"Name"			=> "Matcher 2015",
				"CodeBase"		=> "PHP",
				"ScriptBase"	=> "CLIMatcher2015.php",
				"EmbedInPHP"	=> false,
				"Literature" 	=> array(
						"n/a"
				),
				"Parameters"	=> array(
						"input"			=> "CONST_WORKSPACE_EPML",
						"output"		=> "CONST_WORKSPACE_EPML.matching.rmm-nscm2015",
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
			"Description"	=> "Language translations of process models between several available languages as e.g. EN, DE, NL, IT, FR.",
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
		),
		
		"TEXT2EPC_MINER" => array(
			"Name"			=> "Text to EPC Miner -BETA-",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::TEXT2MODEL_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature" 	=> array(
				"Based on: Friedrich, F.; Mendling, J.; Puhlmann, F.: Process Model Generation from Natural Language Text, In: (CAiSE'11) Advanced Information Systems Engineering, Vol. 6741, Lecture Notes in Computer Science, pp. 482-496."
			),
			"Description"	=> "Converting a natural language text describing a business process to an EPC.",
			"Parameters"	=> array(
					"input_type"	=> "text",
					"input"			=> "INPUT_TEXTFIELD",
					"output"		=> "CONST_WORKSPACE_EPML.model.fromText.epml"
			),
			"ignoreParameters" => array()
		),
		
		"TEXT2BPMN_MINER" => array(
			"Name"			=> "Text to BPMN Miner -BETA-",
			"CodeBase"		=> "JAVA",
			"ScriptBase"	=> Config::TEXT2MODEL_JAVA_PATH_WITH_FILENAME,
			"EmbedInPHP"	=> true,
			"Literature" 	=> array(
				"Based on: Friedrich, F.; Mendling, J.; Puhlmann, F.: Process Model Generation from Natural Language Text, In: (CAiSE'11) Advanced Information Systems Engineering, Vol. 6741, Lecture Notes in Computer Science, pp. 482-496."
			),
			"Description"	=> "Converting a natural language text describing a business process to a BPMN model.",
			"Parameters"	=> array(
				"input_type"	=> "text",
				"input"			=> "INPUT_TEXTFIELD",
				"output"		=> "CONST_WORKSPACE_EPML.bpmn.fromText.bpmn"
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
			
		"Process Model Matching" => array(
			"MATCHING_NSCM",
			"MATCHING_NHCM"
			//"MATCHING_2015"
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
			
		"Process Model Mining" => array(
			//"CREATE_REFERENCE_MODEL_LI" // returns null
			"TEXT2EPC_MINER",
			"TEXT2BPMN_MINER"
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
	
	public $latestFeatures = array(
		"TEXT2EPC_MINER",
		"MATCHING_NHCM",
		"MODEL_TRANSLATION"
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
	
	public function doesActionRequiresWorkspaceModels($actionName) {
		foreach ( $this->actions[$actionName]["Parameters"] as $paramName => $paramValue ) {
			if ( $paramValue == "CONST_WORKSPACE_EPML" ) return true;
		}
		return false;
	}
	
}
?>