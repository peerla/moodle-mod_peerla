<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/factories/LearningIntervalFactory.php';
require_once realpath(__DIR__).'/LazyLoadingCourseTopicFactory.php';
require_once realpath(__DIR__).'/LazyLoadingLearningIntervalGoalFactory.php';
require_once realpath(__DIR__).'/../LazyLoadingLearningInterval.php';

/**
 * Description of DbLearningIntervalFactory
 *
 * @author Christoph Bohr
 */
class LazyLoadingLearningIntervalFactory implements LearningIntervalFactory{
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
	}
	
	public function getLearningInterval($intervalId) {
		$sql = "SELECT id, courseid, userid, start_timestamp, end_timestamp,"
				. "		knowledge_timestamp, goal_update_timestamp,"
				. "		retrospective_good, retrospective_bad"
				. "	FROM {learning_interval}"
				. "	WHERE id = :intervalid";
		$intervalData = $this->db->get_record_sql(
				$sql, array('intervalid' => $intervalId));
		
		if ($intervalData === false){
			return null;
		}
		
		return $this->initLearningInterval($intervalData);
	}
	
	protected function initLearningInterval($dbData){
		
		$interval = new LazyLoadingLearningInterval();
		$interval->setIntervalId($dbData->id);
		$interval->setCourseId($dbData->courseid);
		$interval->setUserId($dbData->userid);
		$interval->setStartDate($dbData->start_timestamp);
		$interval->setEndDate($dbData->end_timestamp);
		$interval->setRetrospectiveGoodText($dbData->retrospective_good);
		$interval->setRetrospectiveBadText($dbData->retrospective_bad);
		
		if (!is_null($dbData->retrospective_good) || !is_null($dbData->retrospective_bad)){
			$interval->setRetrospectiveDone(true);
		}
		
		if ($dbData->goal_update_timestamp){
			$interval->setUserHasFinishedGoalSetting(TRUE);
		}
		
		if ($dbData->knowledge_timestamp){
			$interval->setUserHasFinishedKnowledgeEstimation(TRUE);
		}
		
		$goalFactory = new LazyLoadingLearningIntervalGoalFactory($this->db);
		$topicFactory = new LazyLoadingCourseTopicFactory($this->db);
		
		$interval->setTopicFacotry($topicFactory);
		$interval->setGoalFactory($goalFactory);
		$interval->setLearningIntervalFactory($this);
		
		return $interval;
	}

	public function getParticipantCurrentLearningInterval($courseId, $userID) {
		$sql = "SELECT id, courseid, userid, start_timestamp, end_timestamp,"
				. "		knowledge_timestamp, goal_update_timestamp,"
				. "		retrospective_good, retrospective_bad"
				. "	FROM {learning_interval}"
				. "	WHERE userid = :userid"
				. "		AND courseid = :courseid"
				. "		AND current_user_interval = 1";
		$intervalData = $this->db->get_record_sql(
				$sql, 
				array('userid' => $userID, 'courseid' => $courseId)
		);
		
		if (!$intervalData){
			return null;
		}
		
		return $this->initLearningInterval($intervalData);
	}

	public function getParticipantLearningIntervals($courseId, $userId) {
		return array();
	}

	public function getFollowingLerningInterval($intervalId) {
		
		$referenceInterval = $this->getLearningInterval($intervalId);
		
		if (is_null($referenceInterval)){
			return null;
		}
		
		$sql = "SELECT id, courseid, userid, start_timestamp, end_timestamp,"
				. "		knowledge_timestamp, goal_update_timestamp,"
				. "		retrospective_good, retrospective_bad"
				. "	FROM {learning_interval}"
				. "	WHERE userid = :userid"
				. "		AND courseid = :courseid"
				. "		AND start_timestamp > :refstart"
				. "	ORDER BY start_timestamp ASC";
		$intervalData = $this->db->get_records_sql(
				$sql, 
				array(
					'courseid' => $referenceInterval->getCourseId(),
					'userid' => $referenceInterval->getUserId(),
					'refstart' => $referenceInterval->getStartDate()
				),
				0,1
		);
		
		if (count($intervalData) != 1){
			return null;
		}
		
		return $this->initLearningInterval($intervalData);
	}

	public function getPreviousLerningInterval($intervalId) {
		$referenceInterval = $this->getLearningInterval($intervalId);
		
		if (is_null($referenceInterval)){
			return null;
		}
		
		$sql = "SELECT id, courseid, userid, start_timestamp, end_timestamp,"
				. "		knowledge_timestamp, goal_update_timestamp,"
				. "		retrospective_good, retrospective_bad"
				. "	FROM {learning_interval}"
				. "	WHERE userid = :userid"
				. "		AND courseid = :courseid"
				. "		AND start_timestamp < :refstart"
				. "	ORDER BY start_timestamp DESC";
		$intervalData = $this->db->get_records_sql(
				$sql, 
				array(
					'courseid' => $referenceInterval->getCourseId(),
					'userid' => $referenceInterval->getUserId(),
					'refstart' => $referenceInterval->getStartDate()
				),
				0,1
		);
		
		if (count($intervalData) != 1){
			return null;
		}
		
		return $this->initLearningInterval($intervalData);
	}

}
