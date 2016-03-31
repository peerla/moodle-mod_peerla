<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface LearningIntervalGoal {
	
	/**
	 * Get the database id of this interval goal.
	 * 
	 * @return int Database id
	 */
	function getGoalId();
	
	/**
	 * Set the learning interval id.
	 * 
	 * @param int $intervalId
	 */
	public function setIntervalId($intervalId);
	
	/**
	 * Get the id of the user who created this goal.
	 * 
	 * @return int User id
	 */
	function getUserId();
	
	/**
	 * Get the database course id.
	 * 
	 * @return int Course id
	 */
	function getCourseId();
	
	/**
	 * Get the db id of the interval.
	 * 
	 * @return int Interval db id
	 */
	function getIntervalId();
	
	/**
	 * Get the action part of the learning goal.
	 * 
	 * @return string Action
	 */
	function getActionId();
	
	/**
	 * Get the topic db id.
	 * 
	 * @return int Topic id
	 */
	function getTopicId();
	
	/**
	 * Get the natural language comment the user entered for this goal.
	 * 
	 * @return string User comment
	 */
	function getGoalComment();
	
	/**
	 * Get the time investment the user originally planed for this goal.
	 * 
	 * @return int Planed time investment
	 */
	function getPlanedTimeInvestment();
	
	/**
	 * Get the day timestamps on which the user planed to learn.
	 * 
	 * @return int[] Array of unix timestamps
	 */
	function getPlanedLearningDays();
	
	/**
	 * Set the day timestamps on which the user learned.
	 * 
	 * @param int[] $timestamps Unix timestamps of the learning days
	 */
	function setLearningDays(array $timestamps);
	
	/**
	 * Get the day timestamps on which the user learned.
	 * 
	 * @return int[] Array of unix timestamps
	 */
	function getLearningDays();
	
	/**
	 * Get the actual time the user invested in this goal.
	 * 
	 * Returns null, if no time has been entered jet.
	 * 
	 * @return int|null Actual time investment
	 */
	function getTimeInvestment();
	
	/**
	 * Returns the status of the goal.
	 * 
	 * Can be one of: "open", "done" or "cancelled"
	 * 
	 * @return string Goal status
	 */
	function getStatus();
	
	/**
	 * Get a string representing the goal.
	 * 
	 * The string includes the end date, action name and topic name.
	 * 
	 * @return string Goal text
	 */
	function getGoalText();
	
	/**
	 * Get a short string representing the goal.
	 * 
	 * The string includes action name and topic name.
	 * 
	 * @return string Goal text
	 */
	function getGoalShortText();
	
	/**
	 * Get the create timestamp for the goal.
	 * 
	 * @return int Timestamp of goal creation
	 */
	function getCreateTimestamp();
	
	/**
	 * Get the timestamp of the last goal (status) update.
	 * 
	 * @return int Timestamp of goal update
	 */
	function getUpdateTimestamp();
	
	/**
	 * Set a new status for the goal.
	 * 
	 * @param string $newStatus The new goal status
	 */
	function setStatus($newStatus);
	
	/**
	 * Set the new actual time investment value.
	 * 
	 * @param int $newTimeInvestment Actual time investment
	 */
	function setTimeInvestment($newTimeInvestment);
	
	/**
	 * Get the course topic for the goal.
	 * 
	 * @return CourseTopic Course topic object
	 */
	function getTopic();
	
	/**
	 * Get the learning interval this goal belongs to.
	 * 
	 * @return LearningInterval Learning interval object
	 */
	function getInterval();
	
	/**
	 * Get the interval goal action object for this goal.
	 * 
	 * @return IntervalGoalAction Interval goal action object
	 */
	function getIntervalGoalAction();
	
	
}
