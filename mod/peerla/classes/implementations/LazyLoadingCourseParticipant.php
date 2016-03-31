<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/CourseParticipant.php';

/**
 * Description of DbCourseParticipant
 *
 * @author Christoph Bohr
 */
class LazyLoadingCourseParticipant implements CourseParticipant {
	
	protected $courseId;
	protected $userId;
	
	protected $topicFactory;
	protected $intervalFactory;
	protected $planingFactory;
	protected $userFactory;
	protected $courseFactory;
	
	protected $topics;
	protected $intervals;
	protected $currentInterval;
	protected $user;
	protected $course;
	protected $planing;
	
	public function __construct($courseId, $userId) {
		$this->userId = $userId;
		$this->courseId = $courseId;
	}
	
	/**
	 * Set a CourseTopicFactory object for lazy loading of topics
	 * 
	 * @param \mod_kom_peerla\CourseTopicFactory $topicFactory Topic Factory
	 */
	public function setCourseTopicFactory(CourseTopicFactory $topicFactory){
		$this->topicFactory = $topicFactory;
	}
	
	/**
	 * Set a LearningIntervalFactory object for lazy loading of intervals
	 * 
	 * @param \mod_kom_peerla\LearningIntervalFactory $intervalFactory Interval Factory
	 */
	public function setLearningIntervalFactory(LearningIntervalFactory $intervalFactory){
		$this->intervalFactory = $intervalFactory;
	}
	
	/**
	 * Set a CourseGoalFactory object for lazy loading of the user object
	 * 
	 * @param \mod_kom_peerla\UserFactory $userFactory User Factory
	 */
	public function setUserFactory(UserFactory $userFactory){
		$this->userFactory = $userFactory;
	}
	
	/**
	 * Set a CourseFactory object for lazy loading of the course object
	 * 
	 * @param \mod_kom_peerla\CourseFactory $courseFactory Course Factory
	 */
	public function setCourseFactory(CourseFactory $courseFactory){
		$this->courseFactory = $courseFactory;
	}
	
	/**
	 * Set a CoursePlaningFactory object for lazy loading of the CoursePlaning object
	 * 
	 * @param \mod_kom_peerla\CoursePlaningFactory $planingFactory Planing Factory
	 */
	public function setCoursePlaningFactory(CoursePlaningFactory $planingFactory){
		$this->planingFactory = $planingFactory;
	}

	public function getCourseId() {
		return $this->courseId;
	}

	public function getCurrentLearningInterval() {
		
		if (!isset($this->currentInterval)){
			$this->currentInterval = $this->intervalFactory->getParticipantCurrentLearningInterval(
							$this->courseId, 
							$this->userId);
		}
		
		return $this->currentInterval;
	}

	public function getLearningIntervals() {
		
		if (!isset($this->intervals)){
			$this->intervals = $this->intervalFactory
					->getParticipantLearningIntervals(
							$this->courseId, 
							$this->userId);
		}
		
		return $this->intervals;
	}

	public function getRootCourseTopics() {
		if (!isset($this->topics)){
			$this->topics = $this->topicFactory
					->getCourseTopicsVisibleToParticipant(
							$this->courseId, 
							$this->userId);
		}
		
		return $this->topics;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function getUser() {
		if (!isset($this->user)){
			$this->user = $this->userFactory->getUser($this->userId);
		}
		
		return $this->user;
	}

	public function getCourse() {
		if (!isset($this->course)){
			$this->course = $this->courseFactory->getCourse($this->courseId);
		}
		
		return $this->course;
	}

	public function getCoursePlaning() {
		if (!isset($this->planing)){
			$this->planing = $this->planingFactory->getParticipantCoursePlaning(
					$this->courseId, $this->userId);
			
		}
		return $this->planing;
	}

	public function getCurrentCourseKnowledge() {
		$currentInterval = $this->getCurrentLearningInterval();
		
		if (!is_null($currentInterval)){
			return $currentInterval->getPreIntervalKnowledge();
		}
		
		$topics = $this->topicFactory->getParticipantPreCourseKnowledge(
				$this->getUserId(), $this->getCourseId());
		if (!is_null($topics)){
			return $topics;
		}
		
		$topics = $this->topicFactory->getCourseTopicsVisibleToParticipant(
				$this->getCourseId(), $this->getUserId());
		
		$knowledgeEstimations = $this->initEmptyIntervalKnowledge($topics);
		
		return $knowledgeEstimations;
	}
	
	/**
	 * Create an interval knowledge object with an estimation of 0 for each
	 * given topic.
	 * 
	 * @param CourseTopic[] $topics User course topics
	 * @return LazyLoadingIntervalKnowledge[] Knowledge estimation array
	 */
	protected function initEmptyIntervalKnowledge($topics){
		$knowledgeEstimations = array();
		
		foreach($topics as $topic){
			$knowledge = new LazyLoadingIntervalKnowledge();
			$knowledge->setCourseTopicFactory($this->topicFactory);
			$knowledge->setCreatorUserId($topic->getCreatorUserId());
			$knowledge->setEstimation(0);
			$knowledge->setEstimationUserId($this->getUserId());
			$knowledge->setIsPrivate($topic->isPrivate());
			$knowledge->setName($topic->getName());
			$knowledge->setParentId($topic->getParentId());
			$knowledge->setTopicId($topic->getTopicId());
			$knowledgeEstimations[] = $knowledge;
			
			$subKnowledge = $this->initEmptyIntervalKnowledge($topic->getSubTopics());
			$knowledge->setSubTopics($subKnowledge);
		}
		
		return $knowledgeEstimations;
	}

}
