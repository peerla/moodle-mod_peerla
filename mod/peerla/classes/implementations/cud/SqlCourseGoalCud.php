<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/cud/CourseGoalCud.php';

/**
 * Description of SqlCourseGoalCud
 *
 * @author Christoph Bohr
 */
class SqlCourseGoalCud implements CourseGoalCud{
	
	protected $db;
	protected $timestampUpdatePlanings = array();
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
	}

	public function create(CourseGoal $object) {
		
		$data = new \stdClass();
		$data->userid = $object->getUserId();
		$data->courseid = $object->getCourseId();
		$data->create_timestamp = time();
		$data->goal_text = $object->getGoalText();
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			$returnValue = $this->db->insert_record('course_goal', $data);
			$transaction->allow_commit();
			
			$this->setPlaningTimestampMustBeUpdated($object->getCourseId(), 
					$object->getUserId());
		
			return $returnValue;
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
	}

	public function delete(CourseGoal $object) {
		if (!$object->getGoalId()){
			return false;
		}
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			//archiv old data
			$this->archiveCourseGoalBaseData($object);
			
			//delete data from active course planing
			$conditions = array(
				'id' => $object->getGoalId()
			);
			$this->db->delete_records('course_goal', $conditions);
			
			$transaction->allow_commit();
			
			$this->setPlaningTimestampMustBeUpdated($object->getCourseId(), 
					$object->getUserId());
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}

	public function update(CourseGaol $object) {
		
		if (!$object->getGoalId()){
			return false;
		}
		
		$currentGoal = $this->getCurrentCourseGoal($object->getGoalId());
		
		if (is_null($currentGoal)){
			return false;
		}
		
		if ($this->gaolDataIsEqual($object, $currentGoal)){
			return true;
		}
		
		$data = new \stdClass();
		$data->id = $object->getGoalId();
		$data->userid = $object->getUserId();
		$data->courseid = $object->getCourseId();
		$data->create_timestamp = time();
		$data->goal_text = $object->getGoalText();
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			$this->archiveCourseGoalBaseData($currentGoal);
			$this->db->update_record('course_goal', $data);
			
			$transaction->allow_commit();
			
			$this->setPlaningTimestampMustBeUpdated($object->getCourseId(), 
					$object->getUserId());
		} catch (Exception $ex) {
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}
	
	public function updateAllUserCourseGoals($userId, $courseId, array $newGoals){
		
		if (!$userId || !$courseId){
			return false;
		}
		
		require_once realpath(__DIR__).'/../factories/LazyLoadingCourseGoalFactory.php';
		$goalFactory = new LazyLoadingCourseGoalFactory($this->db);
		$existingGoals = $goalFactory->getParticipantCourseGoals($courseId, $userId);
		
		$updateGoals = array();
		$deleteGoals = $existingGoals;
		$insertGoals = $newGoals;
		
		//iterate throug all new and old goals and compare their data
		foreach($newGoals as $newGoalKey => $newGoal){
			foreach($existingGoals as $existingGoalKey => $existingGoal){
				
				//two goals have the same id -> check if the data changed
				if ($newGoal->getGoalId() == $existingGoal->getGoalId()){
					
					if (!$this->gaolDataIsEqual($newGoal, $existingGoal)){
						$updateGoals[] = $newGoal;
					}
					
					unset($deleteGoals[$existingGoalKey]);
					unset($insertGoals[$newGoalKey]);
				}
				//same data but different ids -> keep the old one and skip the new one
				elseif($this->gaolDataIsEqual($newGoal, $existingGoal)){
					unset($deleteGoals[$existingGoalKey]);
					unset($insertGoals[$newGoalKey]);
				}
				
				
			}
		}
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			foreach($insertGoals as $insertGoal){
				$this->create($insertGoal);
			}
			
			foreach($updateGoals as $updateGoal){
				$this->update($updateGoal);
			}
		
			foreach($deleteGoals as $deleteGoal){
				$this->delete($deleteGoal);
			}
			
			$transaction->allow_commit();
		} catch (Exception $ex) {
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}
	
	protected function getCurrentCourseGoal($goalId){
		require_once realpath(__DIR__).'/../factories/LazyLoadingCourseGoalFactory.php';
		
		$goalFactory = new LazyLoadingCourseGoalFactory($this->db);
		$existingGoal = $goalFactory->getCourseGoals($goalId);
		
		return $existingGoal;
	}
	
	/**
	 * Compares to CourseGoal objects and decides, if they represent the same data.
	 * 
	 * @param \mod_kom_peerla\CourseGoal $goal1
	 * @param \mod_kom_peerla\CourseGoal $goal2
	 * @return bool True, if the data is eaul, false otherwise
	 */
	protected function gaolDataIsEqual(CourseGoal $goal1, CourseGoal $goal2){
		if ($goal1->getCourseId() != $goal2->getCourseId()){
			return false;
		}
		
		if ($goal1->getUserId() != $goal2->getUserId()){
			return false;
		}
		
		if ($goal1->getGoalText() != $goal2->getGoalText()){
			return false;
		}
		
		return true;
	}
	
	/**
	 * Archive the given object.
	 * 
	 * The data of this object will be saved to a seperate archive table. The 
	 * original data record will not be changed. Deletion or update of the existing,
	 * non-archive data record must be done manually after calling this method.
	 * 
	 * @param \mod_kom_peerla\CourseGoal $object
	 * @return boolean
	 */
	protected function archiveCourseGoalBaseData(CourseGoal $object){
		
		$data = new \stdClass();
		$data->userid = $object->getUserId();
		$data->courseid = $object->getCourseId();
		$data->create_timestamp = $object->getCreateTimestamp();
		$data->goal_text = $object->getGoalText();
		$data->archive_timestamp = time();
		$data->goalid = $object->getGoalId();
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			//insert into archive
			$this->db->insert_record('course_goal_history', $data);
			
			$transaction->allow_commit();
			
		} catch (Exception $e) {
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}
	
	/**
	 * Add an planing database id for which the goal setting timestamps
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
	 * Updates the course goal setting timestamps of all planings for which
	 * a course goal has been changed. 
	 * 
	 * @return boolean
	 */
	public function updatePlaningTimestamps(){
		
		if (count($this->timestampUpdatePlanings) == 0){
			return true;
		}
		
		$sql = "UPDATE {course_plan}"
				. "	SET course_goal_update_timestamp = ?"
				. "	WHERE ";
		$sqlValues = array(time());
		$whereString = '';
		
		foreach($this->timestampUpdatePlanings as $courseId => $users){
			foreach($users as $userId => $data){
				$whereString .= ($whereString == '') ? '':' OR ';
				$whereString .= '(courseid = ? AND userid = ?)';
				$sqlValues[] = $courseId;
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
	 * Discards all course planings for the goal update timestamps.
	 * 
	 * This method can be called after update operations in which the planing
	 * timestamp for course goal setting should NOT be changed. All 
	 * preceding operations will not result in a timestamp update. All following
	 * operation will again mark the new planings for update.
	 */
	public function discardAllPlaningsTimestampChanges(){
		$this->timestampUpdatePlanings = array();
	}
}
