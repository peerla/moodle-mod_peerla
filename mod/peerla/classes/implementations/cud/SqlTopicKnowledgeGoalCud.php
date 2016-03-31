<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/cud/TopicKnowledgeGoalCud.php';

/**
 * Description of TopicKnowledgeGoalCud
 *
 * @author Christoph Bohr
 */
class SqlTopicKnowledgeGoalCud implements TopicKnowledgeGoalCud{
	
	protected $db;
	protected $timestampUpdatePlanings = array();
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
	}

	public function create(CourseTopicKnowledge $object) {
		
		$data = new \stdClass();
		$data->topicid = $object->getTopicId();
		$data->userid = $object->getEstimationUserId();
		$data->estimation = $object->getEstimation();
		$data->create_timestamp = time();
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			$returnValue = $this->db->insert_record('course_topic_knowledge_goal', $data);
			
			$transaction->allow_commit();
			
			$this->setPlaningTimestampMustBeUpdated($object->getCourseId(), 
					$object->getEstimationUserId());
			return $returnValue;
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
		
	}

	public function delete(CourseTopicKnowledge $object) {
		
		if (!$object->getTopicId() || !$object->getEstimationUserId()){
			return false;
		}
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			//archiv old data
			$this->archiveTopicKnowledgeGoalData($object);
			
			//delete data from active course planing
			$conditions = array(
				'userid' => $object->getEstimationUserId(),
				'topicid' => $object->getTopicId()
			);
			$this->db->delete_records('course_topic_knowledge_goal', $conditions);
			
			$transaction->allow_commit();
			
			$this->setPlaningTimestampMustBeUpdated($object->getCourseId(), 
					$object->getEstimationUserId());
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}

	public function update(CourseTopicKnowledge $object) {
		
		if (!$object->getTopicId() || !$object->getEstimationUserId()){
			return false;
		}
		
		$existingPrio = $this->getCurrentTopicKnowledgeGoal(
				$object->getTopicId(), $object->getEstimationUserId());
		if (is_null($existingPrio)){
			return false;
		}
		
		if ($this->topicKnowledgeGoalBaseDataIsEqual($object, $existingPrio)){
			return true;
		}
		
		$sql = "UPDATE {course_topic_knowledge_goal} "
				. "	SET estimation = :estimation"
				. "	WHERE userid = :userid AND topicid = :topicid";
		$params = array(
			'userid' => $object->getEstimationUserId(),
			'topicid' => $object->getTopicId(),
			'estimation' => $object->getEstimation()
		);
			
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			//archive old data
			$this->archiveTopicKnowledgeGoalData($existingPrio);
			//update new data
			$this->db->execute($sql, $params);
			
			$transaction->allow_commit();
			
			$this->setPlaningTimestampMustBeUpdated($object->getCourseId(), 
					$object->getEstimationUserId());
			
		} catch (Exception $ex) {
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}

	public function updateTopicKnowledgeGoals(array $newEstimations) {
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			foreach($newEstimations as $estimation){
				$this->updateKnowledgeGoal($estimation);
			}
			$transaction->allow_commit();
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
		
		return true;
		
	}
	
	protected function updateKnowledgeGoal(CourseTopicKnowledge $newEstimationRootTopic){
		
		$existingEstimationRootElement = $this->getCurrentTopicKnowledgeGoal(
				$newEstimationRootTopic->getTopicId(), $newEstimationRootTopic->getEstimationUserId());
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			//check the estimation timestamp to see if the goal has been estimated
			if (is_null($existingEstimationRootElement) || 
					!$existingEstimationRootElement->getEstimationTimestamp()){
				$this->create($newEstimationRootTopic);
			}
			elseif (!$this->topicKnowledgeGoalBaseDataIsEqual($newEstimationRootTopic, $existingEstimationRootElement)){
				$this->update($newEstimationRootTopic);
			}

			foreach($newEstimationRootTopic->getSubTopics() as $subTopic){
				$this->updateKnowledgeGoal($subTopic);
			}

			$transaction->allow_commit();
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}
	
	protected function archiveTopicKnowledgeGoalData(CourseTopicKnowledge $object){
		
		$data = new \stdClass();
		$data->userid = $object->getEstimationUserId();
		$data->topicid = $object->getTopicId();
		$data->create_timestamp = $object->getEstimationTimestamp();
		$data->archive_timestamp = time();
		$data->estimation = $object->getEstimation();
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			//insert into archive
			$this->db->insert_record('topic_knowledge_goal_history', $data);
			$transaction->allow_commit();
			
		} catch (Exception $e) {
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}
	
	protected function getCurrentTopicKnowledgeGoal($topicId, $userId){
		require_once realpath(__DIR__).'/../factories/LazyLoadingCourseTopicFactory.php';
		
		$topicFactory = new LazyLoadingCourseTopicFactory($this->db);
		$existingPrio = $topicFactory->getUserTopicKnowledgeGoal(
				$topicId,
				$userId
		);
		
		return $existingPrio;
	}
	
	/**
	 * Compares the data (without sub topics) of two EstimatedCourseTopic
	 * objects.
	 * 
	 * @param \mod_kom_peerla\CourseTopicKnowledge $topic1
	 * @param \mod_kom_peerla\CourseTopicKnowledge $topic2
	 * @return boolean
	 */
	protected function topicKnowledgeGoalBaseDataIsEqual(
			CourseTopicKnowledge $topic1, CourseTopicKnowledge $topic2){
		
		if ($topic1->getEstimationUserId() != $topic2->getEstimationUserId()){
			return false;
		}
		
		if ($topic1->getTopicId() != $topic2->getTopicId()){
			return false;
		}
		
		if ($topic1->getEstimation() != $topic2->getEstimation()){
			return false;
		}
		
		return true;
	}
	
	/**
	 * Add an planing database id for which the knowledge goal setting timestamps
	 * should be updated.
	 * 
	 * Updates will only be executed, if the corresponding update 
	 * function is called. Adding the same id multiple times will result in only 
	 * one update operation.
	 * 
	 * @param int $courseId Course id
	 * @param int $userId User id
	 */
	protected function setPlaningTimestampMustBeUpdated($courseId, $userId){
		$this->timestampUpdatePlanings[$courseId][$userId] = array(
			'userId' => $userId,
			'courseId' => $courseId,
		);
	}
	
	/**
	 * Updates the knowledge goal setting timestamps of all planings for which
	 * a course goal has been changed. 
	 * 
	 * @return boolean
	 */
	public function updatePlaningTimestamps(){
		
		if (count($this->timestampUpdatePlanings) == 0){
			return true;
		}
		
		$sql = "UPDATE {course_plan}"
				. "	SET topic_goal_update_timestamp = ?"
				. "	WHERE ";
		$sqlValues = array(time());
		$whereString = '';
		
		foreach($this->timestampUpdatePlanings as $courseid => $users){
			foreach($users as $userId => $data){
				$whereString .= ($whereString == '') ? '':' OR ';
				$whereString .= '(courseid = ? AND userid = ?)';
				$sqlValues[] = $courseid;
				$sqlValues[] = $userId;
			}
		}
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			$this->db->execute($sql.$whereString, $sqlValues);
			$transaction->allow_commit();
		} catch (Exception $ex) {
			$transaction->rollback($ex);
			return false;
		}
		
		$this->discardAllPlaningsTimestampChanges();
		return true;
	}
	
	/**
	 * Discards all course planings for the knowledge goal update timestamps.
	 * 
	 * This method can be called after update operations in which the planing
	 * timestamp for knowledge goal setting should NOT be changed. All 
	 * preceding operations will not result in a timestamp update. All following
	 * operation will again mark the new planings for update.
	 */
	public function discardAllPlaningsTimestampChanges(){
		$this->timestampUpdatePlanings = array();
	}

}
