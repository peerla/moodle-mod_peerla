<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/IntervalGoalAction.php';

/**
 * Description of BasicIntervalGoalAction
 *
 * @author Christoph Bohr
 */
class BasicIntervalGoalAction implements IntervalGoalAction {
	
	protected $actionId;
	protected $actionName;
	
	public function getActionId() {
		return $this->actionId;
	}
	
	/**
	 * Set the action database id.
	 * 
	 * @param type $actionId
	 */
	public function setActionId($actionId){
		$this->actionId = $actionId;
	}

	public function getActionName() {
		return $this->actionName;
	}
	
	/**
	 * Set the action name.
	 * 
	 * @param type $name
	 */
	public function setActionName($name){
		$this->actionName = $name;
	}

}
