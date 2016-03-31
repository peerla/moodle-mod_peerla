<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/LearningInterval.php';
require_once realpath(__DIR__).'/../interfaces/LearningIntervalGoal.php';

/**
 * Description of BasicLearningInterval
 *
 * @author Christoph Bohr
 */
class BasicLearningInterval implements LearningInterval {
	
	protected $intervalId = null;
	protected $courseId = null;
	protected $userId = null;
	protected $endDate = null;
	protected $startDate = null;
	protected $retroGoodText;
	protected $retroBadText;
	protected $retrospectiveDone = null;
	
	protected $goals = array();
	protected $previousInterval = null;
	protected $followingInterval = null;
	protected $preKnowledge = null;
	
	protected $hasFinishedKnowledgeEstimation = false;
	protected $hasFinishedGoalSetting = false;
	
	/**
	 * 
	 * @param string|int $intervalId Db learning interval id
	 */
	public function __construct() {
		
	}

	/**
	 * Set the interval db id.
	 * 
	 * @param int $intervalId
	 */
	public function setIntervalId($intervalId){
		$this->intervalId = $intervalId;
	}

	/**
	 * Set the user id.
	 * 
	 * @param int $userId
	 */
	public function setUserId($userId){
		$this->userId = $userId;
	}
	
	/**
	 * Set the course id.
	 * 
	 * @param int $courseId
	 */
	public function setCourseId($courseId){
		$this->courseId = $courseId;
	}


	/**
	 * Set the start date timestamp 
	 * 
	 * @param int $startDate unix timestamp
	 */
	public function setStartDate($startDate){
		$this->startDate = $startDate;
	}
	
	/**
	 * Set the end date timestamp 
	 * 
	 * @param int $endDate unix timestamp
	 */
	public function setEndDate($endDate){
		$this->endDate = $endDate;
	}
	
	/**
	 * Set if the retrospective has been done
	 * 
	 * @param bool $done True if done, false otherwise
	 */
	public function setRetrospectiveDone($done=true){
		$this->retrospectiveDone = $done;
	}

	public function getEndDate() {
		return $this->endDate;
	}
	
	/**
	 * Set the interval goals.
	 * 
	 * @param \mod_kom_peerla\LearningIntervalGoal[] $goals
	 */
	public function setIntervalGoals(array $goals){
		$this->goals = $goals;
	}

	public function getIntervalGoals() {
		return $this->goals;
	}

	public function getStartDate() {
		return $this->startDate;
	}

	public function retrospectiveDone() {
		return $this->retrospectiveDone;
	}

	public function getCourseId() {
		return $this->courseId;
	}

	public function getUserId() {
		return $this->userId;
	}
	
	/**
	 * Set the topic knowledge at the start of the interval.
	 * 
	 * @param LearningInterval[] $preIntervalKnowledge
	 */
	public function setPreIntervalKnowledge(array $preIntervalKnowledge){
		$this->preKnowledge = $preIntervalKnowledge;
	}

	public function getPreIntervalKnowledge() {
		return $this->preKnowledge;
	}
	
	/**
	 * Set the following learning interval.
	 * 
	 * @param \mod_kom_peerla\LearningInterval $following
	 */
	public function setFollowingInterval(LearningInterval $following){
		$this->followingInterval = $following;
	}

	public function getFollowingInterval() {
		return $this->followingInterval;
	}
	
	/**
	 * Set the previous learning interval.
	 * 
	 * @param \mod_kom_peerla\LearningInterval $previous
	 */
	public function setPreviousInterval(LearningInterval $previous){
		$this->previousInterval = $previous;
	}

	public function getPreviousInterval() {
		return $this->previousInterval;
	}

	public function getGoalTopicKnowledge() {
		$goals = $this->getIntervalGoals();
		$topics = array();
		
		foreach($goals as $goal){
			$topic = $goal->getTopic();
			$alreadyExists = false;
			
			//is this topic allready included? -> ignore this topic
			if (isset($topics[$topic->getTopicId()])){
				$alreadyExists = true;
			}
			
			//is a parent goal of this goal allready included? 
			// -> ignore this topic
			$parentTopic = $topic->getParentTopic();
			while ($parentTopic != null && !$alreadyExists){
				if (isset($topics[$parentTopic->getTopicId()])){
					$alreadyExists = true;
				}
				$parentTopic = $parentTopic->getParentTopic();
			}
			
			//the topic itself and no parent topic were found -> add this topic
			if (!$alreadyExists){
				$topics[$topic->getTopicId()] = $topic;
				$this->filterChildTopicsFromArray($topic, $topics);
			}
			
		}
		
		return $topics;
	}
	
	protected function filterChildTopicsFromArray(CourseTopic $parentTopic, &$topicArray){
		$subTopics = $parentTopic->getSubTopics();
		
		foreach($subTopics as $subTopic){
			//if a subtopic of this topic is already included 
			//-> remove the subtopic
			if (isset($topicArray[$subTopic->getTopicId()])){
				unset($topicArray[$subTopic->getTopicId()]);
			}
			
			$this->filterChildTopicsFromArray($subTopic, $topicArray);
		}
	}

	public function getIntervalId() {
		return $this->intervalId;
	}

	/**
	 * Set if the user has finished the interval goal setting step of the
	 * interval planing.
	 * 
	 * @param bool $finished
	 */
	public function setUserHasFinishedGoalSetting($finished=true) {
		$this->hasFinishedGoalSetting = $finished;
	}

	public function userHasFinishedGoalSetting() {
		return $this->hasFinishedGoalSetting;
	}

	/**
	 * Set if the user has finished the interval knowledge estimation step of the
	 * interval planing.
	 * 
	 * @param bool $finished
	 */
	public function setUserHasFinishedKnowledgeEstimation($finished=true) {
		$this->hasFinishedKnowledgeEstimation = $finished;
	}

	public function userHasFinishedKnowledgeEstimation() {
		return $this->hasFinishedKnowledgeEstimation;
	}

	public function userHasFinishedPlaning() {
		if ($this->userHasFinishedGoalSetting() 
				&& $this->userHasFinishedKnowledgeEstimation()){
			return true;
		}
		
		return false;
	}

	public function isRunning() {
		if ($this->getEndDate() > time() && $this->getStartDate() < time()){
			return true;
		}
		return false;
	}

	public function getRetrospectiveBadText() {
		return $this->retroBadText;
	}

	public function getRetrospectiveGoodText() {
		return $this->retroGoodText;
	}

	public function setRetrospectiveBadText($text) {
		$this->retroBadText = $text;
	}

	public function setRetrospectiveGoodText($text) {
		$this->retroGoodText = $text;
	}

}
