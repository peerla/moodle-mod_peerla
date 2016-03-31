<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/cud/IntervalTopicKnowledgeCud.php';

/**
 * Description of SqlIntervalTopicKnowledgeCud
 *
 * @author Christoph Bohr
 */
class SqlIntervalTopicKnowledgeCud implements IntervalTopicKnowledgeCud{
	
	protected $db;
	protected $knowledgeTimestampUpdateIntervals = array();
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
	}
	
	public function create(IntervalCourseTopicKnowledge $object) {
		$data = new \stdClass();
		$data->topicid = $object->getTopicId();
		$data->userid = $object->getEstimationUserId();
		$data->knowledge_estimation = $object->getEstimation();
		$data->estimation_timestamp = time();
		$data->intervalid = $object->getIntervalId();
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			$returnValue = $this->db->insert_record('topic_knowledge', $data);
			
			$transaction->allow_commit();
			$this->setIntervalKnowledgeEstimationTimestampsMustBeUpdated(
					$object->getIntervalId());
			return $returnValue;
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
	}

	public function delete(IntervalCourseTopicKnowledge $object) {
		
		if (!$object->getTopicId() || !$object->getEstimationUserId()
				|| !$object->getIntervalId()){
			return false;
		}
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			//archiv old data
			$this->archiveIntervalTopicKnowledgeData($object);
			
			//delete data from active course planing
			$conditions = array(
				'userid' => $object->getEstimationUserId(),
				'topicid' => $object->getTopicId(),
				'intervalid' => $object->getIntervalId()
			);
			$this->db->delete_records('topic_knowledge', $conditions);
			
			$transaction->allow_commit();
			
			$this->setIntervalKnowledgeEstimationTimestampsMustBeUpdated(
					$object->getIntervalId());
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}

	public function update(IntervalCourseTopicKnowledge $object) {
		
		if (!$object->getTopicId() || !$object->getEstimationUserId()
				|| !$object->getIntervalId()){
			return false;
		}
		
		$existingEstimation = $this->getCurrentIntervalTopicKnowledge(
				$object->getTopicId(), $object->getIntervalId());
		
		//check the estimation timestamp to see if the goal has been estimated
		if (is_null($existingEstimation) || !$existingEstimation->getEstimationTimestamp()){
			return false;
		}
		
		if ($this->intervalTopicKnowledgeBaseDataIsEqual(
				$object, $existingEstimation)){
			return true;
		}
		
		$sql = "UPDATE {topic_knowledge} "
				. "	SET knowledge_estimation = :estimation"
				. "	WHERE userid = :userid AND topicid = :topicid"
				. "		AND intervalId = :intervalid";
		$params = array(
			'userid' => $object->getEstimationUserId(),
			'topicid' => $object->getTopicId(),
			'estimation' => $object->getEstimation(),
			'intervalid' => $object->getIntervalId()
		);
			
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			//archive old data
			$this->archiveIntervalTopicKnowledgeData($existingEstimation);
			//update new data
			$this->db->execute($sql, $params);
			
			$transaction->allow_commit();
			
			$this->setIntervalKnowledgeEstimationTimestampsMustBeUpdated(
					$object->getIntervalId());
			
		} catch (Exception $ex) {
			$transaction->rollback($ex);
			return false;
		}
		
		return true;
	}

	public function updateTopicsKnowledge(array $newEstimations) {
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			foreach($newEstimations as $estimation){
				$this->updateTopicKnowledge($estimation);
			}
			$transaction->allow_commit();
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}
	
	protected function updateTopicKnowledge(IntervalCourseTopicKnowledge $newKnowledge){
		$existingKnowledge = $this->getCurrentIntervalTopicKnowledge(
				$newKnowledge->getTopicId(), $newKnowledge->getIntervalId());
		
		$transaction = $this->db->start_delegated_transaction();
		try{

			if (is_null($existingKnowledge) 
					|| !$existingKnowledge->getEstimationTimestamp()){
				$this->create($newKnowledge);
			}
			elseif (!$this->intervalTopicKnowledgeBaseDataIsEqual(
					$newKnowledge, $existingKnowledge)){
				$this->update($newKnowledge);
			}

			foreach($newKnowledge->getSubTopics() as $subTopic){
				$this->updateTopicsKnowledge($subTopic);
			}

			$transaction->allow_commit();
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}

	protected function archiveIntervalTopicKnowledgeData(IntervalCourseTopicKnowledge $object){
		$data = new \stdClass();
		$data->userid = $object->getEstimationUserId();
		$data->topicid = $object->getTopicId();
		$data->estimation_timestamp = $object->getEstimationTimestamp();
		$data->archive_timestamp = time();
		$data->knowledge_estimation = $object->getEstimation();
		$data->intervalid = $object->getIntervalId();
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			//insert into archive
			$this->db->insert_record('topic_knowledge_history', $data);
			$transaction->allow_commit();
			
		} catch (Exception $e) {
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}
	
	protected function intervalTopicKnowledgeBaseDataIsEqual(
			IntervalCourseTopicKnowledge $knowledge1, IntervalCourseTopicKnowledge $knowledge2){
		
		if ($knowledge1->getTopicId() != $knowledge2->getTopicId()){
			return false;
		}
		if ($knowledge1->getEstimationUserId() != $knowledge2->getEstimationUserId()){
			return false;
		}
		if ($knowledge1->getIntervalId() != $knowledge2->getIntervalId()){
			return false;
		}
		if ($knowledge1->getEstimation() != $knowledge2->getEstimation()){
			return false;
		}
		
		return true;
	}
	
	protected function getCurrentIntervalTopicKnowledge($topicId, $intervalId){
		require_once realpath(__DIR__).'/../factories/LazyLoadingCourseTopicFactory.php';
		
		$topicFactory = new LazyLoadingCourseTopicFactory($this->db);
		$existingKnowledge = $topicFactory
				->getParticipantPreIntervalTopicKnowledge($topicId, $intervalId);
		
		return $existingKnowledge;
	}
	
	/**
	 * Add an interval database id for which the estimation update timestamp
	 * should be updated.
	 * 
	 * Updates will only be executed, if the corresponding update 
	 * function is called. Adding the same id multiple times will result in only 
	 * one update operation.
	 * 
	 * @param int $intervalId
	 */
	protected function setIntervalKnowledgeEstimationTimestampsMustBeUpdated($intervalId){
		$this->knowledgeTimestampUpdateIntervals[$intervalId] = $intervalId;
	}
	
	/**
	 * Updates the knowledge estimation timestamps of all intervals for which
	 * a knowledge estimation has been changed. 
	 * 
	 * @return boolean
	 */
	public function updateIntervalKnowledgeEstimationTimestamps(){
		
		if (count($this->knowledgeTimestampUpdateIntervals) == 0){
			return true;
		}
		
		$sql = "UPDATE {learning_interval}"
				. "	SET knowledge_timestamp = ?"
				. "	WHERE ";
		$sqlValues = array(time());
		$whereString = '';
		
		foreach($this->knowledgeTimestampUpdateIntervals as $intervalId){
			$whereString .= ($whereString == '') ? '':' OR ';
			$whereString .= 'id = ?';
			$sqlValues[] = $intervalId;
		}
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			$this->db->execute($sql.$whereString, $sqlValues);
			$transaction->allow_commit();
		} catch (Exception $ex) {
			$transaction->rollback($ex);
			return false;
		}
		
		$this->discardAllKnowledgeEstimationTimestampIntervals();
		return true;
	}
	
	/**
	 * Discards all intervals for the estimation update timestamps.
	 * 
	 * This method can be called after update operations in which the interval
	 * timestamp for knowledge estimation updates should NOT be changed. All 
	 * preceding operations will not result in a timestamp update. All following
	 * operation will again mark the new intervals for update.
	 */
	public function discardAllKnowledgeEstimationTimestampIntervals(){
		$this->knowledgeTimestampUpdateIntervals = array();
	}
	
	/**
	 * Copy the the given user knowledge to the given interval.
	 * 
	 * @param mod_kom_peerla\IntervalCourseTopicKnowledge[] $topicsKnowledge Topic knowledge
	 * @param mod_kom_peerla\LearningInterval $intervalId Id of interval to which the knowledge is copied
	 * @return bool Success of the copy operation
	 */
	public function copyIntervalKnowledge($topicsKnowledge, $intervalId){
		
		$success = true;
		
		foreach ($topicsKnowledge as $topicKnowledge){
			$topicSuccess = $this->copyIntervalKnowledgeRecursivly($topicKnowledge, $intervalId);
			if (!$topicSuccess){
				$success = false;
			}
		}
		
		$this->setIntervalKnowledgeEstimationTimestampsMustBeUpdated($intervalId);
		
		return $success;
	}
	
	protected function copyIntervalKnowledgeRecursivly(
				IntervalCourseTopicKnowledge $topicKnowledge, $newIntervalId){
		
		$subTopics = $topicKnowledge->getSubTopics();
		
		$topicKnowledge->setIntervalId($newIntervalId);
		$success = $this->create($topicKnowledge);
		
		if (!$success){
			return false;
		}
		
		foreach($subTopics as $subTopicKnowledge){
			$subSuccess = $this->copyIntervalKnowledgeRecursivly($subTopicKnowledge, $newIntervalId);
			if (!$subSuccess){
				$success = false;
			}
		}
		
		return $success;
	}

}
