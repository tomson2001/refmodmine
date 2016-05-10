<?php
/**
 * http://www.win.tue.nl/vis1/home/apretori/data/fsm.html
 *
 * @author t.thaler
 */
class ReachabilityGraph {

	private $epc;
	public $states = array();
	public $transitions = array();
	
	public $complete = true;
	public $countStates = 0;

	public function __construct(EPC $epc) {
		$this->epc = $epc;
	}

	/**
	 * Fuegt einen Zustand dem Graphen hinzu
	 *
	 * @param State $state
	 * @return State
	 */
	public function addState(State $state) {
		$stateCheck = $this->containsState($state);
		if ( $stateCheck === false ) {
			$this->countStates++;
			//print($this->countStates.".");
			array_push($this->states, $state);
			return $state;
		}
		return $stateCheck;
	}

	/**
	 * Prueft anhand der vorgelagerten Knoten, ob der Zustands bereits im Graphen existiert
	 *
	 * @param State $state
	 * @return State / false
	 */
	public function containsState(State $state) {
		foreach ($this->states as $currState) {
			if ( $currState->equals($state) ) return $currState;
		}
		return false;
	}

	public function addTransition($sourceState, $targetState, $label=null) {
		if ( !is_null($sourceState) && !is_null($targetState) ) {
			$transition = new Transition($sourceState, $targetState, $label);
			if ( !$this->containsTransition($transition) ) {
				array_push($this->transitions, $transition);
				//print("\n   - add Transition: ".$sourceState->id." => ".$targetState->id." (".$label.")");
				return true;
			}
		}
		return false;
	}
	
	public function containsTransition($transition) {
		foreach ( $this->transitions as $currTrans ) {
			if ( $currTrans->equals($transition) ) return true;
		}
		return false;
	}

	public function getNumOfStates() {
		return count($this->states);
	}

	public function getNumOfTransitions() {
		return count($this->transitions);
	}

	public function exportFSM() {
		$content =  "---\n---";

		foreach ( $this->transitions as $transition ) {
			//if ( !empty($transition->target->tokens) ) 
				$content .= "\n\"".$transition->source->id."\" \"".$transition->target->id."\" \"".$transition->label."\"";
		}

		$fileGenerator = new FileGenerator(trim($this->epc->name).".fsm", $content."\n");
		$file = $fileGenerator->execute();
		return $file;
	}

}
?>