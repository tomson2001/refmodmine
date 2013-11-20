<?php
class Transition {
	
	public $source;
	public $target;
	public $label;
	
	public function __construct(State $source, State $target, $label=null) {
		$this->source = $source;
		$this->target = $target;
		$this->label = $label;
	}
	
	public function equals(Transition $transition) {
		return $this->source->equals($transition->source) && $this->target->equals($transition->target) && $this->label == $transition->label;
	}
}
?>