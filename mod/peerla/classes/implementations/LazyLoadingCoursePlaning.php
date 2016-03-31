<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/CoursePlaning.php';

/**
 * Description of LazyLoadingCoursePlaning
 *
 * @author Christoph Bohr
 */
class LazyLoadingCoursePlaning implements CoursePlaning{
	
	protected $courseId;
	protected $userId;
	
	protected $topicFactory;
	protected $goalFactory;
	protected $userFactory;
	protected $courseFactory;
	
	protected $mark;
	protected $goals;
	protected $topicPrio;
	protected $isActive;
	protected $createTimestamp;
	protected $deactivationTimestamp;
	
	protected $hasFinishedCourseGoalSetting = false;
	protected $hasFinishedTopicGoalSetting = false;
	protected $hasFinishedPreCourseKnowledge = false;

	public function __construct($courseId, $userId) {
		$this->userId = $userId;
		$this->courseId = $courseId;
	}
	
	/**
	 * Set a CourseGoalFactory object for lazy loading of course goals
	 * 
	 * @param CourseGoalFactory $goalFactory Course goal factory
	 */
	public function setCourseGoalFactory(CourseGoalFactory $goalFactory){
		$this->goalFactory = $goalFactory;
	}
	
	/**
	 * Set a CourseTopicFactory object for lazy loading of course goals
	 * 
	 * @param CourseTopicFactory $goalFactory Course goal factory
	 */
	public function setCourseTopicFactory(CourseTopicFactory $topicFactory){
		$this->topicFactory = $topicFactory;
	}
	
	/**
	 * Set a CourseGoalFactory object for lazy loading of the user object
	 * 
	 * @param UserFactory $userFactory User Factory
	 */
	public function setUserFactory(UserFactory $userFactory){
		$this->userFactory = $userFactory;
	}
	
	/**
	 * Set a CourseFactory object for lazy loading of the course object
	 * 
	 * @param CourseFactory $courseFactory Course Factory
	 */
	public function setCourseFactory(CourseFactory $courseFactory){
		$this->courseFactory = $courseFactory;
	}

	public function getCourseId() {
		return $this->courseId;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function getUser() {
		if (!isset($this->user) && isset($this->userFactory)){
			$this->user = $this->userFactory->getUser($this->userId);
		}
		
		return $this->user;
	}

	public function getCourse() {
		if (!isset($this->course)){
			
			if (!isset($this->courseFactory)){
				return null;
			}
			
			$this->course = $this->courseFactory->getCourse($this->courseId);
		}
		
		return $this->course;
	}

	public function getAspiredMark() {
		return $this->mark;
	}
	
	public function setAspiredMark($mark){
		$this->mark = $mark;
	}

	
	public function getCourseGoals() {
		if (!isset($this->goals)){
			
			if (!isset($this->goalFactory)){
				return array();
			}
			
			$this->goals = $this->goalFactory->getParticipantCourseGoals(
					$this->courseId, $this->userId);
		}
		
		return $this->goals;
	}

	public function getRootCourseTopicKnowledgeGoals() {
		if (!isset($this->topicPrio)){
			
			if (!isset($this->topicFactory)){
				return array();
			}
			
			$this->topicPrio = $this->topicFactory
					->getCourseTopicsVisibleToParticipant(
							$this->courseId, 
							$this->userId);
		}
		
		return $this->topicPrio;
	}

	public function getCreateTimestamp() {
		return $this->createTimestamp;
	}

	public function getDeactivationTimestamp() {
		return $this->deactivationTimestamp;
	}
	
	public function isActive() {
		if (isset($this->deactivationTimestamp) && $this->deactivationTimestamp){
			return false;
		}
		return true;
	}
	
	public function setCreateTimestamp($timestamp) {
		$this->createTimestamp = $timestamp;
	}
	
	public function setDeactivationTimestamp($timestamp) {
		$this->deactivationTimestamp = $timestamp;
	}
	
	public function setCourseGoals(array $goals){
		$this->goals = $goals;
	}

	public function userHasFinishedCourseGoalSetting() {
		return $this->hasFinishedCourseGoalSetting;
	}
	
	/**
	 * Set if the user has finished the course goal setting step of the
	 * course planing.
	 * 
	 * @param bool $finished
	 */
	public function setUserHasFinishedCourseGoalSetting($finished=true){
		$this->hasFinishedCourseGoalSetting = $finished;
	}

	public function userHasFinishedGoalToTopicTransformation() {
		return $this->hasFinishedTopicGoalSetting;
	}
	
	/**
	 * Set if the user has finished the topic knowledge goal step of the
	 * course planing.
	 * 
	 * @param bool $finished
	 */
	public function setUserHasFinishedGoalToTopicTransformation($finished=true){
		$this->hasFinishedTopicGoalSetting = $finished;
	}

	public function userHasFinishedPlaning() {
		if ($this->userHasFinishedCourseGoalSetting() && 
				$this->userHasFinishedGoalToTopicTransformation()
				&& $this->userHasFinishedPreCourseKnowledgeSetting()){
			return true;
		}
		
		return false;
	}

	public function userHasFinishedPreCourseKnowledgeSetting() {
		return $this->hasFinishedPreCourseKnowledge;
	}
	
	/**
	 * Set if the user has finished the pre course topic knowledge step of the
	 * course planing.
	 * 
	 * @param bool $finished
	 */
	public function setUserHasFinishedPreCourseKnowledgeSetting($finished=true){
		$this->hasFinishedPreCourseKnowledge = $finished;
	}

}
