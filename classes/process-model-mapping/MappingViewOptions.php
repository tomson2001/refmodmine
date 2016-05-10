<?php
class MappingViewOptions {
	
	public $posted = null;
	
	private $viewOptions = array(
		'levenshtein_mapping'	=> 'Levenshtein-Mapping',
		'identity'				=> 'Identity-Mapping'
	);
	
	public function __construct() {
		$this->posted = isset($_POST['view']) ? $_POST['view'] : 'levenshtein_mapping';
	}
	
	public function getHtml() {
		$html = "Anzeige: <select name='view'>";
		foreach ( $this->viewOptions as $key => $name ) {
			$html .= "<option value='".$key."'";
			if ( $this->posted == $key ) $html .= " selected";
			$html .= ">".$name."</option>";
		}
		$html .= "</select>";
		return $html;
	}
	
}
?>