<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface LearningIntervalFactory {
	
	/**
	 * Get a learning interval with a specific id.
	 * 
	 * @return LearningInterval|null Learning interval
	 */
	function getLearningInterval($intervalId);
	
	/**
	 * Get the Learning Interval that directly precedes the given interval.
	 * 
	 * @param int $intervalId The id of the referenced interval
	 * @return LearningInterval|null The preceding learning interval
	 */
	function getPreviousLerningInterval($intervalId);
	
	/**
	 * Get the Learning Interval that directly follows the given interval.
	 * 
	 * @param int $intervalId The id of the referenced interval
	 * @return LearningInterval|null The following learning interval
	 */
	function getFollowingLerningInterval($intervalId);
	
	/**
	 * Get all learning intervals for a specific user and course.
	 * 
	 * @param int $courseId Course id
	 * @param int $userId User id
	 * @return LearningInterval[] Learning intervals
	 */
	function getParticipantLearningIntervals($courseId, $userId);
	
	/**
	 * Get the currently active learning interval for a specific user and course.
	 * 
	 * @param int $courseId Course id
	 * @param int $userId User id
	 * @return LearningInterval|null Learning interval
	 */
	function getParticipantCurrentLearningInterval($courseId, $userID);
}
