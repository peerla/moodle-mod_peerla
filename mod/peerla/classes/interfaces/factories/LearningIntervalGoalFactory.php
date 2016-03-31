<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface LearningIntervalGoalFactory {
	
	/**
	 * Get all learning interval goals for the given interval.
	 * 
	 * @return LearningIntervalGoal[] Array of learning interval goals
	 */
	function getIntervalGoals($intervalId);
	
}
