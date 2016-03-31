<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/factories/CoursePlaningFactory.php';

/**
 * Description of LazyLoadingCoursePlaningFactory
 *
 * @author Christoph Bohr
 */
class LazyLoadingCoursePlaningFactory implements CoursePlaningFactory{
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
		
		require_once realpath(__DIR__).'/LazyLoadingCourseTopicFactory.php';
		require_once realpath(__DIR__).'/LazyLoadingUserFactory.php';
		require_once realpath(__DIR__).'/LazyLoadingCourseFactory.php';
		require_once realpath(__DIR__).'/LazyLoadingCourseGoalFactory.php';
		require_once realpath(__DIR__).'/../LazyLoadingCoursePlaning.php';
	}
	
	public function getCurrentCoursePlanings($courseId) {
		return array();
	}

	public function getParticipantCoursePlaning($courseId, $userId) {
		$sql = "SELECT id, aspired_mark, create_timestamp, courseid, userid,"
				. "		course_goal_update_timestamp, topic_goal_update_timestamp,"
				. "		course_know_update_timestamp"
				. "	FROM {course_plan} p"
				. " WHERE p.courseid = :courseid"
				. "		AND p.userid = :userid";
		$courseData = $this->db->get_record_sql($sql,array(
			'courseid' => $courseId,
			'userid' => $userId
		));
		
		if ($courseData === false){
			return null;
		}
		
		return $this->initCoursePlan($courseData);
	}
	
	/**
	 * 
	 * @param mixed $dbData
	 * @return CoursePlaning Course Planing object
	 */
	protected function initCoursePlan($dbData){
		
		$userFactory = new LazyLoadingUserFactory($this->db);
		$goalFactory = new LazyLoadingCourseGoalFactory($this->db);
		$courseFactory = new LazyLoadingCourseFactory($this->db);
		$topicFactory = new LazyLoadingCourseTopicFactory($this->db);
		
		$planing = new LazyLoadingCoursePlaning($dbData->courseid, $dbData->userid);
		$planing->setUserFactory($userFactory);
		$planing->setCourseGoalFactory($goalFactory);
		$planing->setCourseFactory($courseFactory);
		$planing->setCourseTopicFactory($topicFactory);
		
		$planing->setAspiredMark($dbData->aspired_mark);
		$planing->setDeactivationTimestamp($dbData->create_timestamp);
		
		if ($dbData->course_goal_update_timestamp){
			$planing->setUserHasFinishedCourseGoalSetting(TRUE);
		}
		
		if ($dbData->topic_goal_update_timestamp){
			$planing->setUserHasFinishedGoalToTopicTransformation(TRUE);
		}
		
		if ($dbData->course_know_update_timestamp){
			$planing->setUserHasFinishedPreCourseKnowledgeSetting(TRUE);
		}
		
		return $planing;
	}

}
