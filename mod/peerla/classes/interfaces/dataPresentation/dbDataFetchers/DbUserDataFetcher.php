<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface DbUserDataFetcher {
	
	/**
	 * Get the user ids of all user with similar knowledge goals for this 
	 * course.
	 * 
	 * @param int $courseId Course id
	 * @param int $topicId TopicId id
	 * @param int $compareUserId Id of the user against which the others are compared
	 * @return int[] User ids
	 */
	function getSimilarKnowledgeGoalUsers($courseId, $topicId, $compareUserId);
	
	/**
	 * Get the user ids of all users which are currently active.
	 * 
	 * A user is considered active, if the users most current interval hasn't 
	 * ended longer ago than a threshold.
	 * 
	 * @param int $courseId Course id
	 * @return array User ids
	 */
	function getActiveCourseUsers($courseId);
	
}
