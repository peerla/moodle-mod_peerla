<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/BasicLearningInterval.php';

/**
 * Description of LazyLoadingLearningInterval
 *
 * @author Christoph Bohr
 */
class LazyLoadingLearningInterval extends BasicLearningInterval {
	
	protected $goalFactory;
	protected $intervalFactory;
	protected $topicFactory;
	
	protected $goalsLoaded = false;
	
	public function __construct() {
	}

	/**
	 * Set a LearningIntervalGoalFactory object for lazy loading of the goals.
	 * 
	 * @param \mod_kom_peerla\LearningIntervalGoalFactory $goalFactory
	 */
	public function setGoalFactory(LearningIntervalGoalFactory $goalFactory){
		$this->goalFactory = $goalFactory;
	}
	
	/**
	 * Set a LearningIntervalFactory for lazy loading of previous and following
	 * intervals.
	 * 
	 * @param \mod_kom_peerla\LearningIntervalFactory $factory
	 */
	public function setLearningIntervalFactory(LearningIntervalFactory $factory){
		$this->intervalFactory = $factory;
	}

	/**
	 * Set a LazyLoadingCourseTopicFactory for lazy loading of course knowledge.
	 * 
	 * @param \mod_kom_peerla\LazyLoadingCourseTopicFactory $factory
	 */
	public function setTopicFacotry(LazyLoadingCourseTopicFactory $factory){
		$this->topicFactory = $factory;
	}

	public function getIntervalGoals() {
		
		$goals = parent::getIntervalGoals();
		
		if (count($goals) == 0 && !$this->goalsLoaded 
				&& isset($this->goalFactory)){
			$this->setIntervalGoals(
					$this->goalFactory->getIntervalGoals($this->getIntervalId()));
			$this->goalsLoaded = true;
		}
		
		return parent::getIntervalGoals();
	}

	public function getPreIntervalKnowledge() {
		
		$knowledge = parent::getPreIntervalKnowledge();
		
		if (is_null($knowledge) && isset($this->topicFactory)){
			$this->setPreIntervalKnowledge($this->topicFactory
					->getParticipantPreIntervalKnowledge($this->getIntervalId()));
		}
		
		return parent::getPreIntervalKnowledge();
	}

	public function getFollowingInterval() {
		
		$following = parent::getFollowingInterval();
		
		if (is_null($following) && isset($this->intervalFactory)){
			$this->setFollowingInterval($this->intervalFactory
					->getFollowingLerningInterval($this->getIntervalId()));
		}
		
		return parent::getFollowingInterval();
	}

	public function getPreviousInterval() {
		
		$previous = parent::getPreviousInterval();
		
		if (is_null($previous) && isset($this->intervalFactory)){
			$this->setFollowingInterval($this->intervalFactory
					->getPreviousLerningInterval($this->getIntervalId()));
		}
		
		return parent::getPreviousInterval();
	}

}
