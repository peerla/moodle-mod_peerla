<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface DbPreCourseKnowledgeDataFetcher {
	
	/**
	 * Returns all pre course knowledge estimations for alle users and topics of 
	 * the current course.
	 * 
	 * The returned array has the following structure:
	 * [topicID][userID] => estimation
	 * 
	 * @param int $courseId Course id
	 * @return array Knowledge data for all users and topics
	 */
	function getKnowledgeArray($courseId);
	
	/**
	 * Returns all pre course knowledge estimations of all users for the given topic.
	 * 
	 * The returned array has the following structure:
	 * [userID] => estimation
	 * 
	 * @param int $courseId Course id
	 * @param int $topicId Topic id
	 * @return array Knowledge data for all users
	 */
	function getTopicKnowledgeArray($courseId,$topicId);
	
	/**
	 * Retruns the pre course knowledge estimation for a specific topic and user.
	 * 
	 * @param int $courseId Course id
	 * @param int $topicId Topic id
	 * @param int $userId User id
	 * @return int Pre course knowledge estimation
	 */
	function getUserTopicKnowledge($courseId,$topicId,$userId);
}
