<?php
class WorkspaceActionHandler {
	
	private $config;

	private $workspace;
	private $_CONST_WORKSPACE_EPML;
	private $_CONST_SESSION_E_MAIL;
	private $_CONST_SESSION_ID;
	
	public function __construct() {
		$this->config = new WorkspaceActionConfig();
		$this->initProcessing();
	}
		
	public function getActionMenu() {
		$collapseNum = 1;
		$expanded = "true";
		$collapse = "";
		$in = "in";
		$code = "\n<div class=\"panel-group\" id=\"accordion\" role=\"tablist\" aria-multiselectable=\"true\">\n";
		
		foreach ( $this->config->workspaceActions as $collapseName => $actions ) {
			
			$code .= "  <div class=\"panel panel-default\">\n";
			$code .= "    <div class=\"panel-heading\" role=\"tab\" id=\"heading".$collapseNum."\">\n";
			$code .= "      <h4 class=\"panel-title\">\n";
			$code .= "        <a ".$collapse." data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse".$collapseNum."\" aria-expanded=\"".$expanded."\" aria-controls=\"collapse".$collapseNum."\">\n";
			
			// Title of the collapse
			$code .= "        ".$collapseName."\n";
			
			$code .= "		  </a>\n";
			$code .= "      </h4>\n";
			$code .= "    </div>\n";
			$code .= "    <div id=\"collapse".$collapseNum."\" class=\"panel-collapse collapse ".$in." list-group\" role=\"tabpanel\" aria-labelledby=\"heading".$collapseNum."\">\n";
			
			// Action items
			foreach ( $actions as $action ) {
				$code .= "    ".$this->createActionLinkItem($action)."\n";
			}
			
			$code .= "    </div>\n";
			$code .= "  </div>\n";
			
			$expanded = "false";
			$collapse = "class=\"collapsed\"";
			$in = "";
			$collapseNum++;
						
		}
		
		$code .= "<div>";
		
		// Modal if no models are in workspace
		if ( $this->workspace->numModels == 0 ) {
			
			$code .= "<div class=\"modal fade bs-example-modal-sm\" id=\"modal_no_models_in_workspace\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"modal_no_models_in_workspace\">";
			$code .= "  <div class=\"modal-dialog modal-sm\">";
			$code .= "    <div class=\"modal-content\">";
			$code .= "      <div class=\"modal-header\">";
			$code .= "        <button type=\"button\" class=\"close\" data-dismiss=\"moda\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
			$code .= "        <h4 class=\"modal-title\">Models required</h4>";
			$code .= "      </div>";
			$code .= "      <div class=\"modal-body\" align=\"justify\">";
			$code .= "        This action requires models to be in the workspace. You can drag'n'drop EPML files to the workspace or load"; 
			$code .= "		  them from the <a href=\"index.php?site=repository\" title=\"model repository\" alt=\"model repository\">model repository</a>.";
			$code .= "        Alternatively, you can also load other formats like e.g. <a href=\"index.php?site=converter\" title=\"AML-EPML-Converter\" alt=\"AML-EPML-Converter\">AML</a>,";
			$code .= "        <a href=\"index.php?site=converter_BPMN-EPC\" title=\"BPMN-EPC-Converter\" alt=\"BPMN-EPC-Converter\">BPMN</a> or"; 
			$code .= "        <a href=\"index.php?site=converter_PNML-EPC\" title=\"PetriNet-EPC-Converter\" alt=\"PetriNet-EPC-Converter\">PNML</a> via one the converters (see Tools &amp; Infos).</p>";
			$code .= "      </div>";
			$code .= "      <div class=\"modal-footer\">";
			$code .= "        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>";
			$code .= "      </div>";
			$code .= "    </div><!-- /.modal-content -->";
			$code .= "  </div><!-- /.modal-dialog -->";
			$code .= "</div><!-- /.modal -->";
			
		}
		
		return $code;
	}
	
	public function getLatestFeaturesMenu() {
		$this->initProcessing();
		$code = "<div class=\"list-group\">";
		
		foreach ( $this->config->latestFeatures as $action ) {
		
			$actionData = $this->config->getActionData($action);
			
			// compose link components
			$href = "index.php?site=workspace&action=doProceedWorkspaceAction&processingAction=".$action;
			$title = implode(" | ", $actionData["Literature"]);
			$description = $actionData["ShortDescription"];
			$name = $actionData["Name"];
			
			if ( $this->isUserInputNeededForAction($action) ) {			
				$modalCode = $this->createModalForActionLinkItem($action);
				$href = $this->workspace->numModels == 0 && $this->config->doesActionRequiresWorkspaceModels($action) ? "index.php?site=workspace" : "#modal_set_params_".$action;
				$active = $this->workspace->numModels == 0 && $this->config->doesActionRequiresWorkspaceModels($action) ? "" : " active";
				$code .= "  <a href=\"".$href."\" class=\"list-group-item ".$active."\" data-toggle=\"modal\" title=\"".$title."\" data-placement=\"bottom\">\n";
				$code .= "    <h4 class=\"list-group-item-heading\">".$name."</h4>";
				$code .= "    <h5 class=\"list-group-item-text\">".$description."</h5>";
				$code .= "  </a>\n".$modalCode;
			} else {
				$href = $this->workspace->numModels == 0 && $this->config->doesActionRequiresWorkspaceModels($action) ? "index.php?site=workspace" : $href;
				$code .= "  <a href=\"".$href."\" class=\"list-group-item\" title=\"".$title."\">\n";
				$code .= "    <h4 class=\"list-group-item-heading\">".$name."</h4>";
				$code .= "    <h5 class=\"list-group-item-text\">".$description."</h5>";
				$code .= "  </a>\n";
			}
		}
		
		$code .= "</div>";
		return $code;
	}
	
	private function createActionLinkItem($action) {
		$actionData = $this->config->getActionData($action);
                
		// compose link components
		$href = "index.php?site=workspace&action=doProceedWorkspaceAction&processingAction=".$action;
		$title = implode(" | ", $actionData["Literature"]);
		$name = $actionData["Name"];
                
                if (array_key_exists("link", $actionData)){
                    $href = $actionData["link"];
                    return "<a href=\"".$href."\" class=\"list-group-item\" data-toggle=\"tooltip\" title=\"".$title."\" data-placement=\"bottom\">".$name."</a>";
                }
		
		if ( $this->workspace->numModels == 0 && $this->config->doesActionRequiresWorkspaceModels($action) ) {
			return "<a href=\"#modal_no_models_in_workspace\" class=\"list-group-item\" data-toggle=\"modal\" title=\"".$title."\" data-placement=\"bottom\">".$name."</a>\n"; 
		}
		
		if ( $this->isUserInputNeededForAction($action) ) {
			$modalCode = $this->createModalForActionLinkItem($action);
			return "<a href=\"#modal_set_params_".$action."\" class=\"list-group-item\" data-toggle=\"modal\" title=\"".$title."\" data-placement=\"bottom\">".$name."</a>\n".$modalCode;
		} else {
			return "<a href=\"".$href."\" class=\"list-group-item\" data-toggle=\"tooltip\" title=\"".$title."\" data-placement=\"bottom\">".$name."</a>";
		}
	}
	
	private function isUserInputNeededForAction($action) {
		$actionData = $this->config->getActionData($action);
		$params = $actionData["Parameters"];
		
		foreach ( $params as $paramName => $value ) {
			if ( !is_null($value) && ( is_array($value) || in_array($value, $this->config->userDependencyParams) || substr_count($value, "INPUT_BOOL") > 0 ) || substr_count($value, "INPUT_SLIDER") > 0 ) return true;
		}
		
		return false;
	}
	
	private function createModalForActionLinkItem($action) {
		$actionData = $this->config->getActionData($action);
		$params = $actionData["Parameters"];
		
		$workspace = new WorkspaceEPML(false);
		$workspaceData = $workspace->getAvailableData();
		
		$code =  "<div class=\"modal fade\" id=\"modal_set_params_".$action."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"modal_label_set_params".$action."\" aria-hidden=\"true\">\n"; 
		$code .= "  <div class=\"modal-dialog\">\n";
		$code .= "    <div class=\"modal-content\">\n";
		$code .= "      <form  class=\"form-horizontal\" method=\"post\" action=\"index.php?site=workspace\">\n";
		$code .= "        <input type=\"hidden\" name=\"action\" value=\"doProceedWorkspaceAction\" />\n";
		$code .= "        <input type=\"hidden\" name=\"processingAction\" value=\"".$action."\" />\n";
		$code .= "        <div class=\"modal-header\">\n";
		$code .= "          <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
		$code .= "          <h4 class=\"modal-title\" id=\"modal_label_set_params".$action."\">Parameters for ".$actionData["Name"]."</h4>\n";
		$code .= "        </div>\n";
		$code .= "        <div class=\"modal-body\">\n";
		
		// Print description of action
		$descriptionPrinted = false;
		if ( isset($actionData["Description"]) && strlen($actionData["Description"]) < 300 ) {
			$descriptionPrinted = true;
			$code .= "            <p style=\"font-size:14px;\">".$actionData["Description"]."</p>\n";
		}
		
		$runPossible = true; // switch deciding whether all input data are available for an execution of the action
		
		// Form elements for user input
		// @TODO check whether input data are available!
		foreach ( $params as $paramName => $value ) {
			
			if ( is_null($value) ) continue;
			
			if ( is_array($value) ) {
				$multiselect = (isset($value["PARAM_OPTION_MULTISELECT"]) && $value["PARAM_OPTION_MULTISELECT"] === true ) ? "multiple" : "";
				$multiselectBrackets = empty($multiselect) ? "" : "[]"; 
				$defaults = ( isset($value["PARAM_OPTION_DEFAULTS"]) ) ? $value["PARAM_OPTION_DEFAULTS"] : array();
				if ( isset($value["PARAM_OPTION_MULTISELECT"]) ) unset($value["PARAM_OPTION_MULTISELECT"]);
				if ( isset($value["PARAM_OPTION_DEFAULTS"]) ) unset($value["PARAM_OPTION_DEFAULTS"]);
				if ( isset($value["PARAM_OPTION_CALL_SEPARATOR"]) ) unset($value["PARAM_OPTION_CALL_SEPARATOR"]);
				
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select ".$multiselect." class=\"form-control\" name=\"".$paramName."".$multiselectBrackets."\" id=\"".$paramName."\">\n";
				foreach ( $value as $optionName => $optionValue ) {
					$selected = ( in_array($optionName, $defaults) ) ? "selected" : "";
					$code .= "            <option value=\"".$optionName."\" ".$selected.">".$optionValue."</option>\n";
				}
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
			}
			
			if ( $value == "INPUT_TEXT" ) {
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				
				$code .= "              <input type=\"text\" class=\"form-control\" name=\"".$paramName."\" placeholder=\"".$paramName."\">\n";
				
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				
				
				
				//$code .= "          <input type=\"text\" class=\"form-control\" name=\"".$paramName."\" placeholder=\"".$paramName."\">";
				continue;
			}
			
			if ( $value == "INPUT_TEXTFIELD" ) {
				$code .= "          <textarea class=\"form-control\" name=\"".$paramName."\" rows=\"6\">".$paramName."</textarea>";
				continue;
			}
			
			if ( substr_count($value, "INPUT_BOOL") > 0 ) {
				$boolParams = explode("|", $value); // 1: ON | OFF
				$default = ($boolParams[1] == "ON") ? "checked" : "";
				
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				
				$code .= "              <input name=\"".$paramName."\" ".$default." data-toggle=\"toggle\" type=\"checkbox\" data-size=\"small\">\n";
				
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				
				continue;
			}
			
			if ( substr_count($value, "INPUT_SLIDER") > 0 ) {
				$sliderParams = explode("|", $value); // index INPUT_SLIDER|MIN|MAX|STEP|DEFAULT
				
				$code .= "<script type='text/javascript'>\n";
				$code .= "  $(document).ready(function() {\n";
				$code .= "	  $(\"#".$paramName."\").slider();\n";
				$code .= "    $(\"#".$paramName."\").on('slide', function(slideEvt) {\n";
				$code .= "      $(\"#".$paramName."SliderVal\").text(slideEvt.value);\n";
				$code .= "    });\n";
				$code .= "  });\n";
				$code .= "</script>\n";
				
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				
				$code .= "              <input id=\"".$paramName."\" name=\"".$paramName."\" type=\"text\" data-slider-min=\"".$sliderParams[1]."\" data-slider-max=\"".$sliderParams[2]."\" data-slider-step=\"".$sliderParams[3]."\" data-slider-value=\"".$sliderParams[4]."\" value=\"".$sliderParams[4].">\n";
				$code .= "              <span id=\"".$paramName."CurrentSliderValLabel\"> <span id=\"".$paramName."SliderVal\">".$sliderParams[4]."</span></span>\n";
				
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				
				continue;
			}
			
			if ( $value == "SELECT_ONE_METRICS" || $value == "SELECT_ONE_METRICS_OPTIONAL" ) {
				$metricsFiles = $workspaceData->getFilesOfType("metrics");
				$disabled = empty($metricsFiles) ? "disabled" : "";
				$placeHolder = empty($metricsFiles) ? "<option>no metrics available</option>" : "";
				if ( empty($metricsFiles) ) $runPossible = false;
			
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select class=\"form-control\" ".$disabled." name=\"".$paramName."\" id=\"".$paramName."\">\n";
				
				if ( $value == "SELECT_ONE_METRICS_OPTIONAL" ) {
					$code .= "               <option value=\"null\"></option>\n";
				}
				
				foreach ( $metricsFiles as $file => $entry ) {
					$fileParams = $workspaceData->getFileParams($file);
					$description = $this->config->getFileTypeDescriptions("metrics", $fileParams);
					$code .= "               <option value=\"".$workspaceData->path."/".$entry."\">".$description."</option>\n";
				}
				$code .= "               ".$placeHolder."\n";
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
			}
			
			if ( $value == "SELECT_ONE_MODEL" || $value == "SELECT_ONE_MODEL_OPTIONAL" ) {
				
				$disabled = empty($workspace->modelList) ? "disabled" : "";
				$placeHolder = empty($workspace->modelList) ? "<option>no models available</option>" : "";
				if ( empty($workspace->modelList) ) $runPossible = false;
			
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select class=\"form-control\" ".$disabled." name=\"".$paramName."\" id=\"".$paramName."\">\n";
				
				if ( $value == "SELECT_ONE_MODEL_OPTIONAL" ) {
					$code .= "               <option value=\"null\"></option>\n";
				}
				
				foreach ( $workspace->modelList as $modelName ) {
					$code .= "               <option value=\"".$modelName."\">".$modelName."</option>\n";
				}
				$code .= "               ".$placeHolder."\n";
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
			}
			
			if ( $value == "SELECT_ONE_SIMMATRIX" ) {
				
				$simmatrixFiles = $workspaceData->getFilesOfType("simmatrix");
				$disabled = empty($simmatrixFiles) ? "disabled" : "";
				$placeHolder = empty($simmatrixFiles) ? "<option>no similarity matrix available</option>" : "";
				if ( empty($simmatrixFiles) ) $runPossible = false;
				
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select class=\"form-control\" ".$disabled." name=\"".$paramName."\" id=\"".$paramName."\">\n";
				foreach ( $simmatrixFiles as $file => $entry ) {
					$fileParams = $workspaceData->getFileParams($file);
					$description = $this->config->getFileTypeDescriptions("simmatrix", $fileParams);
					$code .= "               <option value=\"".$workspaceData->path."/".$entry."\">".$description."</option>\n";
				}
				$code .= "               ".$placeHolder."\n";
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
			}
			
			if ( $value == "SELECT_ONE_PROCESSLOG" ) {
			
				$mxmlFiles = $workspaceData->getFilesOfType("processlog");
				$disabled = empty($mxmlFiles) ? "disabled" : "";
				$placeHolder = empty($mxmlFiles) ? "<option>no process log available</option>" : "";
				if ( empty($mxmlFiles) ) $runPossible = false;
			
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select class=\"form-control\" ".$disabled." name=\"".$paramName."\" id=\"".$paramName."\">\n";
				foreach ( $mxmlFiles as $file => $entry ) {
					$fileParams = $workspaceData->getFileParams($file);
					$description = $this->config->getFileTypeDescriptions("processlog", $fileParams);
					$code .= "               <option value=\"".$workspaceData->path."/".$entry."\">".$description."</option>\n";
				}
				$code .= "               ".$placeHolder."\n";
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
			}
			
			if ( $value == "SELECT_MULTIPLE_PROCESSLOG" ) {
					
				$mxmlFiles = $workspaceData->getFilesOfType("processlog");
				$disabled = empty($mxmlFiles) ? "disabled" : "";
				$placeHolder = empty($mxmlFiles) ? "<option>no process log available</option>" : "";
				if ( empty($mxmlFiles) ) $runPossible = false;
					
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select multiple class=\"form-control\" ".$disabled." name=\"".$paramName."[]\" id=\"".$paramName."\">\n";
				foreach ( $mxmlFiles as $file => $entry ) {
					$fileParams = $workspaceData->getFileParams($file);
					$description = $this->config->getFileTypeDescriptions("processlog", $fileParams);
					$code .= "               <option value=\"".$workspaceData->path."/".$entry."\">".$description."</option>\n";
				}
				$code .= "               ".$placeHolder."\n";
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
			}
			
			if ( $value == "SELECT_ONE_XML_MATCHING" || $value == "SELECT_ONE_XML_MATCHING_OPTIONAL" ) {
			
				$xmlMatchingFiles = $workspaceData->getFilesOfType("xmlmatching");
				$disabled = empty($xmlMatchingFiles) ? "disabled" : "";
				$placeHolder = empty($xmlMatchingFiles) ? "<option>no xml matching available</option>" : "";
				if ( empty($xmlMatchingFiles) ) $runPossible = false;
			
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select class=\"form-control\" ".$disabled." name=\"".$paramName."\" id=\"".$paramName."\">\n";
				
				if ( $value == "SELECT_ONE_XML_MATCHING_OPTIONAL" ) {
					$code .= "               <option value=\"null\"></option>\n";
				}
				
				foreach ( $xmlMatchingFiles as $file => $entry ) {
					$fileParams = $workspaceData->getFileParams($file);
					$description = $this->config->getFileTypeDescriptions("xmlmatching", $fileParams);
					$code .= "               <option value=\"".$workspaceData->path."/".$entry."\">".$description."</option>\n";
				}
				$code .= "               ".$placeHolder."\n";
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
			}
			
			if ( $value == "SELECT_ONE_VALUE_SERIES" ) {
			
				$valueSeriesFiles = $workspaceData->getFilesOfType("valueseries");
				$disabled = empty($valueSeriesFiles) ? "disabled" : "";
				$placeHolder = empty($valueSeriesFiles) ? "<option>no value series available</option>" : "";
				if ( empty($valueSeriesFiles) ) $runPossible = false;
			
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select class=\"form-control\" ".$disabled." name=\"".$paramName."\" id=\"".$paramName."\">\n";
				foreach ( $valueSeriesFiles as $file => $entry ) {
					$fileParams = $workspaceData->getFileParams($file);
					$description = $this->config->getFileTypeDescriptions("valueseries", $fileParams);
					$code .= "               <option value=\"".$workspaceData->path."/".$entry."\">".$description."</option>\n";
				}
				$code .= "               ".$placeHolder."\n";
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
			}
			
			if ( $value == "SELECT_ONE_PNML" ) {
				
				$pnmlFiles = $workspaceData->getFilesOfType("pnml");
				$disabled = empty($pnmlFiles) ? "disabled" : "";
				$placeHolder = empty($pnmlFiles) ? "<option>no petri net available</option>" : "";
				if ( empty($pnmlFiles) ) $runPossible = false;
				
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select class=\"form-control\" ".$disabled." name=\"".$paramName."\" id=\"".$paramName."\">\n";
				foreach ( $pnmlFiles as $file => $entry ) {
					$fileParams = $workspaceData->getFileParams($file);
					$description = $this->config->getFileTypeDescriptions("pnml", $fileParams);
					$code .= "               <option value=\"".$workspaceData->path."/".$entry."\">".$description."</option>\n";
				}
				$code .= "               ".$placeHolder."\n";
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
				
			}
		}
		
		if ( isset($actionData["Description"]) && !$descriptionPrinted ) {
			$code .= "          <hr>\n";
			$code .= "          <p align=\"justify\" style=\"font-size:14px;\"><b>Description</b>. ".$actionData["Description"]."</p>";
		}
		
		$runDisabled = $runPossible ? "" : "disabled";
		
		$code .= "        </div>\n";
		$code .= "        <div class=\"modal-footer\">\n";
		$code .= "          <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Cancel</button>\n";
		$code .= "          <button type=\"submit\" ".$runDisabled." class=\"btn btn-primary\">Run</button>\n";
		$code .= "        </div>\n";
		$code .= "      </form>\n";
		$code .= "    </div>\n";
		$code .= "  </div>\n";
		$code .= "</div>\n";

		return $code;
	}
	
	public function actionExists($action) {
		if ( isset($this->config->actions[$action]) ) return true;
		return false;
	}
	
	public function run($action) {
		if ( $this->actionExists($action) ) {
			$this->initProcessing();
			$actionData = $this->config->getActionData($action);
			$params = $actionData["Parameters"];
			//print_r($params);
			
			// building CLI command
			$command  = $this->config->execCodeBases[$actionData["CodeBase"]]." ";
			$command .= $actionData["ScriptBase"]." ";
			
			$replaceFragments = array();
			foreach ( $params as $paramName => $value ) {
				
				if ( is_null($value) || $value == "null" ) {
					$command .= $paramName." ";
					continue;
				}
				
				$paramNamePart = (substr($paramName, 0, 1) == "@") ? "" : $paramName."=";
								
				if ( is_array($value) ) {
					
					if ( is_array($_POST[$paramName]) ) {
						// multiselect
						$callSeparator = ","; // DEFAULT CALL SEPARATOR FOR MULTISELECT
						if ( isset($_POST[$paramName]["PARAM_OPTION_CALL_SEPARATOR"]) ) $callSeparator = $_POST[$paramName]["PARAM_OPTION_CALL_SEPARATOR"];
						if ( isset($_POST[$paramName]["PARAM_OPTION_CALL_SEPARATOR"]) ) unset($_POST[$paramName]["PARAM_OPTION_CALL_SEPARATOR"]);
						if ( isset($_POST[$paramName]["PARAM_OPTION_DEFAULTS"]) ) unset($_POST[$paramName]["PARAM_OPTION_DEFAULTS"]);
						if ( isset($_POST[$paramName]["PARAM_OPTION_MULTISELECT"]) ) unset($_POST[$paramName]["PARAM_OPTION_MULTISELECT"]);
						$command .= $paramNamePart.implode(",", $_POST[$paramName])." ";
						$replaceFragments["%".$paramName."%"] = implode(",", $_POST[$paramName]);
						continue;
					} else {
						// single select
						$paramValue = $value[$_POST[$paramName]];
						$command .= $paramNamePart.$paramValue." ";
						$replaceFragments["%".$paramName."%"] = $_POST[$paramName];
					}
					continue;
				}
				
				if ( substr_count($value, "SELECT_MULTIPLE_") > 0 ) {
					// multiselect
					$callSeparator = ","; // DEFAULT CALL SEPARATOR FOR MULTISELECT
					if ( isset($_POST[$paramName]["PARAM_OPTION_CALL_SEPARATOR"]) ) $callSeparator = $_POST[$paramName]["PARAM_OPTION_CALL_SEPARATOR"];
					if ( isset($_POST[$paramName]["PARAM_OPTION_CALL_SEPARATOR"]) ) unset($_POST[$paramName]["PARAM_OPTION_CALL_SEPARATOR"]);
					if ( isset($_POST[$paramName]["PARAM_OPTION_DEFAULTS"]) ) unset($_POST[$paramName]["PARAM_OPTION_DEFAULTS"]);
					if ( isset($_POST[$paramName]["PARAM_OPTION_MULTISELECT"]) ) unset($_POST[$paramName]["PARAM_OPTION_MULTISELECT"]);
					$command .= $paramNamePart.implode(",", $_POST[$paramName])." ";
					$replaceFragments["%".$paramName."%"] = implode(",", $_POST[$paramName]);
					continue;
				}
				
				if ( substr_count($value, "INPUT_BOOL") > 0 ) {
					if ( isset($_POST[$paramName]) ) {
						$command .= $paramName." ";
					}
					continue;
				}
				
				// Special for Input Slider and INPUT BOOL since there a additional parameters
				if ( substr_count($value, "INPUT_SLIDER") > 0 ) $value = "INPUT_SLIDER";
				if ( in_array($value, $this->config->userDependencyParams) ) {
					$paramValue = $_POST[$paramName];
					$paramValue = Config::ABS_PATH.$paramValue;
					$commandExtension = $paramNamePart.$paramValue." ";
					//print($paramValue."\n<br>");
					if ( substr_count($paramValue, " ") > 0 ) $commandExtension = $paramNamePart."\"".$paramValue."\" ";
					if ( isset($actionData["ignoreParameters"]) && in_array($paramName, $actionData["ignoreParameters"]) ) $commandExtension = "";
					$command .= $commandExtension;
					
					$replaceFragments["%".$paramName."%"] = $paramValue;
					
					// Specials
					if ( substr_count($value, "SELECT_ONE_PNML") > 0 ) {
						$paramValue = basename($paramValue);
						$pos = strrpos($paramValue, ".");
						$paramValue = substr($paramValue, $pos+1);
						$replaceFragments["%SELECT_ONE_PNML@".$paramName."%"] = $paramValue;
					}
					
					continue;
				}
				
				$value = str_replace("CONST_SESSION_E_MAIL", $this->_CONST_SESSION_E_MAIL, $value);
				$value = str_replace("CONST_WORKSPACE_EPML", $this->_CONST_WORKSPACE_EPML, $value);
								
				$command .= $paramNamePart.$value." ";
				$replaceFragments["%".$paramName."%"] = $value;
			}
			
			foreach ( $replaceFragments as $index => $value ) {
				$replaceFragments[$index] = str_replace(" ", "-", $replaceFragments[$index]);
				$replaceFragments[$index] = str_replace(",", "-", $replaceFragments[$index]);
				$replaceFragments[$index] = str_replace("?", "-", $replaceFragments[$index]);
				$replaceFragments[$index] = str_replace("!", "-", $replaceFragments[$index]);
				$replaceFragments[$index] = str_replace(";", "-", $replaceFragments[$index]);
				$replaceFragments[$index] = str_replace(".", "-", $replaceFragments[$index]);
			}
			
			$command = str_replace(array_keys($replaceFragments), $replaceFragments, $command);
			
			$extCommand = $command;
                         error_reporting(E_ALL);
                         
                         //$output2 = `echo "%cd%"`;
                        //$output = `java -jar lib\master.jar`;
                        
			$checksum = md5($extCommand."-".time());
                       
			
			if ( $actionData["EmbedInPHP"] ) {
				Logger::log($this->_CONST_SESSION_E_MAIL, "External call started (Embedded in PHP): ".$command, "ACCESS");
				$command = str_replace(" ", "[]", $command);
				$command = str_replace("\"", "@QUOTE@", $command);
				$description = str_replace(" ", "[]", $actionData["Name"]);
				$command = "php CLIExternalExecution.php command=".$command." description=".$description." sessionid=".$this->_CONST_SESSION_ID." notification=".$this->_CONST_SESSION_E_MAIL." checksum=".$checksum." ";
				//$command .= "> /dev/null &";
				//Logger::log($this->_CONST_SESSION_E_MAIL, "External call started (Embedded in PHP): ".$command, "ACCESS");
				exec($command, $output);
                                print_r($output);
			} else {
				Logger::log($this->_CONST_SESSION_E_MAIL, "External call started: ".$command, "ACCESS");
				$command .= "> /dev/null &";
				exec($command, $output);
                                print_r($output);
			}
			
			$actionStats = new ActionStats();
			$actionStats->trackAction($action, $this->_CONST_SESSION_ID);
			
			$actionLog = new ActionLog();
			$actionLog->trackAction($action, $extCommand, $this->_CONST_SESSION_ID, $checksum);
			
			$_POST["msg"] = "<strong>\"".$actionData["Name"]."\" started. </strong> Please wait and refresh the site until the result is available. That may take a while.";
			if ( $this->_CONST_SESSION_E_MAIL !== "no" ) $_POST["msg"] .= " However, you will also get an e-mail when the calculation has been finished.";
			$_POST["msgType"] = "info";
			
		} else {
			$_POST["msg"] = "<strong>Error. </strong> Action does not exist. Please contact the admin!";
			$_POST["msgType"] = "danger";
		}
	}
	
	private function initProcessing() {
		$this->workspace = new WorkspaceEPML();
		$this->_CONST_WORKSPACE_EPML = $this->workspace->file;
		$this->_CONST_SESSION_E_MAIL = empty($_SESSION["email"]) ? "no" : $_SESSION["email"];
		$this->_CONST_SESSION_ID = $this->workspace->sessionID;
	}
	
}
?>