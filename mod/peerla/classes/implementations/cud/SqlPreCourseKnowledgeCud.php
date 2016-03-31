<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/cud/PreCourseKnowledgeCud.php';

/**
 * Description of SqlPreCourseKnowledgeCud
 *
 * @author Christoph Bohr
 */
class SqlPreCourseKnowledgeCud implements PreCourseKnowledgeCud{
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
	}
	
	public function create(IntervalCourseTopicKnowledge $object) {
		$data = new \stdClass();
		$data->topicid = $object->getTopicId();
		$data->userid = $object->getEstimationUserId();
		$data->courseid = $object->getCourseId();
		$data->knowledge_estimation = $object->getEstimation();
		$data->estimation_timestamp = time();
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			$returnValue = $this->db->insert_record('pre_course_topic_knowledge', $data);
			
			$transaction->allow_commit();
			
			$this->setPlaningTimestampMustBeUpdated($object->getCourseId(), 
					$object->getEstimationUserId());
			
			return $returnValue;
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
	}
	
	/**
	 * Save multiple knowledge estimations in one session.
	 * 
	 * @param mod_kom_peerla\IntervalCourseTopicKnowledge $newEstimations Array of knowledge estimation objects
	 * @return bool True if all were saved
	 */
	public function savePreCourseKnowledgeArray($newEstimations){
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			foreach($newEstimations as $estimation){
				$this->create($estimation);
			}
			
			$transaction->allow_commit();
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}
	
	/**
	 * Add an planing id for which the pre course knowledge setting timestamps
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
				. "	SET course_know_update_timestamp = ?"
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
