<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface DbTimeInvestmentDataFetcher {
	
	/**
	 * Get the actual time investment for each user and topic.
	 * 
	 * The returned array has the following structure:
	 * [userID][topicid] => invested time
	 * 
	 * @param int $courseId Course id
	 * @return array
	 */
	function getActualTimeInvestmentArray($courseId);
	
	/**
	 * Get the actual time investment of one user for all topic.
	 * 
	 * The returned array has the following structure:
	 * [topicid] => invested time
	 * 
	 * @param int $courseId Course id
	 * @param int $userId User id id
	 * @return array
	 */
	function getActualTimeInvestmentUserArray($courseId,$userId);
	
	/**
	 * Get the actual time investment of one user and topic.
	 * 
	 * This method only returns the investment, which has been directly put in
	 * this specific topic. Investments in parent and child topics will not 
	 * be included.
	 * 
	 * @param int $courseId Course id
	 * @param int $userId User id id
	 * @return array
	 */
	function getDirectTimeInvestmentForTopicAndUser($courseId,$userId,$topicId);
	
	/**
	 * Get the calculated, actual time investment of one user and topic.
	 * 
	 * This method calculates the time investment from the topics parent and 
	 * child topics. Each child topics time investment is added to the topics
	 * time investment. The investement for a parent topic will be equally devided
	 * between all children of that topic.
	 * 
	 * @param int $courseId Course id
	 * @param int $userId User id id
	 * @return array
	 */
	function getCalculatedTimeInvestmentForTopicAndUser($courseId,$userId,$topicId);
	
}
