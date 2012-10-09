<?php
class EPCSelector {
	
	public $xml1 = null;
	public $xml2 = null;
	
	public function __construct(SimpleXMLElement $xml1, SimpleXMLElement $xml2) {
		$this->xml1 = $xml1;
		$this->xml2 = $xml2;
	}
	
	public function getHtml() {
		$html = "EPK 1: <select name='epc1'>";
		foreach ($this->xml1->xpath("//epc") as $epc) {
			$name = utf8_decode((string) $epc["name"]);
			if ( isset($_POST['epc1']) ) {
				$selected = $epc["name"] == $_POST['epc1'] ? "selected" : "";
			} else { 
				$selected = "";
			}
			$html .= "<option value='".$epc["name"]."' ".$selected.">".$name."</option>";
		}
		$html .= "</select> ";
		
		$html .= "EPK 2: <select name='epc2'>";
		foreach ($this->xml2->xpath("//epc") as $epc) {
			$name = utf8_decode((string) $epc["name"]);
			if ( isset($_POST['epc2']) ) {
				$selected = $epc["name"] == $_POST['epc2'] ? "selected" : "";
			} else {
				$selected = "";
			}
			$html .= "<option value='".$epc["name"]."' ".$selected.">".$name."</option>";
		}
		$html .= "</select>";
		
		return $html;
	}
	
}
?>