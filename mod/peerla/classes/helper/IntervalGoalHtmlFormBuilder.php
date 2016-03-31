<?php

/**
 * Description of IntervalGoalHtmlFormBuilder
 *
 * @author Christoph Bohr
 */
class IntervalGoalHtmlFormBuilder {
	
	protected $allowMultipleGoals = false;
	
	/**
	 * Set if the form should allow adding and removing of goals.
	 * 
	 * @param type $allow
	 */
	public function allowMultipleGoals($allow=true){
		$this->allowMultipleGoals = $allow;
	}
	
}
