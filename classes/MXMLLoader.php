<?php
/**
 * loading/parsing MXML Log files
 * 
 * @author t.thaler
 *
 */
class MXMLLoader {
	
	private $filename;
	private $xml;
	private $aggregate;

	public $processLog;
	
	/**
	 * Constructor
	 * 
	 * @param unknown_type $filename
	 * @param unknown_type $xml
	 * @param unknown_type $aggregate decided whether the ProcessLog should be aggregated or not
	 */
	public function __construct($filename, $xml, $aggregate=false) {
		$this->xml = $xml;
		$this->filename = $filename;
		$this->processLog = new ProcessLog($filename, $aggregate);
		$this->aggregate = $aggregate;
	}
	
	public function addLoadMXML($filename, $xml) {
		$this->xml = $xml;
		$this->filename = $filename;
		$this->load();
	}
	
	/**
	 * Erzeugt ein ProcessLog aus einer MXML-Datei
	 * @return number
	 */
	public function load() {
		
		print("\nLoad ".$this->filename." ... \n");
		$containedProcessInstances = count($this->xml->xpath("//ProcessInstance"));
		$progressBar = new CLIProgressbar($containedProcessInstances, 0.1);
		
		$loadedTraces = 0;
		
		foreach ($this->xml->xpath("//ProcessInstance") as $xml_trace) {
			$id = utf8_decode((string) $xml_trace["id"]);
			
			$trace = new Trace($id);
			$positionInTrace = 0;
			
			foreach ($this->xml->xpath("//ProcessInstance[@id='".$id."']/AuditTrailEntry") as $traceEntry) {
				$positionInTrace++;
				$activity = rtrim(ltrim(utf8_decode($this->convertIllegalChars($traceEntry->WorkflowModelElement))));
				$type = rtrim(ltrim(utf8_decode($this->convertIllegalChars($traceEntry->EventType))));
				$originator = rtrim(ltrim(utf8_decode($this->convertIllegalChars($traceEntry->Originator))));
				$timestamp = rtrim(ltrim(utf8_decode($this->convertIllegalChars($traceEntry->Timestamp))));
				$pot = strtotime($timestamp);
				$traceEntry = new TraceEntry($activity, $pot, $originator, $type, $positionInTrace);
				$trace->addTraceEntry($traceEntry);
			}

			if ( $this->aggregate ) $trace->makeEntriesRepresentative();
			$this->processLog->addTrace($trace, $this->filename);
			$loadedTraces++;
			$progressBar->run($loadedTraces);
		}
		return $this->processLog;
	}
	
	public function convertIllegalChars($string) {
		$string = str_replace("�", "Ae", $string);
		$string = str_replace("�", "ae", $string);
		$string = str_replace("�", "Oe", $string);
		$string = str_replace("�", "oe", $string);
		$string = str_replace("�", "Ue", $string);
		$string = str_replace("�", "ue", $string);
		$string = str_replace("�", "ss", $string);
		$string = str_replace("\n", " ", $string);
		return $string;
	}

}
?>