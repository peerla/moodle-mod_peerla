<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/factories/CourseParticipantFactory.php';

/**
 * Factory for loading CourseParticipant objects from the data base.
 *
 * @author Christoph Bohr
 */
class LazyLoadingCourseParticipantFactory implements CourseParticipantFactory {
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
		require_once realpath(__DIR__).'/../LazyLoadingCourseParticipant.php';
		require_once realpath(__DIR__).'/LazyLoadingCourseTopicFactory.php';
		require_once realpath(__DIR__).'/LazyLoadingLearningIntervalFactory.php';
		require_once realpath(__DIR__).'/LazyLoadingCoursePlaningFactory.php';
	}
	
	public function getAllParticipantsForCourse($courseId) {
		$sql = "SELECT DISTINCT ra.userid"
				. " FROM {context} con"
				. "		LEFT JOIN  {role_assignments} ra"
				. "			ON (ra.contexid = con.id AND ra.roleid = 5)"
				. "	WHERE con.contextlevel = 50 AND con.instanceid = :courseid";
		$participantData = $this->db->get_records_sql(
				$sql,array('courseid' => $courseId));
		
		if (count($participantData) == 0){
			return array();
		}
		
		$topicFactory = new LazyLoadingCourseTopicFactory($this->db);
		$intervalFactory = new LazyLoadingLearningIntervalFactory($this->db);
		$planingFactory = new LazyLoadingCoursePlaningFactory($this->db);
		
		$participants = array();
		foreach($participantData as $participantRow){
			$participant = new LazyLoadingCourseParticipant($courseId, $participantRow->userid);
			$participant->setCoursePlaningFactory($planingFactory);
			$participant->setLearningIntervalFactory($intervalFactory);
			$participant->setCourseTopicFactory($topicFactory);
			$participants[] = $participant;
		}
		
		return $participants;
	}

	public function getCourseParticipant($courseId, $userId) {
		
		$topicFactory = new LazyLoadingCourseTopicFactory($this->db);
		$intervalFactory = new LazyLoadingLearningIntervalFactory($this->db);
		$planingFactory = new LazyLoadingCoursePlaningFactory($this->db);
		
		$participant = new LazyLoadingCourseParticipant($courseId, $userId);
		$participant->setCoursePlaningFactory($planingFactory);
		$participant->setLearningIntervalFactory($intervalFactory);
		$participant->setCourseTopicFactory($topicFactory);
		
		return $participant;
	}

}
