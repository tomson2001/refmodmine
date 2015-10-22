<?php
class WorkspaceData {

	public $path;
	public $files = array();
	
	private $filePrefix = "workspace.epml.";
	
	public function __construct($workspacePath) {

		$this->path = $workspacePath;
		$files = scandir($this->path);
				
		// remove all entries which are no empl
		foreach ( $files as $index => $entry ) {
			if ( substr($entry, 0, 15) == $this->filePrefix ) {
				$this->files[str_replace($this->filePrefix, "", $entry)] = $entry;
			}
		}
	}
	
	public function getFileType($file) {
		$fileInfos = explode(".", $file);
		return $fileInfos[0];
	}
	
	public function getFilesOfType($type) {
		$foundFiles = array();
		foreach ( $this->files as $file => $entry ) {
			$fileType = $this->getFileType($file);
			if ( $fileType == $type ) $foundFiles[$file] = $entry;
		}
		return $foundFiles;
	}
	
	public function getDownloadLink($file) {
		return $this->path."/".$this->files[$file];
	}
	
	public function getDownloadFilename($file) {
		$fileType = $this->getFileType($file);
		$workspaceActionConfig = new WorkspaceActionConfig();
		$fileExtension = $workspaceActionConfig->getFileTypeExtension($fileType);
		return str_replace(".", "_", $file).".".$fileExtension;
	}
	
	public function getFileParams($file) {
		$fileInfos = explode(".", $file);
		unset($fileInfos[0]);
		return $fileInfos;
	}
	
	public function deleteFile($file) {
		if ( isset($this->files[$file]) ) {
			unlink($this->path."/".$this->files[$file]);
			unset($this->files[$file]);
			return true;
		}
		return false;
	}
	
	public function deleteAllFiles() {
		foreach ( $this->files as $file => $filepath ) {
			if ( !$this->deleteFile($file) ) return false;
		}
		return true; 
	}
	
	public function containsFile($file) {
		return isset($this->files[$file]);
	}
	
	public function getDeleteModalCode($file, $reloadLink) {
		$workspaceActionConfig = new WorkspaceActionConfig();
		$fileType = $this->getFileType($file);
		$fileParams = $this->getFileParams($file);
		$type = $workspaceActionConfig->getFileTypeName($fileType);
		$description = $workspaceActionConfig->getFileTypeDescriptions($fileType, $fileParams);
		
		return "
		<div class=\"modal fade\" id=\"modal_delete_".md5($file)."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">
			<div class=\"modal-dialog\">
				<div class=\"modal-content\">
					<div class=\"modal-header\">
						<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						<h4 class=\"modal-title\" id=\"myModalLabel\">Delete ".$type." (".$description.")</h4>
					</div>
					<div class=\"modal-body\">
					Do you really want to delete the ".$type." data?
					</div>
					<div class=\"modal-footer\">
						<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">No</button>
						<button type=\"button\" class=\"btn btn-primary\" onClick=\"window.location.href='".$reloadLink."&action=doDeleteWorkspaceDataFile&file=".$file."'\">Yes, delete now!</button>
					</div>
				</div>
			</div>
		</div>
		";
	}
	
	public function getAddModelsFromDataToWorkspaceModalCode($file, $reloadLink) {
		$workspaceActionConfig = new WorkspaceActionConfig();
		$fileType = $this->getFileType($file);
		$fileParams = $this->getFileParams($file);
		$type = $workspaceActionConfig->getFileTypeName($fileType);
		$description = $workspaceActionConfig->getFileTypeDescriptions($fileType, $fileParams);
	
		return "
		<div class=\"modal fade\" id=\"modal_addModelsFromDataToWorkspace_".md5($file)."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">
			<div class=\"modal-dialog\">
				<div class=\"modal-content\">
					<div class=\"modal-header\">
						<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						<h4 class=\"modal-title\" id=\"myModalLabel\">Add ".$type." (".$description.") to workspace models</h4>
					</div>
					<div class=\"modal-body\">
						How to handle the models with regard to the current workspace models?
					</div>
					<div class=\"modal-footer\">
						<button type=\"button\" class=\"btn btn-primary\" onClick=\"window.location.href='".$reloadLink."&action=doAddModelsFromDataToWorkspace&file=".$file."&mode=add'\">Add</button>
						<button type=\"button\" class=\"btn btn-warning\" onClick=\"window.location.href='".$reloadLink."&action=doAddModelsFromDataToWorkspace&file=".$file."&mode=replace'\">Replace</button>
						<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Cancel</button>
					</div>
				</div>
			</div>
		</div>
		";
	}
	
	public function getEditFileDescriptionModalCode($file, $reloadLink) {
		$workspaceActionConfig = new WorkspaceActionConfig();
		$fileType = $this->getFileType($file);
		$fileParams = $this->getFileParams($file);
		$type = $workspaceActionConfig->getFileTypeName($fileType);
		$description = $workspaceActionConfig->getFileTypeDescriptions($fileType, $fileParams);
	
		return "
		<div class=\"modal fade\" id=\"modal_editFileDescription_".md5($file)."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">
			<div class=\"modal-dialog\">
				<div class=\"modal-content\">
					<form  class=\"form-horizontal\" method=\"post\">
						<input type=\"hidden\" name=\"action\" value=\"doEditFileDescription\" />
						<input type=\"hidden\" name=\"file\" value=\"".$file."\" />
						<div class=\"modal-header\">
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
							<h4 class=\"modal-title\" id=\"myModalLabel\">Edit description of ".$type." (".$description.")</h4>
						</div>
						<div class=\"modal-body\">
							<input type=\"text\" class=\"form-control\" name=\"description\" placeholder=\"".$description."\">
						</div>
						<div class=\"modal-footer\">
							<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Cancel</button>
							 <button type=\"submit\" class=\"btn btn-primary\">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		";
	}
	
}
?>