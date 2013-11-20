<?php 
/**
 * Erstellung des Erreichbarkeitsgraphen nach Mendling
 * siehe dazu Mendling Diss., ab. S. 70
 *
 * @author t.thaler
 *
 */
class ReachabilityGraphGeneratorMendling {

	public $epc;
	public $reachabilityGraph;
	
	public $max_execution_time;
	public $startTime;

	public function __construct(EPC &$epc, $max_execution_time = 0) {
		$this->reachabilityGraph = new ReachabilityGraph($epc);
		$this->epc = &$epc;
		$this->max_execution_time = $max_execution_time;
		$this->startTime = time();
	}

	public function execute() {
		if ( !$this->epc->isSyntaxCorrect() ) {
			$this->epc->tryToCorrectSyntax();
			if ( !$this->epc->isSyntaxCorrect() ) return "Syntax error in EPC \"".$this->epc->name."\".";
		}
		$initialMakings = $this->getInitialMarkings();
		if ( is_string($initialMakings) ) return $initialMakings;
		$this->recursiveGraphGeneration(null, $initialMakings);
		return $this->reachabilityGraph;
	}

	public function recursiveGraphGeneration($oldState, $newMarkings) {
		// Setze ein Dummy-Initial-Marking vor alle Initial Markings
		if ( is_null($oldState) && count($newMarkings) > 1 ) {
			$oldState = $this->reachabilityGraph->addState(new State(array()));
		}
		
		// Zeitueberschreitung pruefen
		if ( time() - $this->startTime > $this->max_execution_time ) {
			$this->reachabilityGraph->complete = false;
			return "Time exceeded (current state count: ".$this->reachabilityGraph->getNumOfStates().")";
		}
		
		foreach ( $newMarkings as $marking ) {

			// Neuen Zustand zum Erreichbarkeitsgraph hinzufuegen
			$propagationState = $marking->convertToState();
			$state = $this->reachabilityGraph->addState($propagationState);

			// Transition hinzufuegen
			if ( !is_null($oldState) ) {
				$transitionAdded = $this->reachabilityGraph->addTransition($oldState, $state, $marking->label);
				if ( $transitionAdded ) {
					$newMarkings = $marking->computeNextMarkings();
					$this->recursiveGraphGeneration($state, $newMarkings);
				}
			} else {
				$newMarkings = $marking->computeNextMarkings();
				$this->recursiveGraphGeneration($state, $newMarkings);
			}
				
		}
	}

	public function getInitialMarkings() {
		$initialMarkingGenerator = new InitialMarkingGenerator($this->epc);
		return $initialMarkingGenerator->execute();
	}

}
?>