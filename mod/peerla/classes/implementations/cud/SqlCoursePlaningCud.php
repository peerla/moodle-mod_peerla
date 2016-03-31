<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/cud/CoursePlaningCud.php';

/**
 * Description of SqlCoursePlaningCud
 *
 * @author Christoph Bohr
 */
class SqlCoursePlaningCud implements CoursePlaningCud{
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
		require_once realpath(__DIR__).'/SqlCourseGoalCud.php';
		require_once realpath(__DIR__).'/SqlTopicKnowledgeGoalCud.php';
	}
	
	public function create(CoursePlaning $object) {
		
		if (!$object->getUserId() || !$object->getCourseId()){
			return false;
		}
		
		$data = new \stdClass();
		$data->userid = $object->getUserId();
		$data->courseid = $object->getCourseId();
		$data->create_timestamp = time();
		$data->aspired_mark = $object->getAspiredMark();
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			$returnValue = $this->db->insert_record('course_plan', $data);
			
			$goalCud = new SqlCourseGoalCud($this->db);
			$goals = $object->getCourseGoals();
			foreach($goals as $goal){
				$goalCud->create($goal);
			}
			$goalCud->updatePlaningTimestamps();
			
			$topicPrioCud = new SqlTopicKnowledgeGoalCud($this->db);
			$topicsKnowledge = $object->getRootCourseTopicKnowledgeGoals();
			foreach($topicsKnowledge as $topicKnowledge){
				if ($topicKnowledge->getEstimationTimestamp()){
					$topicPrioCud->create($topicsKnowledge);
				}
			}
			$topicPrioCud->updatePlaningTimestamps();
			
			$transaction->allow_commit();
		
			return $returnValue;
			
		}catch(Exception $e){
			$transaction->rollback($e);
			return false;
		}
	}

	public function delete(CoursePlaning $object) {
		
		if (!$object->getUserId() || !$object->getCourseId()){
			return false;
		}
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			//archiv old data
			$this->archiveCoursePlaningBaseData($object);
			
			//delete data from active course planing
			$conditions = array(
				'userid' => $object->getUserId(),
				'courseid' => $object->getCourseId()
			);
			$this->db->delete_records('course_plan', $conditions);
			
			//delete goals
			$goalCud = new SqlCourseGoalCud($this->db);
			$goals = $object->getCourseGoals();
			foreach($goals as $goal){
				$goalCud->delete($goal);
			}
			$goalCud->updatePlaningTimestamps();
			
			//delete topic prio
			$topicPrioCud = new SqlTopicKnowledgeGoalCud($this->db);
			$prioTopcis = $object->getRootCourseTopicKnowledgeGoals();
			foreach($prioTopcis as $prioTopic){
				$topicPrioCud->delete($prioTopic);
			}
			$topicPrioCud->updatePlaningTimestamps();
			
			$transaction->allow_commit();
			
		} catch (Exception $e) {
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}

	public function update(CoursePlaning $object) {
		
		if (!$object->getUserId() || !$object->getCourseId()){
			return false;
		}
		
		$currentPlaning = $this->getCurrentCoursePlaning(
				$object->getCourseId(), $object->getUserId());
		
		if (is_null($currentPlaning)){
			return false;
		}
		
		$sql = "UPDATE {course_plan} "
				. "	SET aspired_mark = :aspired_mark"
				. "	WHERE userid = :userid AND courseid = :courseid";
		$params = array(
			'userid' => $object->getUserId(),
			'courseid' => $object->getCourseId(),
			'aspired_mark' => $object->getAspiredMark()
		);
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			if (!$this->planingBaseDataIsEqual($object,$currentPlaning)){
				//archive old data
				$this->archiveCoursePlaningBaseData($currentPlaning);
				//update new data
				$this->db->execute($sql, $params);
			}
			
			//update goals
			$goalCud = new SqlCourseGoalCud($this->db);
			$goalCud->updateAllUserCourseGoals($object->getUserId(),
					$object->getCourseId(), $object->getCourseGoals());
			$goalCud->updatePlaningTimestamps();
			/*
			//update topic prio
			$topicPrioCud = new SqlTopicKnowledgeGoalCud($this->db);
			$topicsKnowledge = $object->getRootCourseTopicKnowledgeGoals();
			foreach($topicsKnowledge as $topicKnowledge){
				if ($topicKnowledge->getEstimationTimestamp()){
					$topicPrioCud->create($topicKnowledge);
				}
			}
			$topicPrioCud->updatePlaningTimestamps();
			*/
			$transaction->allow_commit();
			
		} catch (Exception $e) {
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}
	
	/**
	 * Checks if the base data (not including topic prio and goals) has changed
	 * in comparision to the data base record.
	 * 
	 * @param \mod_kom_peerla\CoursePlaning $planing
	 * @return bool True, if the data has changed, false otherwise
	 * @throws Exception
	 */
	/*
	protected function baseDataHasChanged(CoursePlaning $newPlan){
		
		require_once realpath(__DIR__).'/../factories/LazyLoadingCoursePlaningFactory.php';
		
		$planingFactory = new LazyLoadingCoursePlaningFactory($this->db);
		$existingPlan = $planingFactory->getParticipantCoursePlaning(
				$newPlan->getCourseId(), $newPlan->getUserId());
		
		if (is_null($existingPlan)){
			throw new Exception('Updating a non existing Course Planing');
		}
		
		if ($newPlan->getAspiredMark() != $existingPlan->getAspiredMark()){
			return true;
		}
		
		return false;
	}
	*/
	/**
	 * 
	 * Compares to CoursePlaning objects and decides, if they represent the same data.
	 * 
	 * @param \mod_kom_peerla\CoursePlaning $planing1
	 * @param \mod_kom_peerla\CoursePlaning $planing2
	 * @return bool True, if the data is eaul, false otherwise
	 */
	protected function planingBaseDataIsEqual(CoursePlaning $planing1, CoursePlaning $planing2){
		
		if ($planing1->getUserId() != $planing2->getUserId()){
			return false;
		}
		
		if ($planing1->getCourseId() != $planing2->getCourseId()){
			return false;
		}
		
		if ($planing1->getAspiredMark() != $planing2->getAspiredMark()){
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
	 * @param \mod_kom_peerla\CoursePlaning $object
	 * @return boolean
	 */
	protected function archiveCoursePlaningBaseData(CoursePlaning $object){
		
		$data = new \stdClass();
		$data->userid = $object->getUserId();
		$data->courseid = $object->getCourseId();
		$data->create_timestamp = $object->getCreateTimestamp();
		$data->archive_timestamp = now();
		$data->aspired_mark = $object->getAspiredMark();
		
		try{
			$transaction = $this->db->start_delegated_transaction();
			
			//insert into archive
			$this->db->insert_record('course_plan_history', $data);
			
			$transaction->allow_commit();
			
		} catch (Exception $e) {
			$transaction->rollback($e);
			return false;
		}
		
		return true;
	}
	
	
	protected function getCurrentCoursePlaning($courseId, $userId){
		
		require_once realpath(__DIR__).'/../factories/LazyLoadingCoursePlaningFactory.php';
		
		$planingFactory = new LazyLoadingCoursePlaningFactory($this->db);
		$existingPlan = $planingFactory->getParticipantCoursePlaning(
				$courseId, $userId);
		
		
		return $existingPlan;
	}
}
