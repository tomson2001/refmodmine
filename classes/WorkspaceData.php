<?php
class WorkspaceData {

	public $path;
	public $files = array();
	
	private $filePrefix = "workspace.epml.";
	
	private $fileExtensions = array(
		"metrics" => "csv",
		"simmatrix" => "csv"
	);
	
	private $fileIcons = array(
		"metrics"   => "glyphicon glyphicon-th",
		"simmatrix" => "glyphicon glyphicon-adjust"
	);
	
	private $openingMethods = array(
		"csv"	=> "workspaceCSVViewer"
	);
	
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
	
	public function getFileDescription($file) {
		$fileInfos = explode(".", $file);
		unset($fileInfos[0]);
		foreach ( $fileInfos as $index => $info ) {
			$fileInfos[$index] = ucfirst($info);
		}
		return implode(", ", $fileInfos);
	}
	
	public function getFileType($file) {
		$fileInfos = explode(".", $file);
		return ucfirst($fileInfos[0]);
	}
	
	public function getOpeningMethod($file) {
		$fileExt = $this->getFileExtension($file);
		if ( array_key_exists($fileExt, $this->openingMethods) ) return $this->openingMethods[$fileExt];
		return null;
	}
	
	public function getDownloadLink($file) {
		return $this->path."/".$this->files[$file];
	}
	
	public function getFileIcon($file) {
		$fileInfos = explode(".", $file);
		return $this->fileIcons[$fileInfos[0]];
	}
	
	public function getDownloadFilename($file) {
		$filetype = $this->getFileExtension($file);
		return str_replace(".", "_", $file).".".$filetype;
	}
	
	public function getFileExtension($file) {
		$fileInfos = explode(".", $file);
		return $this->fileExtensions[$fileInfos[0]];
	}
	
	public function deleteFile($file) {
		if ( isset($this->files[$file]) ) {
			unlink($this->path."/".$this->files[$file]);
			unset($this->files[$file]);
			return true;
		}
		return false;
	}
	
	public function containsFile($file) {
		return isset($this->files[$file]);
	}
	
	public function getDeleteModalCode($file, $reloadLink) {
		return "
		<div class=\"modal fade\" id=\"modal_delete_".md5($file)."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">
			<div class=\"modal-dialog\">
				<div class=\"modal-content\">
					<div class=\"modal-header\">
						<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
						<h4 class=\"modal-title\" id=\"myModalLabel\">Delete ".$this->getFileType($file)." (".$this->getFileDescription($file).")</h4>
					</div>
					<div class=\"modal-body\">
					Do you really want to delete the ".$this->getFileType($file)." data?
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
	
}
?>