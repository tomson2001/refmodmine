<?php
class WorkspaceActionHandler {
	
	private $config;

	private $workspace;
	private $_CONST_WORKSPACE_EPML;
	private $_CONST_SESSION_E_MAIL;
	private $_CONST_SESSION_ID;
	
	public function __construct() {
		$this->config = new WorkspaceActionConfig();
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
			$description = $actionData["Description"];
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
			if ( !is_null($value) && ( is_array($value) || in_array($value, $this->config->userDependencyParams) ) ) return true;
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
		if ( isset($actionData["Description"]) ) {
			$code .= "            <p class=\"help-block\">".$actionData["Description"]."</p>\n";
		}
		
		$runPossible = true; // switch deciding whether all input data are available for an execution of the action
		
		// Form elements for user input
		// @TODO check whether input data are available!
		foreach ( $params as $paramName => $value ) {
			
			if ( is_null($value) ) continue;
			
			if ( is_array($value) ) {
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select class=\"form-control\" name=\"".$paramName."\" id=\"".$paramName."\">\n";
				foreach ( $value as $optionName => $optionValue ) {
					$code .= "            <option value=\"".$optionName."\">".$optionName."</option>\n";
				}
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
			}
			
			if ( $value == "INPUT_TEXT" ) {
				$code .= "          <input type=\"text\" class=\"form-control\" name=\"".$paramName."\" placeholder=\"".$paramName."\">";
				continue;
			}
			
			if ( $value == "INPUT_TEXTFIELD" ) {
				$code .= "          <textarea class=\"form-control\" name=\"".$paramName."\" rows=\"6\">".$paramName."</textarea>";
				continue;
			}
			
			if ( $value == "SELECT_ONE_METRICS" ) {
				// TODO
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
			
			if ( $value == "SELECT_ONE_XML_MATCHING" ) {
			
				$xmlMatchingFiles = $workspaceData->getFilesOfType("xmlmatching");
				$disabled = empty($xmlMatchingFiles) ? "disabled" : "";
				$placeHolder = empty($xmlMatchingFiles) ? "<option>no xml matching available</option>" : "";
				if ( empty($xmlMatchingFiles) ) $runPossible = false;
			
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select class=\"form-control\" ".$disabled." name=\"".$paramName."\" id=\"".$paramName."\">\n";
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
				
				if ( is_null($value) ) {
					$command .= $paramName." ";
					continue;
				}
				
				$paramNamePart = (substr($paramName, 0, 1) == "@") ? "" : $paramName."=";
								
				if ( is_array($value) ) {
					$paramValue = $value[$_POST[$paramName]];
					$command .= $paramNamePart.$paramValue." ";
					$replaceFragments["%".$paramName."%"] = $_POST[$paramName];
					continue;
				}
				
				if ( in_array($value, $this->config->userDependencyParams) ) {
					$paramValue = $_POST[$paramName];
					
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
			$command = str_replace(array_keys($replaceFragments), $replaceFragments, $command);
			
			if ( $actionData["EmbedInPHP"] ) {
				Logger::log($this->_CONST_SESSION_E_MAIL, "External call started (Embedded in PHP): ".$command, "ACCESS");
				$command = str_replace(" ", "[]", $command);
				$command = str_replace("\"", "@QUOTE@", $command);
				$description = str_replace(" ", "[]", $actionData["Name"]);
				$command = "php CLIExternalExecution.php command=".$command." description=".$description." sessionid=".$this->_CONST_SESSION_ID." notification=".$this->_CONST_SESSION_E_MAIL." ";
				$command .= "> /dev/null &";
				exec($command);	
			} else {
				Logger::log($this->_CONST_SESSION_E_MAIL, "External call started (Embedded in PHP): ".$command, "ACCESS");
				$command .= "> /dev/null &";
				exec($command);
			}
			
			$actionStats = new ActionStats();
			$actionStats->trackAction($action, $this->_CONST_SESSION_ID);
			
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