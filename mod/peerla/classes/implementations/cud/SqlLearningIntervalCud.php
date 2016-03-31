<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/cud/LearningIntervalCud.php';

/**
 * Description of SqlLearningIntervalCud
 *
 * @author Christoph Bohr
 */
class SqlLearningIntervalCud implements LearningIntervalCud{
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
	}

	public function create(LearningInterval $object) {
		
		$data = new \stdClass();
		$data->courseid = $object->getCourseId();
		$data->userid = $object->getUserId();
		$data->start_timestamp = $object->getStartDate();
		$data->end_timestamp = $object->getEndDate();
		$data->current_user_interval = 1;
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			//set current interval = 0 on all existing intervals of the user
			//for the course
			$sql = "UPDATE {learning_interval} "
					. "	SET current_user_interval = 0 "
					. "	WHERE userid = :userid AND courseid = :courseid";
			$params = array(
				'userid' => $object->getUserId(), 
				'courseid' => $object->getCourseId()
			);
			
			$this->db->execute($sql, $params);
			
			//insert the new interval as current interval
			$returnValue = $this->db->insert_record('learning_interval', $data);
			
			$transaction->allow_commit();
		
			return $returnValue;
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
	}
	
	public function update(LearningInterval $object) {
		
		if (!$object->getIntervalId()){
			return false;
		}
		
		$sql = "UPDATE {learning_interval} "
				. "	SET retrospective_bad = :retrospective_bad,"
				. "		retrospective_good = :retrospective_good"
				. "	WHERE id = :intervalid";
		$params = array(
			'intervalid' => $object->getIntervalId(),
			'retrospective_bad' => $object->getRetrospectiveBadText(),
			'retrospective_good' => $object->getRetrospectiveGoodText()
		);
			
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			//update new data
			$this->db->execute($sql, $params);
			
			$transaction->allow_commit();
			
		} catch (Exception $ex) {
			$transaction->rollback($ex);
			return false;
		}
		
		return true;
	}

}
