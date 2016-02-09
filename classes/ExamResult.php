<?php
class ExamResult {
	
	public $epc;							// the student epc
	public $mappingToSampleSolution = null;	// a binary mapping to the sample solution (see IMapping and AMapping)
	
	public $syntaxErrors = array();			// textual representions of syntax errors ==> see class EPC
	public $syntaxWarnings = array(); 		// textual represention of syntax warnings ==> see class EPC
	
	// Existing activities with regard to sample solution
	public $activityRecall;		// the recall of activities which are available in the sample solution
	
	public $truePositives;		// true positives value (numeric - int)
	public $falsePositives;		// false positives value (numeric - int)
	public $falseNegatives;		// false negatives value (numeric - int)
	
	public $truePositivesLabels = array();	// labels wich are also available in the sample solution
	public $falsePositivesLabels = array();	// labels which are not available in the sample solution
	public $falseNegativesLabels = array();	// missing labels with regard to the samplbe solution
	
	public $cheatingAttempts = array();	// array of epc name to which a cheating attempt was detected
	
	// Trace information
	public $traces;			// array of traces
	public $tracePrecision;	// precision to sample solution
	public $traceRecall;	// recall to sample solution
	public $traceFMeasure;	// f-measure to sample solutionv
		
	public $maxPoints;
	public $weightSyntax;
	public $weightFunctionRecall;
	public $weightBehaviouralFMeasure;
	public $maxSyntaxErrors;
	
	public $score;
	
	public function __construct(EPC $epc) {
		$this->epc = $epc;
	}
	
	public function checkSyntax() {
		$issues = $this->epc->getSyntaxErrors();
		$this->addSytaxResults($issues);
	}
	
	public function addSytaxResults($issues) {
		foreach ( $issues as $issue ) {
			if ( Tools::startsWith($issue, "Error:") ) array_push($this->syntaxErrors, $issue);
			if ( Tools::startsWith($issue, "Warning:") ) array_push($this->syntaxWarnings, $issue);
		}
	}
	
	public function getNumSyntaxIssues() {
		return count($this->syntaxErrors) + count($this->syntaxWarnings);
	}
	
	public function getNumSyntaxErrors() {
		return count($this->syntaxErrors);
	}
	
	public function getNumSyntaxWarnings() {
		return count($this->syntaxWarnings);
	}
	
	public function getRecallOfFunctionNodes($numDifferentFuncsInSampleSolution) {
		if ( is_null($this->mappingToSampleSolution) ) return null;
		
		//print("(".$this->mappingToSampleSolution->epc1->name." => ".$this->mappingToSampleSolution->epc2->name.")");
		
		$checkedLabels = array();
		$truePositives = 0;
		$falsePositives = 0;
		foreach ( $this->epc->functions as $nodeID => $label ) {
			if ( $targetNodeID = $this->mappingToSampleSolution->mappingExistsFrom($nodeID) ) {
				//print ( $label);
				$targetLabel = $this->mappingToSampleSolution->epc2->functions[$targetNodeID];
				if ( in_array($targetLabel, $checkedLabels) ) continue;
				array_push($checkedLabels, $targetLabel);
				array_push($this->truePositivesLabels, $targetLabel);
				$truePositives++;
			} else {
				//print("FP: ".$label." ");
				array_push($this->falsePositivesLabels, $label);
				$falsePositives++;
			}
		}
		
		// receiving the false negative labels (missing labels with regard to sample solution
		foreach ( $this->mappingToSampleSolution->epc2->functions as $ssNodeID => $label ) {
			if ( !in_array($label, $this->truePositivesLabels) ) array_push($this->falseNegativesLabels, $label);
		}
		
		//$numFuncsInSampleSolution must contain different labels!
		$falseNegatives = $numDifferentFuncsInSampleSolution - $truePositives;
		$this->activityRecall = round($truePositives / ($truePositives+$falseNegatives), 3);
		$this->truePositives = $truePositives;
		$this->falsePositives = $falsePositives;
		$this->falseNegatives = $falseNegatives;
		
		return $this->activityRecall;
	}
	
	/**
	 * Checks Cheating Attempts based on the common nodes and edges similarity
	 * @param EPC $epc1
	 * @param EPC $epc2
	 * @return bool
	 */
	public static function checkCheatingAttempt(EPC $epc1, EPC $epc2, $similarityThreshold=0.95) {
		
		$funcIntersection = array_intersect($epc1->functions, $epc2->functions);
		$eventIntersection = array_intersect($epc1->events, $epc2->events);
		
		$edges1 = array();
		foreach ( $epc1->edges as $edge ) {
			foreach ( $edge as $sourceID => $targetID ) {
				$edgeString = $epc1->getLabel($sourceID)." => ".$epc1->getLabel($targetID);
				array_push($edges1, $edgeString);
			}
		}
		
		$edges2 = array();
		foreach ( $epc2->edges as $edge ) {
			foreach ( $edge as $sourceID => $targetID ) {
				$edgeString = $epc2->getLabel($sourceID)." => ".$epc2->getLabel($targetID);
				array_push($edges2, $edgeString);
			}
		}
		
		$edgeIntersection = array_intersect($edges1, $edges2);
		
		$commonNodesAndEdgesSimilarity = (2*(count($funcIntersection)+count($eventIntersection)+count($edgeIntersection)))/(count($epc1->functions)+count($epc1->events)+count($epc1->edges)+count($epc2->functions)+count($epc2->events)+count($epc2->edges));
		
		//print("   Percentage of common nodes and edges between ".$epc1->name." - ".$epc2->name.": ".$commonNodesAndEdgesSimilarity."\n");
// 		print_r($funcIntersection);
// 		print_r($epc1->functions);
// 		print_r($epc2->functions);
		
		if ( $commonNodesAndEdgesSimilarity >= $similarityThreshold ) {
			return $commonNodesAndEdgesSimilarity;
		} else {
			return false;
		}
	}
	
	public function score($maxPoints=20, $maxSyntaxErrors=5, $weightSyntax=2, $weightFunctionRecall=2, $weightBehaviouralFMeasure=1) {
		$this->maxPoints = $maxPoints;
		$this->maxSyntaxErrors = $maxSyntaxErrors;
		$this->weightSyntax = $weightSyntax;
		$this->weightFunctionRecall = $weightFunctionRecall;
		$this->weightBehaviouralFMeasure = $weightBehaviouralFMeasure;
		
		$numSyntaxErrors = count($this->syntaxErrors);
		$syntaxScore = $numSyntaxErrors > $maxSyntaxErrors ? 0 : 1 - ($numSyntaxErrors/$maxSyntaxErrors);
		
		$traceFMeasure = $weightBehaviouralFMeasure > 0 ? $this->traceFMeasure : 1;
		
		$overallScore = (($weightSyntax*$syntaxScore)+($weightFunctionRecall*$this->activityRecall)+($weightBehaviouralFMeasure*$traceFMeasure)) / ($weightSyntax + $weightFunctionRecall + $weightBehaviouralFMeasure);
		$overallScore *= $maxPoints;
		$overallScore = round($overallScore, 1, PHP_ROUND_HALF_UP);
		
		$this->score = $overallScore;
		return $overallScore;
		
	}
	
}
?>
