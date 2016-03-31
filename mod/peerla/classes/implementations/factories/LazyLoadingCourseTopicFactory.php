<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/factories/CourseTopicFactory.php';
require_once realpath(__DIR__).'/../LazyLoadingKnowledgeGoal.php';
require_once realpath(__DIR__).'/../LazyLoadingIntervalKnowledge.php';
require_once realpath(__DIR__).'/../LazyLoadingCourseTopic.php';

/**
 * Description of DbCourseTopicFactory
 *
 * @author Christoph Bohr
 */
class LazyLoadingCourseTopicFactory implements CourseTopicFactory{
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
	}
	
	public function getAllNonPublicCourseTopics($courseId) {
		
		return array();
	}
	
	public function getCourseTopicsVisibleToParticipant($courseId, $userId) {
		
		$sql = "SELECT t.id, t.name, t.parentid, t.public, t.creator_userid,"
				. "		t.create_timestamp, t.courseid, gt.estimation, "
				. "		gt.create_timestamp AS 'estimation_timestamp'"
				. "	FROM {course_topic} t"
				. "		LEFT JOIN {course_topic_knowledge_goal} gt "
				. "			ON (gt.topicid = t.id AND gt.userid = :userid1)"
				. "	WHERE t.courseid = :courseid"
				. "		AND t.delete_timestamp IS NULL"
				. "		AND t.parentid IS NULL"
				. "		AND (t.creator_userid IS NULL OR t.creator_userid = :userid2)";
		
		$topicsData = $this->db->get_records_sql($sql,array(
			'courseid' => $courseId,'userid1' => $userId,'userid2' => $userId));
		
		if (count($topicsData) == 0){
			return array();
		}
		
		$topics = array();
		foreach($topicsData as $topicData){
			$topic = $this->initCourseTopicKnowledgeGoal($topicData, $userId);
			$topics[] = $topic;
		}
		
		return $topics;
	}
	
	protected function initIntervalKnowledge($dbData){
		$topic = new LazyLoadingIntervalKnowledge();
		$topic->setTopicId($dbData->id);
		$topic->setCourseId($dbData->courseid);
		$topic->setCreatorUserId($dbData->creator_userid);
		$topic->setName($dbData->name);
		$topic->setEstimation($dbData->estimation);
		$topic->setEstimationUserId($dbData->userid);
		$topic->setEstimationTimestamp($dbData->estimation_timestamp);
		$topic->setIntervalId($dbData->intervalid);
		$topic->setParentId($dbData->parentid);
		
		if ($dbData->public == 1){
			$topic->setIsPrivate(true);
		}
		else{
			$topic->setIsPrivate(false);
		}
		
		$topic->setCourseTopicFactory($this);
			
		return $topic;
		
	}
	
	protected function initPreCourseKnowledge($dbData){
		
		
		require_once realpath(__DIR__).'/../LazyLoadingPreCourseKnowledge.php';
		
		$topic = new LazyLoadingPreCourseKnowledge();
		$topic->setTopicId($dbData->id);
		$topic->setCourseId($dbData->courseid);
		$topic->setCreatorUserId($dbData->creator_userid);
		$topic->setName($dbData->name);
		$topic->setEstimation($dbData->estimation);
		$topic->setEstimationUserId($dbData->userid);
		$topic->setEstimationTimestamp($dbData->estimation_timestamp);
		$topic->setIntervalId(null);
		
		if ($dbData->public == 1){
			$topic->setIsPrivate(true);
		}
		else{
			$topic->setIsPrivate(false);
		}
		
		$topic->setCourseTopicFactory($this);
			
		return $topic;
		
	}
	
	protected function initCourseTopicKnowledgeGoal($dbData,$userId){
		$topic = new LazyLoadingKnowledgeGoal();
		$topic->setTopicId($dbData->id);
		$topic->setCreatorUserId($dbData->creator_userid);
		$topic->setCourseId($dbData->courseid);
		$topic->setName($dbData->name);
		$topic->setEstimation($dbData->estimation);
		$topic->setEstimationUserId($userId);
		$topic->setEstimationTimestamp($dbData->estimation_timestamp);

		if ($dbData->public == 1){
			$topic->setIsPrivate(true);
		}
		else{
			$topic->setIsPrivate(false);
		}

		$topic->setCourseTopicFactory($this);
			
		return $topic;
	}

	public function getBaseCourseTopcis($courseId) {
		return array();
	}

	public function getPublicCourseTopics($courseId) {
		return array();
	}

	public function getUserTopicKnowledgeGoal($topicId, $userId) {
		$sql = "SELECT t.id, t.name, t.parentid, t.public, t.creator_userid,"
				. "		t.create_timestamp, t.courseid, gt.estimation,"
				. "		gt.create_timestamp AS 'estimation_timestamp'"
				. "	FROM {course_topic} t"
				. "		LEFT JOIN {course_topic_knowledge_goal} gt "
				. "			ON (gt.topicid = t.id AND gt.userid = :userid1)"
				. "	WHERE t.delete_timestamp IS NULL"
				. "		AND t.id = :topicid"
				. "		AND (t.creator_userid IS NULL OR t.creator_userid = :userid2)";
		
		$topicData = $this->db->get_record_sql($sql,array(
			'topicid' => $topicId,'userid1' => $userId,'userid2' => $userId));
		
		if (!$topicData){
			return null;
		}
		
		return $this->initCourseTopicKnowledgeGoal($topicData, $userId);
	}

	public function getAllSubTopics($topicId) {
		return array();
	}

	public function getAllSubTopicsVisibleToParticipant($topicId, $userId) {
		
		$sql = "SELECT t.id, t.name, t.parentid, t.public, t.creator_userid,"
				. "		t.create_timestamp, t.courseid, gt.estimation,"
				. "		gt.create_timestamp AS 'estimation_timestamp'"
				. "	FROM {course_topic} t"
				. "		LEFT JOIN {course_topic_knowledge_goal} gt "
				. "			ON (gt.topicid = t.id AND gt.userid = :userid1)"
				. "	WHERE t.delete_timestamp IS NULL"
				. "		AND t.parentid = :parentid"
				. "		AND (t.creator_userid IS NULL OR t.creator_userid = :userid2)";
		
		$topicsData = $this->db->get_records_sql($sql,array(
			'parentid' => $topicId,'userid1' => $userId,'userid2' => $userId));
		
		if (count($topicsData) == 0){
			return array();
		}
		
		$topics = array();
		foreach($topicsData as $topicData){
			$topic = $this->initCourseTopicKnowledgeGoal($topicData,$userId);
			$topics[] = $topic;
		}
		
		return $topics;
	}

	public function getTopic($topicId) {
		$sql = "SELECT t.id, t.name, t.parentid, t.public, t.creator_userid,"
				. "		t.create_timestamp, t.courseid"
				. "	FROM {course_topic} t"
				. "	WHERE t.delete_timestamp IS NULL"
				. "		AND t.id = :topicid";
		$topicData = $this->db->get_record_sql($sql,array('topicid' => $topicId));
		
		if (!$topicData){
			return null;
		}
		
		$topic = new LazyLoadingCourseTopic();
		$topic->setCourseTopicFactory($this);
		$topic->setCreatorUserId($topicData->creator_userid);
		$topic->setName($topicData->name);
		$topic->setParentId($topicData->parentid);
		$topic->setTopicId($topicData->id);
		
		if ($topicData->public){
			$topic->setIsPrivate(false);
		}
		else{
			$topic->setIsPrivate(true);
		}
		
		return $topic;
	}
	
	public function getParticipantPreCourseKnowledge($userId, $courseId){
		
		$sql = "SELECT t.id, t.name, t.parentid, t.public, t.creator_userid, t.courseid,"
				. "		t.create_timestamp, tk.knowledge_estimation AS 'estimation', "
				. "		tk.estimation_timestamp, :userid AS 'userid', NULL AS 'intervalid'"
				. "	FROM {course_topic} t"
				. "		LEFT JOIN {pre_course_topic_knowledge} tk "
				. "			ON (t.id = tk.topicid AND tk.userid = :userid2)"
				. "	WHERE t.delete_timestamp IS NULL"
				. "		AND t.parentid IS NULL"
				. "		AND (t.creator_userid IS NULL OR t.creator_userid = :userid3)"
				. "		AND t.courseid = :courseid"
				. "		AND tk.knowledge_estimation IS NOT NULL";
		
		$topicsData = $this->db->get_records_sql($sql,array(
			'userid' => $userId, 
			'userid2' => $userId, 
			'userid3' => $userId, 
			'courseid' => $courseId
		));
		
		if (count($topicsData) == 0){
			return null;
		}
		
		$topics = array();
		foreach($topicsData as $topicData){
			$topic = $this->initPreCourseKnowledge($topicData);
			$topics[] = $topic;
		}
		
		return $topics;
	}
	
	public function getParticipantPreCourseKnowledgeSubTopics($topicId, $userId, $courseId) {
		
		$sql = "SELECT t.id, t.name, t.parentid, t.public, t.creator_userid, t.courseid,"
				. "		t.create_timestamp, tk.knowledge_estimation AS 'estimation', "
				. "		tk.estimation_timestamp, :userid AS 'userid', NULL AS 'intervalid'"
				. "	FROM {course_topic} t"
				. "		LEFT JOIN {pre_course_topic_knowledge} tk "
				. "			ON (t.id = tk.topicid AND tk.userid = :userid2)"
				. "	WHERE t.delete_timestamp IS NULL"
				. "		AND t.parentid = :parentid"
				. "		AND (t.creator_userid IS NULL OR t.creator_userid = :userid3)"
				. "		AND t.courseid = :courseid";
		
		$topicsData = $this->db->get_records_sql($sql,array(
			'userid' => $userId, 
			'userid2' => $userId, 
			'userid3' => $userId, 
			'courseid' => $courseId,
			'parentid' => $topicId
		));
		
		if (count($topicsData) == 0){
			return array();
		}
		
		$topics = array();
		foreach($topicsData as $topicData){
			$topic = $this->initPreCourseKnowledge($topicData, $topicData->userid);
			$topics[] = $topic;
		}
		
		return $topics;
	}

	public function getParticipantPreIntervalKnowledge($intervalId) {
		
		$sql = "SELECT t.id, t.name, t.parentid, t.public, t.creator_userid, t.courseid,"
				. "		t.create_timestamp, tk.knowledge_estimation AS 'estimation', "
				. "		tk.estimation_timestamp, i.userid, i.id AS 'intervalid'"
				. "	FROM {learning_interval} i"
				. "		LEFT JOIN {course_topic} t"
				. "			ON (i.courseid = t.courseid AND (t.creator_userid "
				. "				IS NULL OR t.creator_userid = i.userid))"
				. "		LEFT JOIN {topic_knowledge} tk "
				. "			ON (i.id = tk.intervalid AND t.id = tk.topicid)"
				. "	WHERE i.id = :intervalid"
				. "		AND t.delete_timestamp IS NULL"
				. "		AND t.parentid IS NULL";
		
		$topicsData = $this->db->get_records_sql($sql,array('intervalid' => $intervalId));
		
		if (count($topicsData) == 0){
			return array();
		}
		
		$topics = array();
		foreach($topicsData as $topicData){
			$topic = $this->initIntervalKnowledge($topicData);
			$topics[] = $topic;
		}
		
		return $topics;
	}

	public function getParticipantPreIntervalKnowledgeSubTopics($topicId, $intervalId) {
		
		$sql = "SELECT t.id, t.name, t.parentid, t.public, t.creator_userid, t.courseid,"
				. "		t.create_timestamp, tk.knowledge_estimation AS 'estimation', "
				. "		tk.estimation_timestamp, i.userid, i.id AS 'intervalid'"
				. "	FROM {learning_interval} i"
				. "		LEFT JOIN {course_topic} t"
				. "			ON (i.courseid = t.courseid AND (t.creator_userid "
				. "				IS NULL OR t.creator_userid = i.userid))"
				. "		LEFT JOIN {topic_knowledge} tk "
				. "			ON (i.id = tk.intervalid AND t.id = tk.topicid)"
				. "	WHERE i.id = :intervalid"
				. "		AND t.delete_timestamp IS NULL"
				. "		AND t.parentid = :parentid";
		
		$topicsData = $this->db->get_records_sql($sql,
				array('intervalid' => $intervalId, 'parentid' => $topicId));
		
		if (count($topicsData) == 0){
			return array();
		}
		
		$topics = array();
		foreach($topicsData as $topicData){
			$topic = $this->initIntervalKnowledge($topicData, $topicData->userid);
			$topics[] = $topic;
		}
		
		return $topics;
	}
	
	function getParticipantPreIntervalTopicKnowledge($topicId, $intervalId){
		$sql = "SELECT t.id, t.name, t.parentid, t.public, t.creator_userid, t.courseid,"
				. "		t.create_timestamp, tk.knowledge_estimation AS 'estimation', "
				. "		tk.estimation_timestamp, i.userid, i.id AS 'intervalid'"
				. "	FROM {learning_interval} i"
				. "		LEFT JOIN {course_topic} t"
				. "			ON (i.courseid = t.courseid AND (t.creator_userid "
				. "				IS NULL OR t.creator_userid = i.userid))"
				. "		LEFT JOIN {topic_knowledge} tk "
				. "			ON (i.id = tk.intervalid AND tk.topicid = t.id)"
				. "	WHERE i.id = :intervalid"
				. "		AND t.delete_timestamp IS NULL"
				. "		AND t.id = :topicid";
		
		$topicsData = $this->db->get_record_sql($sql,
				array('intervalid' => $intervalId, 'topicid' => $topicId));
		
		if (!$topicsData){
			return null;
		}
		
		return $this->initIntervalKnowledge($topicsData);
	}

}
