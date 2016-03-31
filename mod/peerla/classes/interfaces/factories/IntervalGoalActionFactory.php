<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface IntervalGoalActionFactory {
	
	/**
	 * Get the interval goal action object with the given database id.
	 * 
	 * @return \mod_kom_peerla\IntervalGoalAction Action object
	 */
	function getAction($actionId);
	
	/**
	 * Get all actions visible to the given user.
	 * 
	 * @return \mod_kom_peerla\IntervalGoalAction[] Array of actions
	 */
	function getActionsVisibleToUser($userId);
	
}
