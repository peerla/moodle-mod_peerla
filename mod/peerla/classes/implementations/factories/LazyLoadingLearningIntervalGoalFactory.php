<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/factories/LearningIntervalGoalFactory.php';
require_once realpath(__DIR__).'/LazyLoadingLearningIntervalFactory.php';
require_once realpath(__DIR__).'/LazyLoadingCourseTopicFactory.php';
require_once realpath(__DIR__).'/LazyLoadingIntervalGoalActionFactory.php';
require_once realpath(__DIR__).'/../LazyLoadingLearningIntervalGoal.php';

/**
 * Description of LazyLoadingCourseIntervalGoalFactory
 *
 * @author Christoph Bohr
 */
class LazyLoadingLearningIntervalGoalFactory implements LearningIntervalGoalFactory{
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
	}
	
	public function getIntervalGoals($intervalId) {
		
		$sql = "SELECT id, status, intervalid, userid, courseid, topicid, actionid,"
				. "		create_timestamp, update_timestamp, comment, learning_days, "
				. "		planed_time_investment, invested_time, learning_days_planed"
				. "	FROM {interval_goal}"
				. "	WHERE intervalid = :intervalid";
		
		$goalsData = $this->db->get_records_sql($sql,array('intervalid' => $intervalId));
		
		if (count($goalsData) == 0){
			return array();
		}
		
		$goals = array();
		foreach($goalsData as $goalData){
			$goal = $this->initIntervalGoal($goalData);
			$goals[] = $goal;
		}
		
		return $goals;
	}
	
	protected function initIntervalGoal($dbData){
		$goal = new LazyLoadingLearningIntervalGoal();
		$goal->setGoalId($dbData->id);
		$goal->setCreateTimestamp($dbData->create_timestamp);
		$goal->setGoalComment($dbData->comment);
		$goal->setActionId($dbData->actionid);
		$goal->setIntervalId($dbData->intervalid);
		$goal->setPlanedTimeInvestment($dbData->planed_time_investment);
		$goal->setStatus($dbData->status);
		$goal->setTimeInvestment($dbData->invested_time);
		$goal->setTopicId($dbData->topicid);
		$goal->setUpdateTimestamp($dbData->update_timestamp);
		$goal->setUserId($dbData->userid);
		$goal->setCourseId($dbData->courseid);
		
		if ($dbData->learning_days_planed){
			$goal->setPlanedLearningDays(explode('|', $dbData->learning_days_planed));
		}
		else{
			$goal->setPlanedLearningDays(array());
		}
		
		if ($dbData->learning_days){
			$goal->setLearningDays(explode('|', $dbData->learning_days));
		}
		else{
			$goal->setLearningDays(array());
		}
		
		$intervalFactory = new LazyLoadingLearningIntervalFactory($this->db);
		$goal->setIntervalFactory($intervalFactory);
		
		$topicFactory = new LazyLoadingCourseTopicFactory($this->db);
		$goal->setTopicFactory($topicFactory);
		
		$actionFactory = new LazyLoadingIntervalGoalActionFactory($this->db);
		$goal->setIntervalGoalActionFactory($actionFactory);
		
		return $goal;
	}

}
