<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface IntervalGoalAction {
	
	/**
	 * Get the db id of this action.
	 * 
	 * @return int Database id
	 */
	function getActionId();
	
	/**
	 * Return the name of the action.
	 * 
	 * @return string Action name
	 */
	function getActionName();
	
}
