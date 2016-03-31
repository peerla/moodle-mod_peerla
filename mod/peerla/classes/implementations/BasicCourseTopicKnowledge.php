<?php

namespace mod_kom_peerla;

require_once realpath(__DIR__).'/../interfaces/CourseTopicKnowledge.php';
require_once realpath(__DIR__).'/BasicCourseTopic.php';

/**
 * Description of BasicEstimatedCourseTopic
 *
 * @author Christoph Bohr
 */
class BasicCourseTopicKnowledge extends BasicCourseTopic implements CourseTopicKnowledge {
	
	protected $estimation;
	protected $estimationUserId;
	protected $estimationTimestamp;

	/**
	 * Set the knownledge estimation for this topic.
	 * 
	 * @param int $estimation Estimation of the topic
	 */
	public function setEstimation($estimation){
		$this->estimation = $estimation;
	}
	
	public function getEstimation() {
		return $this->estimation;
	}

	public function getEstimationUserId() {
		return $this->estimationUserId;
	}
	
	/**
	 * Set the user id of the user who estimated this topic.
	 * 
	 * @param int $userId User id
	 */
	public function setEstimationUserId($userId){
		$this->estimationUserId = $userId;
	}
	
	/**
	 * Set the timestamp of the knowledge estimation.
	 * 
	 * @param int $timestamp Unix timestamp
	 */
	public function setEstimationTimestamp($timestamp){
		$this->estimationTimestamp = $timestamp;
	}

	public function getEstimationTimestamp() {
		return $this->estimationTimestamp;
	}

}
