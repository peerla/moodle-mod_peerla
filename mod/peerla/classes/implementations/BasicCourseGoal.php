<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/CourseGoal.php';

/**
 * Basic course goal implementation with setters and getters for all the 
 * geoal data.
 * 
 * No data base operations are executed in this object. All data has to be 
 * set manually before it is available.
 *
 * @author Christoph Bohr
 */
class BasicCourseGoal implements CourseGoal{
	
	protected $courseId;
	protected $userId;
	protected $text;
	protected $goalId;
	protected $createTimestamp;
	protected $deactivationTimestamp;
	
	public function getCourseId() {
		return $this->courseId;
	}

	public function getGoalId() {
		return $this->goalId;
	}

	public function getGoalText() {
		return $this->text;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function isActive() {
		if (isset($this->deactivationTimestamp) && $this->deactivationTimestamp){
			return false;
		}
		return true;
	}
	
	/**
	 * Set the course id
	 * 
	 * @param int $courseId
	 */
	public function setCourseId($courseId) {
		$this->courseId = $courseId;
	}

	/**
	 * Set the goal text
	 * 
	 * @param string $text
	 */
	public function setGoalText($text) {
		$this->text = $text;
	}

	/**
	 * Set the user id
	 * 
	 * @param int $userId
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}
	
	/**
	 * Set the create timestamp
	 * 
	 * @param int $timestamp
	 */
	public function setCreateTimestamp($timestamp) {
		$this->createTimestamp = $timestamp;
	}
	
	/**
	 * Set the deactivation timestamp
	 * 
	 * @param int $timestamp
	 */
	public function setDeactivationTimestamp($timestamp) {
		$this->deactivationTimestamp = $timestamp;
	}

	public function getCreateTimestamp() {
		return $this->createTimestamp;
	}

	public function getDeactivationTimestamp() {
		return $this->deactivationTimestamp;
	}
	
	/**
	 * Set the goal db id
	 * 
	 * @param int $goalId
	 */
	public function setGoalId($goalId) {
		$this->goalId = $goalId;
	}

}
