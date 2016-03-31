<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/factories/CourseGoalFactory.php';
require_once realpath(__DIR__).'/../BasicCourseGoal.php';

/**
 * Description of DbCourseGoalFactory
 *
 * @author Christoph Bohr
 */
class LazyLoadingCourseGoalFactory implements CourseGoalFactory{
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
		//require_once realpath(__DIR__).'/../Db.php';
	}
	
	public function getCourseGoals($courseId) {
		$sql = "SELECT g.id, g.goal_text, g.userid, g.create_timestamp"
				. "	FROM {course_goal} g"
				. "	WHERE g.courseid = :courseid";
		
		$courseData = $this->db->get_records_sql($sql,array('courseid' => $courseId));
		
		if (count($courseData) == 0){
			return array();
		}
		
		$goals = array();
		
		foreach($courseData as $data){
			$goal = new BasicCourseGoal();
			$goal->setCourseId($courseId);
			$goal->setUserId($data->userid);
			$goal->setGoalText($data->goal_text);
			$goal->setCreateTimestamp($data->create_timestamp);
			$goal->setGoalId($data->id);
			$goals[] = $goal;
		}
		
		return $goals;
	}

	public function getParticipantCourseGoals($courseId, $userId) {
		
		$sql = "SELECT g.id, g.goal_text, g.create_timestamp"
				. "	FROM {course_goal} g"
				. "	WHERE g.courseid = :courseid AND g.userid = :userid";
		
		$courseData = $this->db->get_records_sql($sql,array(
			'userid' => $userId, 'courseid' => $courseId
		));
		
		if (count($courseData) == 0){
			return array();
		}
		
		$goals = array();
		
		foreach($courseData as $data){
			$goal = new BasicCourseGoal();
			$goal->setCourseId($courseId);
			$goal->setUserId($userId);
			$goal->setGoalText($data->goal_text);
			$goal->setCreateTimestamp($data->create_timestamp);
			$goal->setGoalId($data->id);
			$goals[] = $goal;
		}
		
		return $goals;
	}

}
