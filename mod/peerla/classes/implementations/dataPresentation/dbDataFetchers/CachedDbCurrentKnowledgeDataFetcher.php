<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../../interfaces/dataPresentation/dbDataFetchers/DbCurrentKnowledgeDataFetcher.php';

/**
 * Description of CachedDbCurrentKnowledgeDataProvider
 *
 * @author Christoph Bohr
 */
class CachedDbCurrentKnowledgeDataFetcher implements DbCurrentKnowledgeDataFetcher {
	
	protected $knowledgeArray;
	protected $db;
	
	function __construct(\moodle_database $db){
		$this->db = $db;
	}
	
	/**
	 * Returns the age of a current learning interval to be considered in the 
	 * calculations. 
	 * 
	 * The age is the time since the last interval ended.
	 * 
	 * @return int Seconds since interval end
	 */
	protected function getMaxAgeForIntervals(){
		return time() - 60*60*24*7*3;
	}
	
	public function getKnowledgeArray($courseId) {
		if (!isset($this->knowledgeArray[$courseId])){
			$sql = "SELECT CONCAT(tk.topicid,'#',tk.userid), tk.topicid,tk.userid,tk.knowledge_estimation"
					. "	FROM {topic_knowledge} tk"
					. "		LEFT JOIN {learning_interval} i ON (i.id = tk.intervalid)"
					. "	WHERE i.courseid = :courseid"
					. "		AND i.current_user_interval = 1"
					. "		AND i.end_timestamp > :mintimestamp";
			$params = array(
				'courseid' => $courseId,
				'mintimestamp' => $this->getMaxAgeForIntervals()
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
