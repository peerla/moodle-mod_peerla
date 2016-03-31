<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../../interfaces/dataPresentation/dbDataFetchers/DbPreCourseKnowledgeDataFetcher.php';

/**
 * Description of CachedDbPreCourseKnowledgeDataFetcher
 *
 * @author Christoph Bohr
 */
class CachedDbPreCourseKnowledgeDataFetcher implements DbPreCourseKnowledgeDataFetcher {
	
	protected $knowledgeArray;
	protected $db;
	
	function __construct(\moodle_database $db){
		$this->db = $db;
	}
	
	public function getKnowledgeArray($courseId) {
		if (!isset($this->knowledgeArray[$courseId])){
			$sql = "SELECT tk.id, tk.topicid, tk.userid, tk.knowledge_estimation"
				. "	FROM {pre_course_topic_knowledge} tk "
				. "	WHERE tk.courseid = :courseid";
			
			$params = array(
				'courseid' => $courseId
			);

			$dbData = $this->db->get_records_sql($sql,$params);
			
			if (!$dbData){
				$this->knowledgeArray[$courseId] = array();
				return $this->knowledgeArray[$courseId];
			}
			
			$formatedData = array();
			foreach($dbData as $row){
				$formatedData[$row->topicid][$row->userid] = $row->knowledge_estimation;
			}
			$this->knowledgeArray[$courseId] = $formatedData;
		}
		
		return $this->knowledgeArray[$courseId];
	}
	
	function getTopicKnowledgeArray($courseId,$topicId){
		$dataArray = $this->getKnowledgeArray($courseId);
		if (!isset($dataArray[$topicId])){
			return array();
		}
		return $dataArray[$topicId];
	}
	
	function getUserTopicKnowledge($courseId,$topicId,$userId){
		$dataArray = $this->getTopicKnowledgeArray($courseId, $topicId);
		if (!isset($dataArray[$userId])){
			return 0;
		}
		return $dataArray[$userId];
	}
}
