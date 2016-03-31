<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../../interfaces/factories/IntervalGoalActionFactory.php';
require_once realpath(__DIR__).'/../BasicIntervalGoalAction.php';

/**
 * Description of LazyLoadingIntervalGoalFactory
 *
 * @author Christoph Bohr
 */
class LazyLoadingIntervalGoalActionFactory implements IntervalGoalActionFactory{
	
	protected $db;
	
	public function __construct(\moodle_database $db) {
		$this->db = $db;
	}
	
	public function getAction($actionId) {
		$sql = "SELECT id, action"
				. "	FROM {interval_goal_action}"
				. "	WHERE id = :actionid";
		
		$actionData = $this->db->get_record_sql($sql,array('actionid' => $actionId));
		
		if (!$actionData){
			return null;
		}
		
		return $this->initBasicIntervalGoalAction($actionData);
	}
	
	public function getActionsVisibleToUser($userId){
		$sql = "SELECT id, action"
				. "	FROM {interval_goal_action}";
		
		$actionsData = $this->db->get_records_sql($sql);
		
		$actions = array();
		
		foreach($actionsData as $actionData){
			$actions[] = $this->initBasicIntervalGoalAction($actionData);
		}
		
		return $actions;
	}
	
	/**
	 * Init a BasicIntervalGoalAction object from a database data object.
	 * 
	 * @param type $dbData
	 * @return \mod_kom_peerla\BasicIntervalGoalAction
	 */
	protected function initBasicIntervalGoalAction($dbData){
		$action = new BasicIntervalGoalAction();
		$action->setActionId($dbData->id);
		$action->setActionName($dbData->action);
		return $action;
	}

}
