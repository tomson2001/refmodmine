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
			
			if ( empty($actions) ) continue;
			
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
		
		$workspace = new WorkspaceEPML();
		$workspaceData = $workspace->getAvailableData();
		
		$code =  "<div class=\"modal fade\" id=\"modal_set_params_".$action."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"modal_label_set_params".$action."\" aria-hidden=\"true\">\n"; 
		$code .= "  <div class=\"modal-dialog\">\n";
		$code .= "    <div class=\"modal-content\">\n";
		$code .= "      <form  class=\"form-horizontal\" method=\"post\">\n";
		$code .= "        <input type=\"hidden\" name=\"action\" value=\"doProceedWorkspaceAction\" />\n";
		$code .= "        <input type=\"hidden\" name=\"processingAction\" value=\"".$action."\" />\n";
		$code .= "        <div class=\"modal-header\">\n";
		$code .= "          <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
		$code .= "          <h4 class=\"modal-title\" id=\"modal_label_set_params".$action."\">Parameters for ".$actionData["Name"]."</h4>\n";
		$code .= "        </div>\n";
		$code .= "        <div class=\"modal-body\">\n";
		
		// Form elements for user input
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
			
			if ( $value == "SELECT_ONE_METRICS" ) {
				// TODO
				continue;
			}
			
			if ( $value == "SELECT_ONE_SIMMATRIX" ) {
				
				$simmatrixFiles = $workspaceData->getFilesOfType("simmatrix");
				
				$code .= "          <div class=\"form-group\">\n";
				$code .= "            <label for=\"".$paramName."\" class=\"col-sm-4 control-label\">".$paramName."</label>\n";
				$code .= "            <div class=\"col-sm-6\">\n";
				$code .= "              <select class=\"form-control\" name=\"".$paramName."\" id=\"".$paramName."\">\n";
				foreach ( $simmatrixFiles as $file => $entry ) {
					$fileParams = $workspaceData->getFileParams($file);
					$description = $this->config->getFileTypeDescriptions("simmatrix", $fileParams);
					$code .= "            <option value=\"".$workspaceData->path."/".$entry."\">".$description."</option>\n";
				}
				$code .= "              </select>\n";
				$code .= "            </div>\n";
				$code .= "          </div>\n";
				continue;
			}
		}
		
		$code .= "        </div>\n";
		$code .= "        <div class=\"modal-footer\">\n";
		$code .= "          <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Cancel</button>\n";
		$code .= "          <button type=\"submit\" class=\"btn btn-primary\">Run</button>\n";
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
					$command .= $paramNamePart.$paramValue." ";
					$replaceFragments["%".$paramName."%"] = $paramValue;
					continue;
				}
				
				$value = str_replace("CONST_SESSION_E_MAIL", $this->_CONST_SESSION_E_MAIL, $value);
				$value = str_replace("CONST_WORKSPACE_EPML", $this->_CONST_WORKSPACE_EPML, $value);
				$command .= $paramNamePart.$value." ";
				$replaceFragments["%".$paramName."%"] = $value;
			}
			$command = str_replace(array_keys($replaceFragments), $replaceFragments, $command);
			
			if ( $actionData["EmbedInPHP"] ) {
				echo $command;
				$command = str_replace(" ", "[]", $command);
				$description = str_replace(" ", "[]", $actionData["Name"]);
				$command = "php CLIExternalExecution.php command=".$command." description=".$description." sessionid=".$this->_CONST_SESSION_ID." notification=".$this->_CONST_SESSION_E_MAIL." ";
				$command .= "> /dev/null &";
				exec($command);	
			} else {
				$command .= "> /dev/null &";
				exec($command);
			}
			
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