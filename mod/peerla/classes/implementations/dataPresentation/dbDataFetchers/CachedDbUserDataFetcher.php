<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../../interfaces/dataPresentation/dbDataFetchers/DbUserDataFetcher.php';
require_once realpath(__DIR__).'/CachedDbKnowledgeGoalDataFetcher.php';

/**
 * Description of CachedDbUserDataProvider
 *
 * @author Christoph Bohr
 */
class CachedDbUserDataFetcher implements DbUserDataFetcher {
	
	protected $knowledgeGoalProvider;
	protected $similarUsers;
	protected $activeCourseUsers;
	protected $db;
	
	function __construct(\moodle_database $db){
		$this->db = $db;
	}
	
	protected function getMaxInactivityTimestamp(){
		return time() - 60*60*24*3;
	}
	
	public function getActiveCourseUsers($courseId){
		
		if (!isset($this->activeCourseUsers[$courseId])){
			$sql = "SELECT userid FROM {learning_interval} "
					. "	WHERE courseid = :courseid AND current_user_interval = 1"
					. "		AND end_timestamp > :maxtime";
			$params = array(
				'courseid' => $courseId,
				'maxtime' => $this->getMaxInactivityTimestamp()
			);

			$userData = $this->db->get_records_sql($sql,$params);
			
			if (!$userData){
				$this->activeCourseUsers[$courseId] = array();
				return $this->activeCourseUsers[$courseId];
			}
			
			$userIds = array();
			foreach($userData as $user){
				$userIds[] = $user->userid;
			}
			
			$this->activeCourseUsers[$courseId] = $userIds;
		}
		
		return $this->activeCourseUsers[$courseId];
	}
	
	/**
	 * Set a DbKnowGoalDataProvider object, which will be used to find the 
	 * similar knowledge goal users.
	 * 
	 * By providing an existing data provider object this class can profit from
	 * allready cached data of the provider.
	 * 
	 * @param \mod_kom_peerla\DbKnowledgeGoalDataFetcher $provider
	 */
	public function setDbKnowledgeGoalFetcher(DbKnowledgeGoalDataFetcher $provider){
		$this->knowledgeGoalProvider = $provider;
	}
	
	/**
	 * Get the existing DbKnowGoalDataProvider object or create a new one, if 
	 * non allready exists.
	 * 
	 * @return DbKnowledgeGoalDataFetcher
	 */
	protected function getKnowledgeGoalFetcher(){
		if (!isset($this->knowledgeGoalProvider)){
			$this->knowledgeGoalProvider = new CachedDbKnowledgeGoalDataFetcher($this->db);
		}
		
		return $this->knowledgeGoalProvider;
	}
	
	/**
	 * Get the highest difference in knowledge goal estimations, which still
	 * qualifies as similar.
	 * 
	 * @return int
	 */
	protected function getMaxGoalDifference(){
		return 10;
	}

	public function getSimilarKnowledgeGoalUsers($courseId, $topicId, $compareUserId) {
		
		if (!isset($this->similarUsers[$courseId][$compareUserId])){
		
			$maxDifference = $this->getMaxGoalDifference();

			$goalFetcher = $this->getKnowledgeGoalFetcher();
			$goalData = $goalFetcher->getKnowledgeGoalArray($courseId);

			if (!isset($goalData[$compareUserId])){
				$this->similarUsers[$courseId][$compareUserId] = array();
				return $this->similarUsers[$courseId][$compareUserId];
			}

			$userIds = array();
			$targetGoalData = $goalData[$compareUserId];
			foreach($goalData as $userId => $userGoals){
				$hasSimilarGoals = true;
				foreach($targetGoalData as $goalTopicId => $estimation){
					if (!isset($userGoals[$goalTopicId]) || $userId == $compareUserId 
							|| is_null($estimation) || is_null($userGoals[$goalTopicId])
							||  abs($estimation - $userGoals[$goalTopicId]) > $maxDifference ){
						$hasSimilarGoals = false;
					}
				}
				if ($hasSimilarGoals){
					$userIds[] = $userId;
				}
			}
			
			$this->similarUsers[$courseId][$compareUserId] = $userIds;
		}
		
		return $this->similarUsers[$courseId][$compareUserId];
	}

}
