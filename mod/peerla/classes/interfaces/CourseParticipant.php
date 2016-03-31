<?php

namespace mod_kom_peerla;

/**
 *
 * @author Christoph Bohr
 */
interface CourseParticipant {
	
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
	 * Returns all topics for this course and user. 
	 * 
	 * This will only returns the highest hierarchy level of topics.
	 * 
	 * @return \mod_kom_peerla\CourseTopic[] Array of topics
	 */
	function getRootCourseTopics();
	
	/**
	 * Get all learning intervals of the current user and course.
	 * 
	 * @return \mod_kom_peerla\LearningInterval[] Array of learning intervals
	 */
	function getLearningIntervals();
	
	/**
	 * Get the currently active learning interval for the course.
	 * 
	 * @return \mod_kom_peerla\LearningInterval|null Learning interval
	 */
	function getCurrentLearningInterval();
	
	/**
	 * Get the course planing object for this user and cours.
	 * 
	 * Retruns null, if the planing has not been finished by the user.
	 * 
	 * @return \mod_kom_peerla\CoursePlaning|null Users course planing
	 */
	function getCoursePlaning();
	
	/**
	 * Get the current course topic knowledge of the participant.
	 * 
	 * If the participant already planed intervals, the knowledge estimations 
	 * of the most current interval will be returned. If no intervals were
	 * planed, all course topics will be returned, each having a knowledge of 0.
	 * Only the highest hierarchy level of topics will be returned.
	 * 
	 * @return IntervalCourseTopicKnowledge[] Topic knowledge estimations
	 */
	function getCurrentCourseKnowledge();
}
