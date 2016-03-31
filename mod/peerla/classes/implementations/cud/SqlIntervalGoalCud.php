<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/cud/IntervalGoalCud.php';

/**
 * Description of SqlIntervalGoalCud
 *
 * @author Christoph Bohr
 */
class SqlIntervalGoalCud implements IntervalGoalCud{
	
	protected $db;
	protected $timestampUpdateIntervals = array();
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
	}
	
	protected function getAllLowestLevelTopicIds(CourseTopic $topic){
		$ids = array();
		$subTopics = $topic->getSubTopics();
		if (count($subTopics) == 0){
			return array($topic->getTopicId());
		}
		
		foreach ($subTopics as $subTopic){
			$subTopicIds = $this->getAllLowestLevelTopicIds($subTopic);
			array_merge($ids, $subTopicIds);
		}
		
		return $ids;
	}
	
	public function create(LearningIntervalGoal $object, $createRecurseivly=false) {
		
		if ($createRecurseivly){
			$topicIds = $this->getAllLowestLevelTopicIds($object->getTopic());
		}
		else{
			$topicIds = array($object->getTopicId());
		}
		
		$data = new \stdClass();
		$data->actionid = $object->getActionId();
		$data->courseid = $object->getCourseId();
		$data->topicid = $object->getTopicId();
		$data->userid = $object->getUserId();
		$data->intervalid = $object->getIntervalId();
		$data->comment = $object->getGoalComment();
		$data->planed_time_investment = ($object->getPlanedTimeInvestment() / count($topicIds));
		$data->status = $object->getStatus();
		$data->invested_time = $object->getTimeInvestment();
		$data->create_timestamp = time();
		$data->learning_days_planed = '';
		
		$planedDays = $object->getPlanedLearningDays();
		if (isset($planedDays) && is_array($planedDays)){
			foreach($planedDays as $day){
				$data->learning_days_planed .= ($data->learning_days_planed != '') ? '|':'';
				$data->learning_days_planed .= $day;
			}
		}
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			$goalIds = array();
			foreach($topicIds as $topicId){
				$data->topicid = $topicId;
				$returnValue = $this->db->insert_record('interval_goal', $data);
				$goalIds[] = $returnValue;
			}
			
			$transaction->allow_commit();
			$this->setIntervalGoalSettingTimestampsMustBeUpdated(
					$object->getIntervalId());
			return $returnValue;
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
	}

	public function delete(LearningIntervalGoal $object) {
		if (!$object->getGoalId()){
			return false;
		}
		
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			
			//delete data from active course planing
			$conditions = array(
				'id' => $object->getGoalId()
			);
			$this->db->delete_records('interval_goal', $conditions);
			
			$transaction->allow_commit();
			
			$this->setIntervalGoalSettingTimestampsMustBeUpdated(
					$object->getIntervalId());
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}

	public function update(LearningIntervalGoal $object) {
		if (!$object->getGoalId()){
			return false;
		}
		
		$days = $object->getLearningDays();
		$learningDaysValue = null;
		if (isset($days) && is_array($days) && count($days) > 0){
			$learningDaysValue = '';
			foreach($days as $day){
				$learningDaysValue .= ($learningDaysValue != '') ? '|':'';
				$learningDaysValue .= $day;
			}
		}
		
		$sql = "UPDATE {interval_goal} "
				. "	SET update_timestamp = :time,"
				. "		status = :status,"
				. "		invested_time = :invested_time,"
				. "		learning_days = :learning_days"
				. "	WHERE id = :goalid";
		$params = array(
			'time' => time(),
			'goalid' => $object->getGoalId(),
			'status' => $object->getStatus(),
			'invested_time' => $object->getTimeInvestment(),
			'learning_days' => $learningDaysValue
		);
			
		$transaction = $this->db->start_delegated_transaction();
		
		try{
			//update new data
			$this->db->execute($sql, $params);
			
			$transaction->allow_commit();
			
			$this->setIntervalGoalSettingTimestampsMustBeUpdated(
					$object->getIntervalId());
			
		} catch (Exception $ex) {
			$transaction->rollback($ex);
			return false;
		}
		
		return true;
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
	protected function setIntervalGoalSettingTimestampsMustBeUpdated($intervalId){
		$this->timestampUpdateIntervals[$intervalId] = $intervalId;
	}
	
	public function updateIntervalGoalSettingTimestamps(){
		
		if (count($this->timestampUpdateIntervals) == 0){
			return true;
		}
		
		$sql = "UPDATE {learning_interval}"
				. "	SET goal_update_timestamp = ?"
				. "	WHERE ";
		$sqlValues = array(time());
		$whereString = '';
		
		foreach($this->timestampUpdateIntervals as $intervalId){
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
		
		$this->discardAllGoalSettingIntervals();
		return true;
	}
	
	public function discardAllGoalSettingIntervals(){
		$this->timestampUpdateIntervals = array();
	}
	
	/**
	 * Copies all interval goals with the status "open" from one interval
	 * to another.
	 * 
	 * @param mod_kom_peerla\LearningInterval $sourceInterval Source interval
	 * @param int $destinationIntervalId Destination interval
	 */
	public function copyOpenIntervalGoals($sourceInterval,$destinationIntervalId){
		$goals = $sourceInterval->getIntervalGoals();
		$success = true;
		
		foreach ($goals as $goal){
			
			if ($goal->getStatus() == 'open'){
				$goal->setIntervalId($destinationIntervalId);
				$goalSuccess = $this->create($goal);

				if (!$goalSuccess){
					$success = false;
				}
			}
		}
		
		return $success;
	}


}
