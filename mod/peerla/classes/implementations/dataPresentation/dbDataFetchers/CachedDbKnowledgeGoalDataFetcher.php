<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../../interfaces/dataPresentation/dbDataFetchers/DbKnowledgeGoalDataFetcher.php';

/**
 * Description of CachedDbKnowGoalDataProvider
 *
 * @author Christoph Bohr
 */
class CachedDbKnowledgeGoalDataFetcher implements DbKnowledgeGoalDataFetcher {
	
	protected $knowledgeGoalArray;
	protected $db;
	
	function __construct(\moodle_database $db){
		$this->db = $db;
	}
	
	public function getKnowledgeGoalArray($courseId) {
		
		if (!isset($this->knowledgeGoalArray[$courseId])){
			//read all knowledge goals for all topics
			$sql = "SELECT CONCAT(kg.topicid,'#',kg.userid), kg.userid, "
					. "		kg.topicid, kg.estimation "
					. "	FROM {course_topic_knowledge_goal} kg"
					. "		LEFT JOIN {course_topic} t ON (t.id = kg.topicid)"
					. "	WHERE t.courseid = :courseid";
			$params = array(
				'courseid' => $courseId
			);

			$goals = $this->db->get_records_sql($sql,$params);

			if (!$goals){
				$this->knowledgeGoalArray[$courseId] = array();
				return $this->knowledgeGoalArray[$courseId];
			}

			$data = array();
			foreach($goals as $goal){
				$this->knowledgeGoalArray[$courseId][$goal->userid][$goal->topicid]
						= $goal->estimation;
			}
		}
		
		return $this->knowledgeGoalArray[$courseId];
	}

	public function getKnowledgeGoalUserArray($courseId, $userId) {
		$knowledgeArray = $this->getKnowledgeGoalArray($courseId);
		if (!isset($knowledgeArray[$userId])){
			return array();
		}
		return $knowledgeArray[$userId];
	}

	public function getTopicKnowledgeGoalForUser($courseId, $userId, $topicId) {
		$userGoals = $this->getKnowledgeGoalUserArray($courseId, $userId);
		if (!isset($userGoals[$topicId])){
			return 0;
		}
		return $userGoals[$topicId];
	}

}
