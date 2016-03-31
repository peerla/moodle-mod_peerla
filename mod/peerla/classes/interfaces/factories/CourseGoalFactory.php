<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface CourseGoalFactory {
	
	/**
	 * Get all course goals of all users for a specific course.
	 * 
	 * @param int $courseId Course id
	 * @return CourseGoal[] Course goals
	 */
	function getCourseGoals($courseId);
	
	/**
	 * Get all course goals for a specific course and user.
	 * 
	 * @param int $courseId Course id
	 * @param int $userId User id
	 * @return \mod_kom_peerla\CourseGoal[] Users course goals
	 */
	function getParticipantCourseGoals($courseId, $userId);
	
}
