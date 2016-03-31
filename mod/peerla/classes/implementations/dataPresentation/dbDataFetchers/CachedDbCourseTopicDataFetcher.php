<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../../interfaces/dataPresentation/dbDataFetchers/DbCourseTopicDataFetcher.php';

/**
 * Description of CachedDbCourseTopicDataProvider
 *
 * @author Christoph Bohr
 */
class CachedDbCourseTopicDataFetcher implements DbCourseTopicDataFetcher {
	
	protected $courseTopics;
	protected $db;
	
	protected $topicIdHierarchy;
	protected $topicIdParentHierarchy;
	
	function __construct(\moodle_database $db){
		$this->db = $db;
	}
	
	public function getCourseTopicsDbData($courseId, $userId) {
		if (!isset($this->courseTopics[$userId][$courseId])){
			/** @todo Load all topics in one db query */
			$topicFactory = new LazyLoadingCourseTopicFactory($this->db);
			$this->courseTopics[$userId][$courseId] = $topicFactory
					->getCourseTopicsVisibleToParticipant($courseId, $userId);
		}
		
		return $this->courseTopics[$userId][$courseId];
		
	}
	
	public function getSubTopicHierarchy($topicId){
		//if (!isset($this->topicIdHierarchy[]]))
	}
	
	public function getParentTopicsHierarchy($topicId){
		
	}
	
	protected function generateTopicIdHierarchyArrays(CourseTopic $rootTopic, $parentIds=array()){
		
		$this->topicIdHierarchy[$rootTopic->getTopicId()] = array();
		$this->topicIdParentHierarchy[$rootTopic->getTopicId()] = $parentIds;
		$subTopicIdArray = array();
		
		$subTopics = $rootTopic->getSubTopics();
		if (count($subTopics) == 0){
			return $subTopicIdArray;
		}
		
		array_unshift($parentIds, $rootTopic->getTopicId());
		
		foreach($subTopics as $subTopic){
			$subTopicIdArray[$subTopic->getTopicId()]
					= $this->getTopicIdHierarchyArray($subTopic, $parentIds);
		}
		
		$this->topicIdHierarchy[$rootTopic->getTopicId()] = $subTopicIdArray;
		
		return $subTopicIdArray;
	}

}
