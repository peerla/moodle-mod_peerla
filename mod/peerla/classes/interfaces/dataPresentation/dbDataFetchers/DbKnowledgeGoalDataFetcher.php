<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface DbKnowledgeGoalDataFetcher {
	
	/**
	 * Get the knowledge goal estimations for all users and topics.
	 * 
	 * The returned array has the following structure:
	 * [userID][topicid] => estimation
	 * 
	 * @param int $courseId Course id
	 * @return array
	 */
	function getKnowledgeGoalArray($courseId);
	
	/**
	 * Get the knowledge goal estimations of one users for all his topics.
	 * 
	 * The returned array has the following structure:
	 * [topicid] => estimation
	 * 
	 * @param int $courseId Course id
	 * @param int $userId User id
	 * @return array
	 */
	function getKnowledgeGoalUserArray($courseId,$userId);
	
	/**
	 * Get the estimated knowledge goal of the given user and topic.
	 * 
	 * @param int $courseId Course id
	 * @param int $userId User id
	 * @param int $topicId Topic id
	 * @return int Knowledge goal estimation
	 */
	function getTopicKnowledgeGoalForUser($courseId,$userId,$topicId);
}
