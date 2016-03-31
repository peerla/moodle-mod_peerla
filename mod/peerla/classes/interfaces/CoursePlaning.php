<?php

namespace mod_kom_peerla;

/**
 * The general (pre start) course planing information
 * 
 * @author Christoph Bohr
 */
interface CoursePlaning {
	
	
	/**
	 * Get the user id
	 * 
	 * @return int The user id
	 */
	function getUserId();
	
	/**
	 * Get the course id
	 * 
	 * @return int The course id
	 */
	function getCourseId();
	
	/**
	 * Get the User object
	 * 
	 * @return User User object
	 */
	function getUser();
	
	/**
	 * Get the Course object
	 * 
	 * @return Course Course object
	 */
	function getCourse();
	
	/**
	 * Get the personal goals for this course
	 * 
	 * @return \mod_kom_peerla\CourseGoal[] Array of course goals
	 */
	function getCourseGoals();
	
	/**
	 * Get the useres aspired mark for this course.
	 * 
	 * @return string|null Mark or null, if non is set
	 */
	function getAspiredMark();
	
	
	/**
	 * Returns the topic knowledge goal of the user
	 * 
	 * This will only returns the highest hierarchy level of topics. Each topic
	 * will include the users knowledge goal.
	 * 
	 * @return \mod_kom_peerla\CourseTopicKnowledge[] Array of topics
	 */
	function getRootCourseTopicKnowledgeGoals();
	
	/**
	 * Returns if the plan is still set by the user.
	 * 
	 * If this function returns false, the plan was deleted or replaced by
	 * the user. 
	 * 
	 * @return bool True, if current plan, false otherwise
	 */
	function isActive();
	
	/**
	 * Returns the timestamp of the plan creation.
	 * 
	 * @return int Unix timestamp
	 */
	function getCreateTimestamp();
	
	/**
	 * Returns the timestamp of the plan deactivation.
	 * 
	 * @return int|null Deactivation timestamp
	 */
	function getDeactivationTimestamp();
	
	
	/**
	 * Set the create timestamp
	 * 
	 * @param int $timestamp
	 */
	public function setCreateTimestamp($timestamp);
	
	/**
	 * Set the deactivation timestamp
	 * 
	 * @param int $timestamp
	 */
	public function setDeactivationTimestamp($timestamp);
	
	/**
	 * Set the course goals.
	 * 
	 * @param \mod_kom_peerla\CourseGoal[] $goals
	 */
	public function setCourseGoals(array $goals);
	
	
	/**
	 * Set the aspired mark.
	 * 
	 * @param string $mark
	 */
	public function setAspiredMark($mark);
	
	/**
	 * Returns true, if the user has finished all steps of the course planing.
	 * 
	 * @return bool Course planing finished
	 */
	function userHasFinishedPlaning();
	
	/**
	 * Returns true, if the user has finished the course goal setting step of 
	 * the course planing.
	 * 
	 * @return bool Course goal setting finished
	 */
	function userHasFinishedCourseGoalSetting();
	
	/**
	 * Returns true, if the user has finished the course topic knowledge 
	 * goal setting step of the course planing.
	 * 
	 * @return bool Course knowledge goal setting finished.
	 */
	function userHasFinishedGoalToTopicTransformation();
	
	/**
	 * Returns true, if the user has finished the estimation the pre course 
	 * topic knowledge .
	 * 
	 * @return bool Course knowledge goal setting finished.
	 */
	function userHasFinishedPreCourseKnowledgeSetting();
	
}
