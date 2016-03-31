<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/BasicLearningIntervalGoal.php';

/**
 * Description of LazyLoadingLearningIntervalGoal
 *
 * @author Christoph Bohr
 */
class LazyLoadingLearningIntervalGoal extends BasicLearningIntervalGoal{
	
	protected $actionFactory;
	protected $topicFactory;
	protected $intervalFactory;
	
	protected $actionLoaded = false;
	protected $topicLoaded = false;
	protected $intervalLoaded = false;
	
	/**
	 * Set the interval goal action factory for lazy loading of the action object.
	 * 
	 * @param \mod_kom_peerla\IntervalGoalActionFactory $factory
	 */
	function setIntervalGoalActionFactory(IntervalGoalActionFactory $factory){
		$this->actionFactory = $factory;
	}
	
	function getIntervalGoalAction() {
		$action = parent::getIntervalGoalAction();
		
		if (is_null($action) && !$this->actionLoaded && !is_null($this->actionFactory)){
			$this->setIntervalGoalAction($this->actionFactory->getAction(
					$this->getActionId()));
			$this->actionLoaded = true;
		}
		
		return parent::getIntervalGoalAction();
	}
	
	/**
	 * Set the course topic factory for lazy loading of the topic object.
	 * 
	 * @param \mod_kom_peerla\CourseTopicFactory $factory
	 */
	function setTopicFactory(CourseTopicFactory $factory){
		$this->topicFactory = $factory;
	}
	
	function getTopic() {
		$topic = parent::getTopic();
		
		if (is_null($topic) && !$this->topicLoaded && !is_null($this->topicFactory)
				&& !is_null($this->getTopicId())){
			$topic = $this->topicFactory->
						getParticipantPreIntervalTopicKnowledge(
							$this->getTopicId(), $this->getIntervalId());
			$this->topicLoaded = true;
			if (isset($topic)){
				$this->setTopic($topic);
			}
		}
		
		return parent::getTopic();
	}
	
	/**
	 * Set the learning interval factory for lazy loading of the interval object.
	 * 
	 * @param \mod_kom_peerla\LearningIntervalFactory $factory
	 */
	function setIntervalFactory(LearningIntervalFactory $factory){
		$this->intervalFactory = $factory;
	}
	
	function getInterval() {
		$interval = parent::getInterval();
		
		if (is_null($interval) && !$this->intervalLoaded && !is_null($this->intervalFactory)){
			$this->setInterval($this->intervalFactory->getLearningInterval(
					$this->getIntervalId()));
			$this->intervalLoaded = true;
		}
		
		return parent::getInterval();
	}
	
}
