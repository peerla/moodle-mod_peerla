<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/LearningIntervalGoal.php';
require_once realpath(__DIR__).'/../interfaces/IntervalGoalAction.php';
require_once realpath(__DIR__).'/../interfaces/LearningInterval.php';
require_once realpath(__DIR__).'/../interfaces/CourseTopic.php';

/**
 * Description of LazyLoadingLearningIntervalGoal
 *
 * @author Christoph Bohr
 */
class BasicLearningIntervalGoal implements LearningIntervalGoal {
	
	protected $actionId;
	protected $userId;
	protected $courseId;
	protected $topicId;
	protected $intervalId;
	protected $goalId;
	protected $comment;
	protected $createTimestamp;
	protected $updateTimestamp;
	protected $status;
	protected $planedTimeInvestment;
	protected $actualTimeInvestment;
	protected $planedLearningDays;
	protected $actualLearningDays;
	
	protected $topic;
	protected $action;
	protected $interval;


	public function getActionId() {
		return $this->actionId;
	}

	public function getCreateTimestamp() {
		return $this->createTimestamp;
	}

	public function getGoalComment() {
		return $this->comment;
	}

	public function getGoalId() {
		return $this->goalId;
	}

	public function getGoalShortText() {
		$action = $this->getIntervalGoalAction();
		$topic = $this->getTopic();
		
		if (is_null($topic) || is_null($action)){
			return '';
		}
		
		$text = $topic->getName();
		$text .= ' ';
		$text .= $action->getActionName();
		
		return $text;
	}
	
	public function getGoalText() {
		$interval = $this->getInterval();
		$text = get_string('interval_goal_text_start_part1','peerla');
		$text .= userdate($interval->getEndDate(),get_string('strftimedateshort'));
		$text .= get_string('interval_goal_text_start_part2','peerla');
		$text .= $this->getGoalShortText();
		return $text;
	}

	public function getCourseId() {
		return $this->courseId;
	}

	public function getInterval() {
		return $this->interval;
	}

	public function getIntervalGoalAction() {
		return $this->action;
	}

	public function getIntervalId() {
		return $this->intervalId;
	}

	public function getPlanedTimeInvestment() {
		return $this->planedTimeInvestment;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getTimeInvestment() {
		return $this->actualTimeInvestment;
	}

	public function getTopic() {
		return $this->topic;
	}

	public function getTopicId() {
		return $this->topicId;
	}

	public function getUpdateTimestamp() {
		return $this->updateTimestamp;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function setStatus($newStatus) {
		$this->status = $newStatus;
	}

	public function setTimeInvestment($newTimeInvestment) {
		$this->actualTimeInvestment = $newTimeInvestment;
	}
	
	/**
	 * Set the database action id.
	 * 
	 * @param int $actionId
	 */
	public function setActionId($actionId) {
		$this->actionId = $actionId;
	}
	
	/**
	 * Set the create timestamp.
	 * 
	 * @param int $timestamp
	 */
	public function setCreateTimestamp($timestamp) {
		$this->createTimestamp = $timestamp;
	}

	/**
	 * Set the user comment.
	 * 
	 * @param int $comment
	 */
	public function setGoalComment($comment) {
		$this->comment = $comment;
	}

	/**
	 * Set the database goal id.
	 * 
	 * @param int $goalId
	 */
	public function setGoalId($goalId) {
		$this->goalId = $goalId;
	}

	/**
	 * Set the database course id.
	 * 
	 * @param int $courseId
	 */
	public function setCourseId($courseId) {
		$this->courseId = $courseId;
	}
	
	/**
	 * Set the learning interval object.
	 * 
	 * @param \mod_kom_peerla\LearningInterval $interval
	 */
	public function setInterval(LearningInterval $interval) {
		$this->interval = $interval;
		$this->setIntervalId($interval->getIntervalId());
	}

	/**
	 * Set the goal action object.
	 * 
	 * @param \mod_kom_peerla\IntervalGoalAction $action
	 */
	public function setIntervalGoalAction(IntervalGoalAction $action) {
		$this->action = $action;
		$this->setActionId($action->getActionId());
	}

	public function setIntervalId($intervalId) {
		$this->intervalId = $intervalId;
	}

	/**
	 * Set the planed time investment for the goal.
	 * 
	 * @param int $planedInvestment
	 */
	public function setPlanedTimeInvestment($planedInvestment) {
		$this->planedTimeInvestment = $planedInvestment;
	}
	
	/**
	 * Set the course topic object.
	 * 
	 * @param \mod_kom_peerla\CourseTopic $topic
	 */
	public function setTopic(CourseTopic $topic) {
		$this->topic = $topic;
		$this->setTopicId($topic->getTopicId());
	}

	/**
	 * Set the database topic id.
	 * 
	 * @param int $topicId
	 */
	public function setTopicId($topicId) {
		$this->topicId = $topicId;
	}

	/**
	 * Set the update timestamp
	 * 
	 * @param int $timestamp
	 */
	public function setUpdateTimestamp($timestamp) {
		$this->updateTimestamp = $timestamp;
	}

	/**
	 * Set the database user id.
	 * 
	 * @param int $userId
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}

	public function getPlanedLearningDays() {
		return $this->planedLearningDays;
	}
	
	public function setPlanedLearningDays(array $timestamps){
		$this->planedLearningDays = $timestamps;
	}

	public function getLearningDays() {
		return $this->actualLearningDays;
	}
	
	public function setLearningDays(array $timestamps){
		$this->actualLearningDays = $timestamps;
	}

}
