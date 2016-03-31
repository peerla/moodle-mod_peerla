<?php

namespace mod_kom_peerla;

/**
 * 
 *
 * @author Christoph Bohr
 */
interface CourseGoal {
	
	/**
	 * Get the goal text.
	 * 
	 * @return string Goal text
	 */
	function getGoalText();
	
	/**
	 * Get the user id.
	 * 
	 * @return int User id
	 */
	function getUserId();
	
	/**
	 * Get the course id.
	 * 
	 * @return int Course id
	 */
	function getCourseId();
	
	/**
	 * Get the unique goal id.
	 * 
	 * @return int Goal id
	 */
	function getGoalId();
	
	/**
	 * Returns if the goal is still set by the user.
	 * 
	 * If this function returns false, the goal was deleted or replaced by
	 * the user. 
	 * 
	 * @return bool True, if current goal, false otherwise
	 */
	function isActive();
	
	/**
	 * Returns the timestamp of the goals creation.
	 * 
	 * @return int Unix timestamp
	 */
	function getCreateTimestamp();
	
	/**
	 * Returns the timestamp of the goals deactivation.
	 * 
	 * @return int|null Deactivation timestamp
	 */
	function getDeactivationTimestamp();
	
}
