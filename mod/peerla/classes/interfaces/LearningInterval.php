<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface LearningInterval {
	
	/**
	 * Get the unique interval id.
	 * 
	 * @return int Interval id
	 */
	function getIntervalId();
	
	/**
	 * Get the user id.
	 * 
	 * @return int Db user id
	 */
	function getUserId();
	
	/**
	 * Get the course id.
	 * 
	 * @return int Db course id
	 */
	function getCourseId();
	
	/**
	 * Get the interval start date.
	 * 
	 * @return int Unix timestamp
	 */
	function getStartDate();
	
	/**
	 * Get the interval end date.
	 * 
	 * @return int Unix timestamp
	 */
	function getEndDate();
	
	/**
	 * Get the goals for this interval.
	 * 
	 * @return LearningIntervalGoal[] Interval Goals
	 */
	function getIntervalGoals();
	
	
	/**
	 * Set the user input for the good aspects of the interval
	 * 
	 * @param string $text
	 */
	public function setRetrospectiveGoodText($text);
	
	/**
	 * Get the user input for the good aspects of the interval
	 * 
	 * @return string|null Good Aspects text
	 */
	function getRetrospectiveGoodText();
	
	/**
	 * Set the user input for the bad aspects of the interval
	 * 
	 * @param string $text
	 */
	public function setRetrospectiveBadText($text);
	
	/**
	 * Get the user input for the bad aspects of the interval
	 * 
	 * @return string|null Bad Aspects text
	 */
	function getRetrospectiveBadText();
	
	/**
	 * Returns if the retrospective for this interval has been completed.
	 * 
	 * @return bool True, if completed, false otherwise
	 */
	function retrospectiveDone();
	
	/**
	 * Get the highest level of course topics with the knowledge estimation 
	 * for this interval.
	 * 
	 * @return IntervalCourseTopicKnowledge[] Array of CourseTopics with knowledge estimation
	 */
	function getPreIntervalKnowledge();
	
	/**
	 * Get the next learning interval after this.
	 * 
	 * @return LearningInterval|null The following learning interval
	 */
	function getFollowingInterval();
	
	/**
	 * Get the learning interval prior to this.
	 * 
	 * @return LearningInterval|null The previous learning interval
	 */
	function getPreviousInterval();
	
	/**
	 * Get all distinct topics, that are referenced by the interval goals for 
	 * this interval.
	 * 
	 * @return IntervalCourseTopicKnowledge[] Array of CourseTopics with knowledge estimation
	 */
	function getGoalTopicKnowledge();
	
	/**
	 * Returns true, if the user has finished all steps of the interval planing.
	 * 
	 * @return bool Course planing finished
	 */
	function userHasFinishedPlaning();
	
	/**
	 * Returns true, if the user has finished the knowledge estimation step of 
	 * the interval planing.
	 * 
	 * @return bool Course goal setting finished
	 */
	function userHasFinishedKnowledgeEstimation();
	
	/**
	 * Returns true, if the user has finished the interval goal setting step of 
	 * the interval planing.
	 * 
	 * @return bool Course goal setting finished
	 */
	function userHasFinishedGoalSetting();
	
	/**
	 * Returns true, if this interval is currently active (current time in interval).
	 * 
	 * @return bool True, if it is active
	 */
	function isRunning();
	
}
