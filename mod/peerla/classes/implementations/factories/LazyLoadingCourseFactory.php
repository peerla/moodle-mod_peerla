<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/factories/CourseFactory.php';
/**
 * Description of DbCourseFactory
 *
 * @author Christoph Bohr
 */
class LazyLoadingCourseFactory implements CourseFactory {
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
		require_once realpath(__DIR__).'/LazyLoadingCourseParticipantFactory.php';
		require_once realpath(__DIR__).'/../LazyLoadingCourse.php';
	}
	
	public function getCourseFromModuleInstanceId($moduleInstanceId){
		$cm = get_coursemodule_from_instance('peerla', $moduleInstanceId);
		
		if ($cm === false){
			return null;
		}
		
		return $this->getCourse($cm->course);
	}
	
	function getCourseFromCourseModuleId($courseModuleId){
		$cm = get_coursemodule_from_id('peerla', $courseModuleId);
		
		if ($cm === false){
			return null;
		}
		
		return $this->getCourse($cm->course);
	}

	public function getCourse($courseId) {
		$sql = "SELECT id, fullname, shortname, startdate "
				. "	FROM {course} c"
				. " WHERE c.id = :courseid";
		$courseData = $this->db->get_record_sql($sql,array('courseid' => $courseId));
		
		if ($courseData === false){
			return null;
		}
		
		$participantFactory = new LazyLoadingCourseParticipantFactory($this->db);
		$course = new LazyLoadingCourse($courseId, $participantFactory);
		
		$course->setName($courseData->fullname);
		$course->setShortName($courseData->shortname);
		$course->setStartDate($courseData->startdate);
		
		return $course;
	}

	public function getCurrentCoursesForUser($userId) {
		/**
		 * @todo Unterschied zwischen aktuellen und alten kursen finden
		 */
		return $this->getCoursesForUser($userId);
	}

	public function getCoursesForUser($userId) {
		
		$sql = "SELECT c.id, c.fullname, c.shortname, c.startdate"
				. "	FROM {course} c"
				. "	WHERE c.id IN "
				. "		(SELECT con.instanceid "
				. "			FROM {role_assignments} ra "
				. "				LEFT JOIN {context} con "
				. "					ON (ra.contextid = con.id "
				. "						AND con.contextlevel = 50)"
				. "			WHERE ra.userid = :userid AND ra.roleid = 5)";
		
		$courseData = $this->db->get_records_sql($sql,array('userid' => $userId));
		
		if (count($courseData) == 0){
			return array();
		}
		
		$courses = array();
		$participantFactory = new LazyLoadingCourseParticipantFactory($this->db);
		
		foreach($courseData as $courseRow){
			$course = new LazyLoadingCourse($courseRow->id, $participantFactory);
			$course->setName($courseRow->fullname);
			$course->setShortName($courseRow->shortname);
			$course->setStartDate($courseRow->startdate);
			$courses[] = $course;
		}
		
		return $courses;
		
	}

}
